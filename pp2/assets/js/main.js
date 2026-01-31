(function () {
    if (window.__techstore_main_loaded) {
        console.warn('assets/js/main.js already loaded, skipping duplicate execution.');
        return;
    }
    window.__techstore_main_loaded = true;

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

    window.changeSlide = changeSlide;
    window.goToSlide = goToSlide;

    const prevBtn = document.querySelector('.slider-btn.prev');
    const nextBtn = document.querySelector('.slider-btn.next');
    if (prevBtn) prevBtn.addEventListener('click', () => changeSlide(-1));
    if (nextBtn) nextBtn.addEventListener('click', () => changeSlide(1));

    __ts_dots.forEach((dot, idx) => {
        dot.addEventListener('click', () => goToSlide(idx + 1));
    });

    showSlide(0);

    if (__ts_slideInterval) clearInterval(__ts_slideInterval);
    __ts_slideInterval = setInterval(() => changeSlide(1), 5000);

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

    function addToCart(productId, productName, price) {
        const formData = new FormData();
        formData.append('action', 'add');
        formData.append('product_id', productId);
        formData.append('product_name', productName);
        formData.append('price', price);
        formData.append('quantity', 1);
        

        const basePath = (typeof window.TECHSTORE_BASE !== 'undefined' ? window.TECHSTORE_BASE : '');
        fetch((basePath || '') + 'add_to_cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cartCount = document.getElementById('cart-count');
                if (cartCount) {
                    cartCount.textContent = data.cartCount;
                }
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


    function quickView(productId) {
        window.location.href = `product_detail.php?id=${productId}`;
    }
    function showNotification(message, type = 'success') {
        const existing = document.querySelector('.notification');
        if (existing) {
            existing.remove();
        }
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
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
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
    window.addToCart = addToCart;
    window.quickView = quickView;
    window.showNotification = showNotification;
    window.toggleWishlist = toggleWishlist;
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
        const inputs = document.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                this.style.borderColor = '';
            });
        });
    });
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.closest('form').submit();
            }
        });
    }
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
