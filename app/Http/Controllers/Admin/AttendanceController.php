<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Show Attendance Dashboard with today's records and date filtering.
     */
    public function index(Request $request): View
    {
        $selectedDate = $request->input('date', date('Y-m-d'));

        // All active staff & admin members
        $staffRole = Role::where('name', Role::STAFF)->first();
        $adminRole = Role::where('name', Role::ADMIN)->first();
        $staffMembers = User::whereIn('role_id', [$staffRole?->id, $adminRole?->id])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Today's attendance keyed by user_id
        $attendances = Attendance::where('date', $selectedDate)
            ->with('user')
            ->get()
            ->keyBy('user_id');

        $totalStaff = $staffMembers->count();
        $presentCount = $attendances->whereIn('status', ['present', 'late', 'half_day'])->count();
        $lateCount = $attendances->where('status', 'late')->count();
        $absentCount = max(0, $totalStaff - $presentCount);

        return view('admin.attendance.index', compact(
            'staffMembers',
            'attendances',
            'selectedDate',
            'totalStaff',
            'presentCount',
            'lateCount',
            'absentCount'
        ));
    }

    /**
     * Fullscreen Dynamic QR Kiosk Display for Showroom Reception / Counter.
     */
    public function kiosk(): View
    {
        $tokenData = $this->generateCurrentDynamicToken();

        // Recent checkins today
        $todayCheckins = Attendance::where('date', date('Y-m-d'))
            ->with('user')
            ->latest('updated_at')
            ->take(8)
            ->get();

        $recentCheckinsPayload = $todayCheckins->map(function ($a) {
            return [
                'name' => $a->user->name,
                'avatar' => $a->user->avatar_url,
                'designation' => $a->user->designation ?: 'Staff Member',
                'punch_in' => $a->punch_in_at ? Carbon::parse($a->punch_in_at)->format('h:i A') : null,
                'punch_out' => $a->punch_out_at ? Carbon::parse($a->punch_out_at)->format('h:i A') : null,
                'status' => ucfirst($a->status),
            ];
        })->values()->toArray();

        return view('admin.attendance.kiosk', compact('tokenData', 'todayCheckins', 'recentCheckinsPayload'));
    }

    /**
     * API endpoint to get the fresh 20-second dynamic QR token.
     */
    public function getLiveToken(): JsonResponse
    {
        $tokenData = $this->generateCurrentDynamicToken();

        $recentCheckins = Attendance::where('date', date('Y-m-d'))
            ->with('user')
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(function ($a) {
                return [
                    'name' => $a->user->name,
                    'avatar' => $a->user->avatar_url,
                    'designation' => $a->user->designation ?: 'Staff Member',
                    'punch_in' => $a->punch_in_at ? Carbon::parse($a->punch_in_at)->format('h:i A') : null,
                    'punch_out' => $a->punch_out_at ? Carbon::parse($a->punch_out_at)->format('h:i A') : null,
                    'status' => ucfirst($a->status),
                ];
            });

        return response()->json([
            'success' => true,
            'token' => $tokenData['token'],
            'qr_payload' => $tokenData['scan_url'],
            'qr_image_url' => $tokenData['qr_image_url'],
            'seconds_remaining' => $tokenData['seconds_remaining'],
            'window' => $tokenData['window'],
            'generated_at' => $tokenData['time'],
            'recent_checkins' => $recentCheckins,
        ]);
    }

    /**
     * Staff Scanning Landing Page & Auto-Punch.
     */
    public function scan(Request $request): View|Response|RedirectResponse
    {
        $token = $request->query('token');

        if (!$token) {
            return view('attendance.scan-result', [
                'success' => false,
                'message' => 'No attendance token provided in QR scan. Please scan the live QR code on the showroom counter.',
            ]);
        }

        // Validate token against 20-second rotating window (current or previous window)
        $validation = $this->verifyDynamicToken($token);

        if (!$validation['valid']) {
            return view('attendance.scan-result', [
                'success' => false,
                'message' => 'This QR code has expired (QR changes every 20 seconds). Please scan the live QR code currently displayed on the showroom screen.',
            ]);
        }

        $deviceId = $this->resolveDeviceId($request);

        // If staff is already logged in on mobile browser
        if (Auth::check()) {
            return $this->processPunch(Auth::user(), $request->ip(), $deviceId);
        }

        // If staff is not logged in on mobile browser, present quick staff identity selector / PIN check
        $staffRole = Role::where('name', Role::STAFF)->first();
        $adminRole = Role::where('name', Role::ADMIN)->first();
        $staffMembers = User::whereIn('role_id', [$staffRole?->id, $adminRole?->id])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()
            ->view('attendance.verify-staff', [
                'token' => $token,
                'deviceId' => $deviceId,
                'staffMembers' => $staffMembers,
            ])
            ->withCookie(cookie()->forever('bf_attendance_device_token', $deviceId));
    }

    /**
     * Submit Punch for unauthenticated or PIN-confirmed staff.
     */
    public function submitPunch(Request $request): View|Response
    {
        $request->validate([
            'token' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'phone_last4' => 'required|string|size:4',
            'client_device_id' => 'nullable|string|max:64',
        ]);

        $validation = $this->verifyDynamicToken($request->input('token'));
        if (!$validation['valid']) {
            return view('attendance.scan-result', [
                'success' => false,
                'message' => 'The 20-second QR token expired while confirming. Please re-scan the live counter screen.',
            ]);
        }

        $user = User::findOrFail($request->integer('user_id'));

        // Verify phone last 4 digits as quick proof of possession
        $userPhone = preg_replace('/\D/', '', $user->phone ?? '');
        $expectedLast4 = substr($userPhone, -4);

        if ($expectedLast4 && $expectedLast4 !== $request->input('phone_last4')) {
            return view('attendance.scan-result', [
                'success' => false,
                'message' => 'Verification failed: The last 4 digits of your phone number did not match our records for ' . $user->name . '.',
            ]);
        }

        $deviceId = $this->resolveDeviceId($request);

        return $this->processPunch($user, $request->ip(), $deviceId);
    }

    /**
     * Manual Attendance Update by Store Owner.
     */
    public function updateManual(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'status' => 'required|in:present,late,half_day,absent,leave',
            'punch_in_at' => 'nullable',
            'punch_out_at' => 'nullable',
            'notes' => 'nullable|string|max:255',
        ]);

        $attendance = Attendance::firstOrNew([
            'user_id' => $validated['user_id'],
            'date' => $validated['date'],
        ]);

        $attendance->status = $validated['status'];
        $attendance->punch_in_at = $validated['punch_in_at'] ?: null;
        $attendance->punch_out_at = $validated['punch_out_at'] ?: null;
        $attendance->verification_method = 'manual_admin';
        $attendance->notes = $validated['notes'] ?? 'Updated manually by admin';
        $attendance->calculateTotalHours();
        $attendance->save();

        return back()->with('success', 'Attendance record updated successfully.');
    }

    /**
     * Resolve mobile device identifier to enforce 1 mobile = 1 staff punch policy.
     */
    protected function resolveDeviceId(Request $request): string
    {
        $deviceId = $request->cookie('bf_attendance_device_token');

        if ($deviceId) {
            try {
                $decrypted = \Illuminate\Support\Facades\Crypt::decrypt($deviceId, false);
                if (is_string($decrypted) && str_contains($decrypted, '|')) {
                    $parts = explode('|', $decrypted, 2);
                    $deviceId = $parts[1];
                } elseif (is_string($decrypted)) {
                    $deviceId = $decrypted;
                }
            } catch (\Throwable $e) {
                // Already plaintext
            }
        }

        if (!$deviceId) {
            $deviceId = $request->input('client_device_id');
        }

        if (!$deviceId || strlen($deviceId) < 8) {
            $deviceId = 'mob_' . bin2hex(random_bytes(16));
        }

        return substr($deviceId, 0, 64);
    }

    /**
     * Process Punch In or Punch Out for a verified staff user.
     * Enforces single mobile device restriction ("ek mobile se ek hi scan ho").
     */
    protected function processPunch(User $user, ?string $ip, string $deviceId): Response
    {
        $today = date('Y-m-d');
        $nowTime = Carbon::now('Asia/Kolkata');
        $timeString = $nowTime->format('H:i:s');
        $formattedTime = $nowTime->format('h:i A');

        // Check 1: "ek mobile se ek hi scan ho" -> Has this device already recorded attendance today for another person?
        $conflictDevice = Attendance::whereDate('date', $today)
            ->where('device_id', $deviceId)
            ->where('user_id', '!=', $user->id)
            ->with('user')
            ->first();

        if ($conflictDevice) {
            return response()
                ->view('attendance.scan-result', [
                    'success' => false,
                    'device_blocked' => true,
                    'message' => '⚠️ Device Restriction: This mobile device has already recorded attendance today for another staff member (' . $conflictDevice->user->name . '). At Bombay Fashion, each employee must scan from their own mobile phone to prevent proxy attendance.',
                ])
                ->withCookie(cookie()->forever('bf_attendance_device_token', $deviceId));
        }

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            $attendance = new Attendance([
                'user_id' => $user->id,
                'date' => $today,
            ]);
        }

        $actionType = '';
        $message = '';

        // If no punch yet -> PUNCH IN
        if (!$attendance->exists || !$attendance->punch_in_at) {
            $attendance->punch_in_at = $timeString;
            $attendance->punch_in_ip = $ip;
            $attendance->device_id = $deviceId;
            $attendance->verification_method = 'dynamic_qr';

            // Showroom opens 10:00 AM, grace till 10:30 AM
            $cutoff = Carbon::today('Asia/Kolkata')->setTime(10, 30, 0);
            if ($nowTime->greaterThan($cutoff)) {
                $attendance->status = 'late';
                $message = "Punch In Recorded at {$formattedTime}. Marked as Late Arrival (Showroom reporting cutoff is 10:30 AM).";
            } else {
                $attendance->status = 'present';
                $message = "Namaste {$user->name}! Punch In successfully recorded on time at {$formattedTime}. Have a productive day at Bombay Fashion!";
            }

            $attendance->save();
            $actionType = 'PUNCH_IN';
        } 
        // If already punched in and no punch out -> PUNCH OUT
        elseif (!$attendance->punch_out_at) {
            // Prevent accidental double-scanning within 2 minutes of Punch In
            $inTime = Carbon::parse($today . ' ' . $attendance->punch_in_at);
            if ($nowTime->diffInMinutes($inTime) < 2) {
                return response()
                    ->view('attendance.scan-result', [
                        'success' => true,
                        'already_done' => true,
                        'user' => $user,
                        'attendance' => $attendance,
                        'message' => "Punch In was just recorded at {$formattedTime}. You do not need to scan again right now. Punch Out will be recorded at the end of your shift.",
                    ])
                    ->withCookie(cookie()->forever('bf_attendance_device_token', $deviceId));
            }

            $attendance->punch_out_at = $timeString;
            $attendance->punch_out_ip = $ip;
            $attendance->device_id = $deviceId;
            $attendance->calculateTotalHours();
            $attendance->save();

            $actionType = 'PUNCH_OUT';
            $message = "Punch Out Recorded at {$formattedTime}. Total shift: {$attendance->total_hours} hours. Thank you for your service today!";
        } 
        // Already punched out
        else {
            return response()
                ->view('attendance.scan-result', [
                    'success' => true,
                    'already_done' => true,
                    'user' => $user,
                    'attendance' => $attendance,
                    'message' => "You have already completed both Punch In ({$attendance->punch_in_at}) and Punch Out ({$attendance->punch_out_at}) for today on this mobile device.",
                ])
                ->withCookie(cookie()->forever('bf_attendance_device_token', $deviceId));
        }

        return response()
            ->view('attendance.scan-result', [
                'success' => true,
                'user' => $user,
                'attendance' => $attendance,
                'action_type' => $actionType,
                'formatted_time' => $formattedTime,
                'message' => $message,
            ])
            ->withCookie(cookie()->forever('bf_attendance_device_token', $deviceId));
    }

    /**
     * Generate 20-second dynamic rotating token with admin email, date, time, year, and window index.
     */
    protected function generateCurrentDynamicToken(): array
    {
        $admin = User::whereHas('role', fn($q) => $q->where('name', Role::ADMIN))->first();
        $adminEmail = $admin?->email ?? 'admin@bombayfashion.com';

        $currentTime = time();
        $windowSeconds = 20;
        $windowIndex = floor($currentTime / $windowSeconds);
        $secondsRemaining = $windowSeconds - ($currentTime % $windowSeconds);

        $now = Carbon::now('Asia/Kolkata');
        $date = $now->format('Y-m-d');
        $year = $now->format('Y');
        $time = $now->format('H:i:s');

        // Create HMAC signature
        $secretKey = config('app.key', 'BombayFashionAmritsarSecretKey2026');
        $dataToSign = "email:{$adminEmail}|date:{$date}|year:{$year}|win:{$windowIndex}";
        $signature = hash_hmac('sha256', $dataToSign, $secretKey);

        $payload = [
            'email' => $adminEmail,
            'date' => $date,
            'year' => $year,
            'time' => $time,
            'win' => $windowIndex,
            'sig' => substr($signature, 0, 16),
        ];

        $token = base64_encode(json_encode($payload));
        $scanUrl = route('attendance.scan', ['token' => $token]);

        // Generate reliable SVG QR code via public QR renderer
        $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=320x320&margin=10&color=3b2a3e&bgcolor=fff9f3&data=' . urlencode($scanUrl);

        return [
            'token' => $token,
            'scan_url' => $scanUrl,
            'qr_image_url' => $qrImageUrl,
            'window' => $windowIndex,
            'seconds_remaining' => $secondsRemaining,
            'time' => $time,
            'date' => $date,
        ];
    }

    /**
     * Verify dynamic token with a 1-window grace period (±20s).
     */
    protected function verifyDynamicToken(string $token): array
    {
        try {
            $json = base64_decode($token, true);
            if (!$json) {
                return ['valid' => false, 'error' => 'Invalid base64 encoding'];
            }

            $payload = json_decode($json, true);
            if (!isset($payload['email'], $payload['date'], $payload['year'], $payload['win'], $payload['sig'])) {
                return ['valid' => false, 'error' => 'Malformed token payload'];
            }

            $currentWindow = (int)floor(time() / 20);
            $tokenWindow = (int)$payload['win'];

            // Allow current window or previous window (max 40s total validity)
            if ($tokenWindow !== $currentWindow && $tokenWindow !== ($currentWindow - 1)) {
                return ['valid' => false, 'error' => 'Token expired (window mismatch)'];
            }

            // Verify signature
            $secretKey = config('app.key', 'BombayFashionAmritsarSecretKey2026');
            $expectedData = "email:{$payload['email']}|date:{$payload['date']}|year:{$payload['year']}|win:{$tokenWindow}";
            $expectedSig = substr(hash_hmac('sha256', $expectedData, $secretKey), 0, 16);

            if (!hash_equals($expectedSig, $payload['sig'])) {
                return ['valid' => false, 'error' => 'Tampered QR signature'];
            }

            return ['valid' => true, 'payload' => $payload];
        } catch (\Throwable $e) {
            return ['valid' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Staff Self-Service Attendance & In-Browser QR Scanner Portal.
     * Accessible by all staff & admin members.
     */
    public function myAttendance(Request $request): View
    {
        $user = Auth::user();
        $today = date('Y-m-d');

        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $recentAttendances = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->take(30)
            ->get();

        $monthPresent = Attendance::where('user_id', $user->id)
            ->whereYear('date', date('Y'))
            ->whereMonth('date', date('m'))
            ->whereIn('status', ['present', 'late', 'half_day'])
            ->count();

        $monthHours = Attendance::where('user_id', $user->id)
            ->whereYear('date', date('Y'))
            ->whereMonth('date', date('m'))
            ->sum('total_hours');

        return view('admin.attendance.mark', compact(
            'user',
            'todayAttendance',
            'recentAttendances',
            'monthPresent',
            'monthHours'
        ));
    }
}
