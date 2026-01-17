/**
 * Main JavaScript File
 * Handles events, sliders, and interactive features
 */

// Variables for slider functionality
let currentSlide = 0;
const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.dot');
const totalSlides = slides.length;

// Function to change slide
function changeSlide(direction) {
    // Remove active class from current slide and dot
    slides[currentSlide].classList.remove('active');
    if (dots[currentSlide]) {
        dots[currentSlide].classList.remove('active');
    }
    
    // Calculate new slide index
    currentSlide += direction;
    
    // Loop around if at beginning or end
    if (currentSlide >= totalSlides) {
        currentSlide = 0;
    } else if (currentSlide < 0) {
        currentSlide = totalSlides - 1;
    }
    
    // Add active class to new slide and dot
    slides[currentSlide].classList.add('active');
    if (dots[currentSlide]) {
        dots[currentSlide].classList.add('active');
    }
}

// Function to go to specific slide
function goToSlide(index) {
    // Remove active class from current slide and dot
    slides[currentSlide].classList.remove('active');
    if (dots[currentSlide]) {
        dots[currentSlide].classList.remove('active');
    }
    
    // Set new slide index
    currentSlide = index;
    
    // Add active class to new slide and dot
    slides[currentSlide].classList.add('active');
    if (dots[currentSlide]) {
        dots[currentSlide].classList.add('active');
    }
}

// Auto-advance slider every 5 seconds
let sliderInterval = setInterval(() => {
    if (slides.length > 0) {
        changeSlide(1);
    }
}, 5000);

// Event listeners for slider controls
document.addEventListener('DOMContentLoaded', function() {
    // Pause slider on hover
    const heroSection = document.querySelector('.hero-section');
    if (heroSection) {
        heroSection.addEventListener('mouseenter', () => {
            clearInterval(sliderInterval);
        });
        
        heroSection.addEventListener('mouseleave', () => {
            sliderInterval = setInterval(() => {
                if (slides.length > 0) {
                    changeSlide(1);
                }
            }, 5000);
        });
    }
    
    // Add to cart function (placeholder)
    window.addToCart = function(productId) {
        // This would typically send an AJAX request to add item to cart
        alert('Product added to cart! (This is a demo - cart functionality not implemented)');
    };
    
    // Image zoom on product detail page
    const mainProductImage = document.getElementById('mainProductImage');
    if (mainProductImage) {
        mainProductImage.addEventListener('click', function() {
            // Toggle zoom effect
            if (this.style.transform === 'scale(1.5)') {
                this.style.transform = 'scale(1)';
                this.style.cursor = 'zoom-in';
            } else {
                this.style.transform = 'scale(1.5)';
                this.style.cursor = 'zoom-out';
                this.style.transition = 'transform 0.3s';
            }
        });
        
        mainProductImage.style.cursor = 'zoom-in';
    }
    
    // Form validation events
    const forms = document.querySelectorAll('.crud-form');
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
                alert('Please fill in all required fields.');
            }
        });
    });
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
    
    // Product card hover effects
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});

// Function to handle search (if search functionality is added)
function handleSearch(query) {
    if (query.trim().length > 0) {
        window.location.href = `products.php?search=${encodeURIComponent(query)}`;
    }
}

// Event listener for search input (if exists)
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                handleSearch(this.value);
            }
        });
    }
});
