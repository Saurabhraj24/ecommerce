<div x-data="{ 
        notifications: [],
        add(message, type = 'success') {
            const id = Date.now();
            this.notifications.push({ id, message, type });
            setTimeout(() => {
                this.remove(id);
            }, 4500);
        },
        remove(id) {
            this.notifications = this.notifications.filter(n => n.id !== id);
        }
     }"
     @notify.window="add($event.detail.message, $event.detail.type)"
     class="fixed bottom-6 right-6 z-50 flex flex-col space-y-3 pointer-events-none max-w-sm w-full">
    
    <template x-for="n in notifications" :key="n.id">
        <div x-show="true"
             x-transition:enter="transform ease-out duration-300 transition"
             x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
             x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="pointer-events-auto rounded-2xl p-4 shadow-xl border glass-modal flex items-start space-x-3 text-sm font-medium"
             :class="{
                'border-primary-200 text-plum bg-white/95': n.type === 'success',
                'border-rose-300 text-rose-800 bg-rose-50/95': n.type === 'error',
                'border-gold-300 text-gold-800 bg-gold-50/95': n.type === 'info'
             }">
            <div class="shrink-0 mt-0.5">
                <template x-if="n.type === 'success'">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-primary-100 text-primary-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </span>
                </template>
                <template x-if="n.type === 'error'">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-rose-100 text-rose-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </span>
                </template>
                <template x-if="n.type === 'info'">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gold-100 text-gold-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                </template>
            </div>
            <div class="flex-1" x-text="n.message"></div>
            <button @click="remove(n.id)" class="shrink-0 text-plum/40 hover:text-plum transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </template>
</div>

{{-- Trigger flash messages from session --}}
@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.showToast(@json(session('success')), 'success');
        });
    </script>
@endif

@if (session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.showToast(@json(session('error')), 'error');
        });
    </script>
@endif

@if (session('info'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.showToast(@json(session('info')), 'info');
        });
    </script>
@endif
