/**
 * Form Validation Script - Singleton Pattern
 * Provides centralized client-side validation for all forms
 */

/**
 * FormValidator Singleton
 * Ensures single instance of form validation throughout the application
 */
const FormValidator = (() => {
    let instance = null;

    class ValidatorClass {
        constructor() {
            this.errors = {};
            this.initialized = false;
        }

        /**
         * Initialize form validators on page load
         */
        init() {
            if (this.initialized) return;
            
            'use strict';
            window.addEventListener('load', () => {
                const forms = document.querySelectorAll('form[novalidate]');
                Array.prototype.slice.call(forms).forEach((form) => {
                    form.addEventListener('submit', (event) => {
                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);

            this.initPasswordConfirmation();
            this.initDiscountPriceValidation();
            this.initialized = true;
        }

        /**
         * Validate password strength
         */
        validatePasswordStrength(password) {
            const strength = {
                score: 0,
                feedback: [],
                isValid: true
            };

            if (password.length >= 8) {
                strength.score++;
            } else {
                strength.feedback.push('At least 8 characters recommended');
                strength.isValid = false;
            }

            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) {
                strength.score++;
            } else {
                strength.feedback.push('Mix uppercase and lowercase letters');
            }

            if (/\d/.test(password)) {
                strength.score++;
            } else {
                strength.feedback.push('Include at least one number');
            }

            if (/[!@#$%^&*]/.test(password)) {
                strength.score++;
            } else {
                strength.feedback.push('Include special characters (!@#$%^&*)');
            }

            return strength;
        }

        /**
         * Validate email format
         */
        validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        /**
         * Validate phone number
         */
        validatePhone(phone) {
            const phoneRegex = /^[\d\s\-\+\(\)]{7,20}$/;
            return phoneRegex.test(phone);
        }

        /**
         * Validate price (must be positive decimal)
         */
        validatePrice(price) {
            const priceNum = parseFloat(price);
            return !isNaN(priceNum) && priceNum > 0 && priceNum <= 999999.99;
        }

        /**
         * Validate password confirmation match
         */
        validatePasswordMatch(password1, password2) {
            return password1 === password2;
        }

        /**
         * Initialize password confirmation validation
         */
        initPasswordConfirmation() {
            document.addEventListener('DOMContentLoaded', () => {
                const passwordInput = document.getElementById('password');
                const confirmPasswordInput = document.getElementById('confirm_password');

                if (passwordInput && confirmPasswordInput) {
                    [passwordInput, confirmPasswordInput].forEach(input => {
                        input.addEventListener('change', () => {
                            if (passwordInput.value !== confirmPasswordInput.value) {
                                confirmPasswordInput.setCustomValidity('Passwords do not match');
                            } else {
                                confirmPasswordInput.setCustomValidity('');
                            }
                        });
                    });
                }
            });
        }

        /**
         * Initialize discount price validation
         */
        initDiscountPriceValidation() {
            document.addEventListener('DOMContentLoaded', () => {
                const priceInput = document.getElementById('price');
                const discountPriceInput = document.getElementById('discount_price');

                if (priceInput && discountPriceInput) {
                    [priceInput, discountPriceInput].forEach(input => {
                        input.addEventListener('change', () => {
                            const price = parseFloat(priceInput.value);
                            const discountPrice = parseFloat(discountPriceInput.value);

                            if (discountPrice >= price) {
                                discountPriceInput.setCustomValidity('Discount price must be less than regular price');
                            } else {
                                discountPriceInput.setCustomValidity('');
                            }
                        });
                    });
                }
            });
        }
    }

    return {
        /**
         * Get singleton instance
         */
        getInstance: () => {
            if (!instance) {
                instance = new ValidatorClass();
            }
            return instance;
        }
    };
})();

/**
 * Initialize validator on page load
 */
document.addEventListener('DOMContentLoaded', () => {
    FormValidator.getInstance().init();
});
