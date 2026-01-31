/**
 * Main JavaScript File
 * Demonstrates: Events, Interactive Features
 */
// Wrap everything in an IIFE to avoid top-level lexical declarations
(function () {
    if (window.__techstore_main_loaded) {
        console.warn('assets/js/main.js already loaded, skipping duplicate execution.');
        return;
    }
    window.__techstore_main_loaded = true;

    // Slider functionality (use unique names to avoid collisions)
    let __ts_currentSlide = 0;
    let __ts_slides = [];
    let __ts_dots = [];
    let __ts_slideInterval = null;

    function showSlide(n) {
    if (!__ts_slides || __ts_slides.length === 0) return;

    if (n >= __ts_slides.length) {
        __ts_currentSlide = 0;
    } else if (n < 0) {
        __ts_currentSlide = __ts_slides.length - 1;
    } else {
        __ts_currentSlide = n;
    }

    __ts_slides.forEach(slide => slide.classList.remove('active'));
    __ts_dots.forEach(dot => dot.classList.remove('active'));

    if (__ts_slides[__ts_currentSlide]) __ts_slides[__ts_currentSlide].classList.add('active');
    if (__ts_dots[__ts_currentSlide]) __ts_dots[__ts_currentSlide].classList.add('active');
}

function changeSlide(n) {
    showSlide(__ts_currentSlide + n);
}

function goToSlide(n) {
    showSlide(n - 1);
}

function initSlider() {
    __ts_slides = Array.from(document.querySelectorAll('.slide'));
    __ts_dots = Array.from(document.querySelectorAll('.dot'));

    // Expose for inline handlers as a fallback
    window.changeSlide = changeSlide;
    window.goToSlide = goToSlide;

    // Attach arrow button handlers (prefer event listeners over inline)
    const prevBtn = document.querySelector('.slider-btn.prev');
    const nextBtn = document.querySelector('.slider-btn.next');
    if (prevBtn) prevBtn.addEventListener('click', () => changeSlide(-1));
    if (nextBtn) nextBtn.addEventListener('click', () => changeSlide(1));

    // Attach dot handlers
    __ts_dots.forEach((dot, idx) => {
        dot.addEventListener('click', () => goToSlide(idx + 1));
    });

    // Initialize state

    showSlide(0);

    // Auto-advance
    if (__ts_slideInterval) clearInterval(__ts_slideInterval);
    __ts_slideInterval = setInterval(() => changeSlide(1), 5000);

    // Pause on hover
    const slider = document.querySelector('.hero-slider');
    if (slider) {
        slider.addEventListener('mouseenter', () => {
            clearInterval(__ts_slideInterval);
        });
        slider.addEventListener('mouseleave', () => {
            __ts_slideInterval = setInterval(() => changeSlide(1), 5000);
        });
    }
}


    document.addEventListener('DOMContentLoaded', initSlider);

    // Add to cart function
    function addToCart(productId, productName, price) {
        // Create form data
        const formData = new FormData();
        formData.append('action', 'add');
        formData.append('product_id', productId);
        formData.append('product_name', productName);
        formData.append('price', price);
        formData.append('quantity', 1);
        
        // Send AJAX request
        const basePath = (typeof window.TECHSTORE_BASE !== 'undefined' ? window.TECHSTORE_BASE : '');
        fetch((basePath || '') + 'add_to_cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update cart count
                const cartCount = document.getElementById('cart-count');
                if (cartCount) {
                    cartCount.textContent = data.cartCount;
                }
                
                // Show notification
                showNotification('Product added to cart!', 'success');
            } else {
                showNotification('Error adding product to cart', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error adding product to cart', 'error');
        });
    }

    // Quick view function
    function quickView(productId) {
        // In a real application, this would fetch product details via AJAX
        window.location.href = `product_detail.php?id=${productId}`;
    }

    // Show notification
    function showNotification(message, type = 'success') {
        // Remove existing notification
        const existing = document.querySelector('.notification');
        if (existing) {
            existing.remove();
        }
        
        // Create notification
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            padding: 1rem 2rem;
            background: ${type === 'success' ? '#10b981' : '#ef4444'};
            color: white;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 10000;
            animation: slideIn 0.3s ease-out;
        `;
        
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }

    // Toggle wishlist
    function toggleWishlist(productId, btn) {
        const formData = new FormData();
        formData.append('action', 'toggle');
        formData.append('product_id', productId);
        
        const basePath = (typeof window.TECHSTORE_BASE !== 'undefined' ? window.TECHSTORE_BASE : '');
        
        fetch((basePath || '') + 'add_to_wishlist.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (btn) {
                    btn.classList.toggle('in-wishlist', data.inWishlist);
                    const icon = btn.querySelector('i.fa-heart');
                    if (icon) {
                        icon.className = (data.inWishlist ? 'fa-solid' : 'fa-regular') + ' fa-heart';
                        btn.title = data.inWishlist ? 'Remove from wishlist' : 'Add to wishlist';
                    }
                    const textSpan = btn.querySelector('span, .btn-text');
                    if (textSpan) textSpan.textContent = data.inWishlist ? 'In Wishlist' : 'Add to Wishlist';
                }
                showNotification(data.message, 'success');
                // Update wishlist count in nav if present
                const wishlistNav = document.querySelector('a[href*="tab=wishlist"]');
                if (wishlistNav && data.wishlistCount !== undefined) {
                    const match = wishlistNav.textContent.match(/Wishlist\s*(?:\((\d+)\))?/);
                    wishlistNav.innerHTML = '<i class="fas fa-heart"></i> Wishlist' + (data.wishlistCount > 0 ? ' (' + data.wishlistCount + ')' : '');
                }
            } else {
                showNotification(data.message || 'Error', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            showNotification('Error updating wishlist', 'error');
        });
    }

    // Expose functions to window for inline handlers
    window.addToCart = addToCart;
    window.quickView = quickView;
    window.showNotification = showNotification;
    window.toggleWishlist = toggleWishlist;

    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.style.borderColor = '#ef4444';
                    } else {
                        field.style.borderColor = '';
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    showNotification('Please fill in all required fields', 'error');
                }
            });
        });
        
        // Remove error styling on input
        const inputs = document.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                this.style.borderColor = '';
            });
        });
    });

    // Search functionality
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.closest('form').submit();
            }
        });
    }

    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);

})();
