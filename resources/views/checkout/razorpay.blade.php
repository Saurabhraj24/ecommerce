@extends('layouts.app')

@section('title', 'Complete Payment - Order #' . $order->order_number)

@section('content')
<div class="py-12 bg-festive-ivory/50 min-h-[70vh] flex items-center justify-center">
    <div class="max-w-md w-full mx-auto px-4">
        
        <div class="glass-card rounded-3xl p-8 border border-white/80 bg-white/90 shadow-xl text-center">
            
            <div class="w-16 h-16 rounded-full bg-festive-blush mx-auto flex items-center justify-center text-festive-rose mb-4 animate-pulse">
                <svg class="w-8 h-8 stroke-current" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>

            <h1 class="text-2xl font-serif font-bold text-festive-plum mb-1">
                Complete Online Payment
            </h1>
            <p class="text-xs text-festive-plum/60 mb-6">
                Order <span class="font-bold font-mono">#{{ $order->order_number }}</span> • Bombay Fashion
            </p>

            <div class="bg-festive-blush/30 rounded-2xl p-4 mb-6 border border-festive-plum/5">
                <span class="text-xs text-festive-plum/70">Payable Amount:</span>
                <div class="text-3xl font-serif font-bold text-festive-rose mt-0.5">
                    ₹{{ number_format($order->grand_total, 2) }}
                </div>
            </div>

            <p class="text-xs text-festive-plum/70 mb-6 leading-relaxed">
                Click below to launch the Razorpay secure gateway to pay via UPI (GPay, PhonePe, Paytm), Cards, or Netbanking.
            </p>

            {{-- Razorpay Button --}}
            <button 
                type="button" 
                id="rzp-button"
                class="w-full py-3.5 px-6 rounded-xl font-bold text-sm bg-festive-rose text-white shadow-lg shadow-festive-rose/25 hover:bg-festive-rose/90 transition-all flex items-center justify-center gap-2"
            >
                <span>Pay ₹{{ number_format($order->grand_total, 2) }} Now</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>

            <div class="mt-4">
                <a href="{{ route('checkout.index') }}" class="text-xs text-festive-plum/50 hover:text-festive-rose underline">
                    Cancel and change payment method
                </a>
            </div>

            {{-- Hidden verification form --}}
            <form action="{{ route('checkout.razorpay.verify') }}" method="POST" id="razorpayForm" class="hidden">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <input type="hidden" name="razorpay_payment_id" id="rzp_payment_id">
                <input type="hidden" name="razorpay_order_id" id="rzp_order_id">
                <input type="hidden" name="razorpay_signature" id="rzp_signature">
            </form>

        </div>

    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var options = {
            "key": "{{ $keyId }}",
            "amount": "{{ (int)($order->grand_total * 100) }}",
            "currency": "{{ $currency }}",
            "name": "Bombay Fashion",
            "description": "Payment for Order #{{ $order->order_number }}",
            "image": "{{ asset('images/logo.png') }}",
            "order_id": "{{ $razorpayOrder['id'] ?? '' }}",
            "handler": function (response) {
                document.getElementById('rzp_payment_id').value = response.razorpay_payment_id;
                document.getElementById('rzp_order_id').value = response.razorpay_order_id;
                document.getElementById('rzp_signature').value = response.razorpay_signature;
                document.getElementById('razorpayForm').submit();
            },
            "prefill": {
                "name": "{{ $user->name ?? $order->shipping_name }}",
                "email": "{{ $user->email ?? $order->shipping_email }}",
                "contact": "{{ $user->phone ?? $order->shipping_phone }}"
            },
            "theme": {
                "color": "#C2185B"
            },
            "modal": {
                "ondismiss": function() {
                    console.log('Payment modal closed by user');
                }
            }
        };

        var rzp = new Razorpay(options);

        document.getElementById('rzp-button').onclick = function(e){
            rzp.open();
            e.preventDefault();
        };

        // Auto open on page load
        setTimeout(function() {
            rzp.open();
        }, 600);
    });
</script>
@endsection
