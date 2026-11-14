<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SchoolUniform;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolUniformController extends Controller
{
    public function index(Request $request): View
    {
        // Get all unique schools with uniform items from DB
        $schools = SchoolUniform::distinct()
            ->pluck('school_name')
            ->filter()
            ->sort()
            ->values();

        $selectedSchool = $request->input('school');
        $selectedClass = $request->input('class');
        $selectedGender = $request->input('gender', 'all');

        $classes = collect();
        $uniformItems = collect();

        if ($selectedSchool) {
            $classes = SchoolUniform::where('school_name', $selectedSchool)
                ->distinct()
                ->pluck('class_grade')
                ->filter()
                ->values();
        }

        $uniformProducts = Product::whereHas('schoolUniform', function ($q) use ($selectedSchool, $selectedClass, $selectedGender) {
            if ($selectedSchool) {
                $q->where('school_name', $selectedSchool);
            }
            if ($selectedClass) {
                $q->where('class_grade', $selectedClass);
            }
            if ($selectedGender && $selectedGender !== 'all') {
                $q->whereIn('gender', [$selectedGender, 'unisex']);
            }
        })
        ->active()
        ->with(['category', 'primaryImage', 'images', 'variants', 'schoolUniform'])
        ->paginate(12)
        ->withQueryString();

        return view('uniforms.index', compact(
            'schools',
            'selectedSchool',
            'selectedClass',
            'selectedGender',
            'classes',
            'uniformProducts'
        ));
    }
}
