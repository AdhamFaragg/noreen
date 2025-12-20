/**
 * Form Validation Script
 * Provides client-side validation for all forms
 */

// Bootstrap form validation
(function () {
    'use strict';
    window.addEventListener('load', function () {
        // Get all forms that need validation
        const forms = document.querySelectorAll('form[novalidate]');

        // Loop over forms and prevent submission if invalid
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

/**
 * Validate password strength
 */
function validatePasswordStrength(password) {
    const strength = {
        score: 0,
        feedback: []
    };

    if (password.length >= 8) strength.score++;
    else strength.feedback.push('At least 8 characters recommended');

    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength.score++;
    else strength.feedback.push('Mix uppercase and lowercase letters');

    if (/\d/.test(password)) strength.score++;
    else strength.feedback.push('Include at least one number');

    if (/[!@#$%^&*]/.test(password)) strength.score++;
    else strength.feedback.push('Include special characters (!@#$%^&*)');

    return strength;
}

/**
 * Validate email format
 */
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Validate phone number
 */
function validatePhone(phone) {
    const phoneRegex = /^[\d\s\-\+\(\)]{7,20}$/;
    return phoneRegex.test(phone);
}

/**
 * Validate price (must be positive decimal)
 */
function validatePrice(price) {
    const priceNum = parseFloat(price);
    return !isNaN(priceNum) && priceNum > 0 && priceNum <= 999999.99;
}

/**
 * Real-time password confirmation validation
 */
document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');

    if (passwordInput && confirmPasswordInput) {
        [passwordInput, confirmPasswordInput].forEach(input => {
            input.addEventListener('change', function () {
                if (passwordInput.value !== confirmPasswordInput.value) {
                    confirmPasswordInput.setCustomValidity('Passwords do not match');
                } else {
                    confirmPasswordInput.setCustomValidity('');
                }
            });
        });
    }

    // Real-time discount price validation
    const priceInput = document.getElementById('price');
    const discountPriceInput = document.getElementById('discount_price');

    if (priceInput && discountPriceInput) {
        [priceInput, discountPriceInput].forEach(input => {
            input.addEventListener('change', function () {
                const price = parseFloat(priceInput.value);
                const discountPrice = parseFloat(discountPriceInput.value);

                if (discountPrice && discountPrice >= price) {
                    discountPriceInput.setCustomValidity('Discount price must be less than regular price');
                } else {
                    discountPriceInput.setCustomValidity('');
                }
            });
        });
    }
});
