// Global JavaScript Functions

// Show loading spinner
function showLoading() {
    const loadingHTML = `
        <div id="loading-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;">
            <div class="spinner-border text-light" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', loadingHTML);
}

// Hide loading spinner
function hideLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        overlay.remove();
    }
}

// Show toast notification
function showToast(message, type = 'info') {
    const toastHTML = `
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', toastHTML);
    
    const toastElement = document.querySelector('.toast:last-child');
    const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
    toast.show();
    
    // Remove toast element after it's hidden
    toastElement.addEventListener('hidden.bs.toast', function() {
        this.closest('.toast-container').remove();
    });
}

// Confirm delete action
function confirmDelete(message = 'Are you sure you want to delete this item?') {
    return confirm(message);
}

// Format price
function formatPrice(price) {
    return '$' + parseFloat(price).toFixed(2);
}

// Add to cart (AJAX)
function addToCart(productId, quantity = 1, size = '', color = '') {
    showLoading();
    
    // In a real application, this would be an AJAX call
    // For now, we'll simulate it with a timeout
    setTimeout(() => {
        hideLoading();
        showToast('Product added to cart!', 'success');
        
        // Update cart count in navbar
        updateCartCount();
    }, 500);
}

// Add to wishlist (AJAX)
function addToWishlist(productId) {
    showLoading();
    
    setTimeout(() => {
        hideLoading();
        showToast('Product added to wishlist!', 'success');
        
        // Update wishlist count in navbar
        updateWishlistCount();
    }, 500);
}

// Update cart count
function updateCartCount() {
    // This would typically fetch from server
    const cartBadge = document.querySelector('.navbar .fa-shopping-cart + .badge');
    if (cartBadge) {
        let currentCount = parseInt(cartBadge.textContent) || 0;
        cartBadge.textContent = currentCount + 1;
    }
}

// Update wishlist count
function updateWishlistCount() {
    const wishlistBadge = document.querySelector('.navbar .fa-heart + .badge');
    if (wishlistBadge) {
        let currentCount = parseInt(wishlistBadge.textContent) || 0;
        wishlistBadge.textContent = currentCount + 1;
    }
}

// Image preview before upload
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const preview = document.getElementById(previewId);
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('is-invalid');
            
            // Add error message if not exists
            if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('invalid-feedback')) {
                const errorMsg = document.createElement('div');
                errorMsg.className = 'invalid-feedback';
                errorMsg.textContent = 'This field is required.';
                field.parentNode.insertBefore(errorMsg, field.nextSibling);
            }
        } else {
            field.classList.remove('is-invalid');
            const errorMsg = field.nextElementSibling;
            if (errorMsg && errorMsg.classList.contains('invalid-feedback')) {
                errorMsg.remove();
            }
        }
    });
    
    return isValid;
}

// Email validation
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(String(email).toLowerCase());
}

// Password strength checker
function checkPasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]+/)) strength++;
    if (password.match(/[A-Z]+/)) strength++;
    if (password.match(/[0-9]+/)) strength++;
    if (password.match(/[$@#&!]+/)) strength++;
    
    const strengthTexts = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
    const strengthColors = ['danger', 'warning', 'info', 'primary', 'success'];
    
    return {
        score: strength,
        text: strengthTexts[strength - 1] || 'Very Weak',
        color: strengthColors[strength - 1] || 'danger'
    };
}

// Show password strength indicator
function showPasswordStrength(passwordInput, strengthIndicatorId) {
    passwordInput.addEventListener('input', function() {
        const strength = checkPasswordStrength(this.value);
        const indicator = document.getElementById(strengthIndicatorId);
        
        if (indicator) {
            indicator.className = `badge bg-${strength.color}`;
            indicator.textContent = strength.text;
        }
    });
}

// Auto-dismiss alerts
function autoDismissAlerts() {
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
}

// Search with debounce
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Live search
function setupLiveSearch(searchInputId, resultsContainerId) {
    const searchInput = document.getElementById(searchInputId);
    const resultsContainer = document.getElementById(resultsContainerId);
    
    if (!searchInput || !resultsContainer) return;
    
    const performSearch = debounce(function(query) {
        if (query.length < 2) {
            resultsContainer.innerHTML = '';
            return;
        }
        
        // Show loading
        resultsContainer.innerHTML = '<div class="p-3 text-center"><div class="spinner-border spinner-border-sm"></div></div>';
        
        // In a real application, this would be an AJAX call to search.php
        setTimeout(() => {
            resultsContainer.innerHTML = `
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action">Search result 1</a>
                    <a href="#" class="list-group-item list-group-item-action">Search result 2</a>
                    <a href="#" class="list-group-item list-group-item-action">Search result 3</a>
                </div>
            `;
        }, 300);
    }, 300);
    
    searchInput.addEventListener('input', function() {
        performSearch(this.value);
    });
}

// Quantity selector
function setupQuantitySelector() {
    document.querySelectorAll('.quantity-selector').forEach(selector => {
        const minusBtn = selector.querySelector('.quantity-minus');
        const plusBtn = selector.querySelector('.quantity-plus');
        const input = selector.querySelector('.quantity-input');
        
        if (minusBtn && input) {
            minusBtn.addEventListener('click', () => {
                let value = parseInt(input.value) || 1;
                if (value > parseInt(input.min || 1)) {
                    input.value = value - 1;
                    input.dispatchEvent(new Event('change'));
                }
            });
        }
        
        if (plusBtn && input) {
            plusBtn.addEventListener('click', () => {
                let value = parseInt(input.value) || 1;
                const max = parseInt(input.max || 999);
                if (value < max) {
                    input.value = value + 1;
                    input.dispatchEvent(new Event('change'));
                }
            });
        }
    });
}

// Scroll to top button
function setupScrollToTop() {
    const scrollBtn = document.getElementById('scrollToTop');
    if (!scrollBtn) {
        // Create button if doesn't exist
        const btn = document.createElement('button');
        btn.id = 'scrollToTop';
        btn.className = 'btn btn-primary position-fixed bottom-0 end-0 m-3';
        btn.style.display = 'none';
        btn.style.zIndex = '1000';
        btn.innerHTML = '<i class="fas fa-arrow-up"></i>';
        document.body.appendChild(btn);
    }
    
    const btn = document.getElementById('scrollToTop');
    
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            btn.style.display = 'block';
        } else {
            btn.style.display = 'none';
        }
    });
    
    btn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss alerts
    autoDismissAlerts();
    
    // Setup quantity selectors
    setupQuantitySelector();
    
    // Setup scroll to top button
    setupScrollToTop();
    
    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize Bootstrap popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Add smooth scrolling to all anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
    
    // Add animation to elements when they come into view
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.product-card, .card').forEach(el => {
        observer.observe(el);
    });
});

// Export functions for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        showLoading,
        hideLoading,
        showToast,
        confirmDelete,
        formatPrice,
        addToCart,
        addToWishlist,
        validateForm,
        validateEmail,
        checkPasswordStrength
    };
}
