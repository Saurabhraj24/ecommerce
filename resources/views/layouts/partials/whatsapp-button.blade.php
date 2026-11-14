@php
    $whatsappNumber = preg_replace('/[^0-9]/', '', \App\Models\Setting::get('store_whatsapp', '919876543210'));
    $shopName = \App\Models\Setting::get('store_name', 'Bombay Fashion');
@endphp

<div class="fixed bottom-20 md:bottom-6 right-4 md:right-6 z-40 group">
    <a href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode('Hello ' . $shopName . ', I am interested in shopping from your Amritsar store. Could you please assist me?') }}"
       target="_blank"
       rel="noopener noreferrer"
       class="flex items-center space-x-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white p-3 sm:px-4 sm:py-3 rounded-full shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition duration-300 border border-white/40"
       aria-label="Chat with Bombay Fashion on WhatsApp">
        <svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 24 24">
            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.694.06-2.124-.531-1.833-.759-3.003-2.614-3.094-2.735-.09-.122-.74-1.02-.74-1.944 0-.924.475-1.378.644-1.567.169-.189.37-.236.493-.236.123 0 .247.001.355.006.113.005.265-.043.414.316.154.372.527 1.284.573 1.377.046.094.077.204.015.328-.061.123-.092.201-.184.308-.093.108-.195.241-.278.324-.093.093-.19.194-.082.38.109.186.482.795 1.033 1.286.709.632 1.307.827 1.492.919.185.093.293.078.401-.047.108-.124.462-.538.585-.723.123-.186.246-.154.415-.093.17.062 1.077.508 1.262.601.185.092.308.138.354.216.046.077.046.447-.098.852zM12 2C6.477 2 2 6.477 2 12c0 1.891.524 3.662 1.436 5.176L2 22l4.982-1.393C8.441 21.493 10.165 22 12 22c5.523 0 10-4.477 10-10S17.523 2 12 2zm0 18.25c-1.637 0-3.15-.521-4.39-1.411l-.315-.226-2.955.826.837-2.887-.247-.336C3.962 14.939 3.438 13.513 3.438 12 3.438 7.279 7.279 3.438 12 3.438c4.721 0 8.562 3.841 8.562 8.562 0 4.721-3.841 8.25-8.562 8.25z"/>
        </svg>
        <span class="text-xs font-semibold tracking-wide hidden sm:inline">WhatsApp Chat</span>
    </a>
</div>
