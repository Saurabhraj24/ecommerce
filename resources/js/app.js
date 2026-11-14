import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Global helper for toast notifications
window.showToast = function (message, type = 'success') {
    window.dispatchEvent(new CustomEvent('notify', {
        detail: { message, type }
    }));
};

// Global wishlist toggle helper
window.toggleWishlist = async function (productId, buttonElement) {
    try {
        const response = await fetch(`/wishlist/toggle/${productId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (response.status === 401) {
            window.location.href = '/login';
            return;
        }

        const data = await response.json();
        if (data.status === 'added') {
            window.showToast(data.message || 'Added to wishlist!', 'success');
            if (buttonElement) {
                buttonElement.classList.add('text-primary-500', 'fill-current');
                buttonElement.classList.remove('text-gray-400');
            }
        } else if (data.status === 'removed') {
            window.showToast(data.message || 'Removed from wishlist', 'info');
            if (buttonElement) {
                buttonElement.classList.remove('text-primary-500', 'fill-current');
                buttonElement.classList.add('text-gray-400');
            }
        }

        // Update wishlist counter badge if present
        const badge = document.getElementById('wishlist-count-badge');
        if (badge && data.count !== undefined) {
            badge.innerText = data.count;
            badge.style.display = data.count > 0 ? 'inline-flex' : 'none';
        }
    } catch (err) {
        console.error('Wishlist error:', err);
    }
};

Alpine.start();
