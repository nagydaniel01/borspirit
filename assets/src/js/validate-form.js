(function ($) {
    'use strict';

    var errorShown = {};
    function showError(input, message) {
        var $input = $(input);
        var $error = $input.next('.error-message');
        var errorId = 'error-' + $input.attr('id');

        if (!errorShown[errorId]) {
            if ($input.is(':checkbox') || $input.is(':radio')) {
                // Update to check if the parent is either a fieldset or a label tag
                var $closestParent = $input.closest('fieldset, label');
                $error = $closestParent.find('.error-message');

                if ($error.length === 0) {
                    $error = $('<span id="' + errorId + '" class="error-message" role="alert"></span>').insertAfter($closestParent);
                }

                /*
                $error = $input.closest('fieldset').find('.error-message');
                if ($error.length === 0) {
                    $error = $('<span id="' + errorId + '" class="error-message" role="alert"></span>').insertAfter($input.closest('fieldset'));
                }
                */
            } else if ($input.is('select')) {
                $error = $input.parent().find('.error-message');
                if ($error.length === 0) {
                    $error = $('<span id="' + errorId + '" class="error-message" role="alert"></span>').insertAfter($input);
                }
            } else {
                if ($error.length === 0) {
                    $error = $('<span id="' + errorId + '" class="error-message" role="alert"></span>').insertAfter($input);
                }
            }

            if ($error.text() !== message) {
                $error.text(message);
            }

            $input.attr('aria-describedby', $error.attr('id'));
            $input.addClass('is-invalid');

            errorShown[errorId] = true;
        }
    }

    function clearError(input) {
        var $input = $(input);
        var $error;
        var errorId = 'error-' + $input.attr('id');

        if (errorShown[errorId]) {
            // Check if input is checkbox or radio
            if ($input.is(':checkbox') || $input.is(':radio')) {
                // Determine error message location
                var $fieldset = $input.closest('fieldset');
                if ($fieldset.length) {
                    $error = $fieldset.next('.error-message');
                } else {
                    var $label = $input.siblings('label').length ? $input.siblings('label') : $input.closest('label');
                    $error = $label.length ? $label.next('.error-message') : $input.next('.error-message');
                }
            } else { // For other input types
                $error = $input.next('.error-message');
            }
            
            if ($error.length) {
                $error.remove();
            }

            $input.removeAttr('aria-describedby');
            $input.removeClass('is-invalid');

            errorShown[errorId] = false;
        }
    }

    function updateErrorState(input, isValid, message) {
        var $input = $(input);
        var errorId = 'error-' + $input.attr('id');
        var $error = $('#' + errorId);

        if (!isValid) {
            if (!$error.length) {
                $error = $('<span id="' + errorId + '" class="error-message" role="alert"></span>').insertAfter($input);
            }
            
            if ($error.text() !== message) {
                $error.text(message);
            }

            $input.attr('aria-describedby', errorId);
            $input.addClass('is-invalid');
        } else {
            if ($error.length) {
                $error.remove();
            }

            $input.removeAttr('aria-describedby');
            $input.removeClass('is-invalid');
        }
    }

    function parseTimeString(timeString) {
        var parts = timeString.split(':');
        var hours = parseInt(parts[0], 10);
        var minutes = parseInt(parts[1], 10);
        return new Date(0, 0, 0, hours, minutes);
    }

    /*
    function validateText(input) {
        var $input = $(input);
        var value = $input.val().trim();
        var errorMessage = $input.data('error') || 'This field is required.';
        if (!value) {
            showError(input, errorMessage);
            return false;
        } else {
            clearError(input);
            return true;
        }
    }
    */

    function validateText(input) {
        var $input = $(input);
        var value = $input.val().trim();
        var errorMessage = $input.data('error') || 'This field is required.';
        var isValid = true;
        
        if (!value) {
            isValid = false;
        }

        updateErrorState(input, isValid, errorMessage);
        return isValid;
    }

    function validateEmail(input) {
        var $input = $(input);
        var value = $input.val().trim();
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        var errorMessage = $input.data('error') || 'This field is required.';
        var isValid = true;

        if (!value) {
            isValid = false;
            showError(input, errorMessage);
        } else if (!emailPattern.test(value)) {
            isValid = false;
            errorMessage = 'Please enter a valid email address.';
        }

        updateErrorState(input, isValid, errorMessage);
        return isValid;
    }

    function validatePhone(input) {
        var $input = $(input);
        var value = $input.val().trim();
        var phonePattern = /^\+?\d{10,15}$/;
        var errorMessage = $input.data('error') || 'This field is required.';
        var isValid = true;

        if (!value) {
            isValid = false;
            showError(input, errorMessage);
        } else if (!phonePattern.test(value)) {
            isValid = false;
            errorMessage = 'Please enter a valid phone number.';
        }

        updateErrorState(input, isValid, errorMessage);
        return isValid;
    }

    function validateURL(input) {
        var $input = $(input);
        var value = $input.val().trim();
        var errorMessage = $input.data('error') || 'This field is required.';
        var isValid = true;

        if (!value) {
            isValid = false;
            showError(input, errorMessage);
        } else {
            var urlPattern = /^(https?:\/\/)?([\da-z.-]+)\.([a-z.]{2,6})([/\w .-]*)*\/?$/i;

            if (!urlPattern.test(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid URL.';
            }
        }

        updateErrorState(input, isValid, errorMessage);
        return isValid;
    }

    function validateNumber(input) {
        var $input = $(input);
        var value = $input.val().trim();
        var min = parseFloat($input.attr('min'));
        var max = parseFloat($input.attr('max'));
        var errorMessage = $input.data('error') || 'This field is required.';
        var isValid = true;

        if (!value) {
            isValid = false;
            showError(input, errorMessage);
        } else if (isNaN(value)) {
            isValid = false;
            errorMessage = 'Please enter a valid number.';
        } else if ((min && parseFloat(value) < min) || (max && parseFloat(value) > max)) {
            isValid = false;
            errorMessage = 'Please enter a number between ' + min + ' and ' + max + '.';
        }

        updateErrorState(input, isValid, errorMessage);
        return isValid;
    }

    function validateFile(input) {
        var $input = $(input);
        var files = $input.get(0).files;
        var errorMessage = $input.data('error') || 'This field is required.';

        if (files.length === 0) {
            showError(input, errorMessage);
            return false;
        } else {
            clearError(input);
            return true;
        }
    }

    function validateDate(input) {
        var $input = $(input);
        var value = $input.val().trim();
        var errorMessage = $input.data('error') || 'This field is required.';
        var isValid = true;
    
        if (!value) {
            isValid = false;
            showError(input, errorMessage);
        } else {
            var selectedDate = new Date(value);
            var today = new Date();
            var startDate = new Date('2022-01-01'); // Example start date
            var endDate = new Date('2023-12-31'); // Example end date

            if (selectedDate < today) {
                isValid = false;
                errorMessage = 'Please select a date from today onwards.';
            }

            /*
            if (selectedDate > today) {
                isValid = false;
                errorMessage = 'Please select a date up to today.';
            }

            if (selectedDate < startDate || selectedDate > endDate) {
                isValid = false;
                errorMessage = 'Please select a date between ' + startDate.toLocaleDateString() + ' and ' + endDate.toLocaleDateString() + '.';
            }
            */
        }

        updateErrorState(input, isValid, errorMessage);
        return isValid;
    }

    function validateTime(input) {
        var $input = $(input);
        var value = $input.val().trim();
        var errorMessage = $input.data('error') || 'This field is required.';
        var isValid = true;
    
        if (!value) {
            isValid = false;
            showError(input, errorMessage);
        } else {
            var selectedTime = parseTimeString(value);
            var startTime = parseTimeString('08:00'); // Example start time (8:00 AM)
            var endTime = parseTimeString('17:00'); // Example end time (5:00 PM)

            if (selectedTime < startTime || selectedTime > endTime) {
                isValid = false;
                errorMessage = 'Please select a time between 8:00 AM and 5:00 PM.';
            }
        }
    
        updateErrorState(input, isValid, errorMessage);
        return isValid;
    }

    function validateBirthDate(input) {
        var $input = $(input);
        var value = $input.val().trim();
        var errorMessage = $input.data('error') || 'This field is required.';
        var isValid = true;
    
        if (!value) {
            isValid = false;
            showError(input, errorMessage);
        } else {
            var dob = new Date(value);
            var today = new Date();
            var age = today.getFullYear() - dob.getFullYear();
            var monthDiff = today.getMonth() - dob.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                age--;
            }

            if (age < 18) {
                isValid = false;
                errorMessage = 'You must be at least 18 years old.';
            }
        }
    
        updateErrorState(input, isValid, errorMessage);
        return isValid;
    }

    function validateSelect(input) {
        var $input = $(input);
        var value = $input.val();
        var errorMessage = $input.data('error') || 'This field is required.';
        if (!value) {
            showError(input, errorMessage);
            return false;
        } else {
            clearError(input);
            return true;
        }
    }

    function validateCheckbox(input) {
        var name = $(input).attr('name');
        var $input = $('input[name="' + name + '"]');
        var errorMessage = $($input).data('error') || 'This field is required.';
        if (!$(input).is(':checked')) {
            showError(input, errorMessage);
            return false;
        } else {
            clearError(input);
            return true;
        }
    }

    function validateMultipleCheckbox(input) {
        var name = $(input).attr('name');
        var $inputs = $('input[name="' + name + '"]');
        var errorMessage = $inputs.first().data('error') || 'This field is required.';
        if ($inputs.filter(':checked').length === 0) {
            showError($inputs.first(), errorMessage);
            return false;
        } else {
            clearError($inputs.first());
            return true;
        }
    }

    function validateRadio(input) {
        var name = $(input).attr('name');
        var $inputs = $('input[name="' + name + '"]');
        var errorMessage = $inputs.first().data('error') || 'This field is required.';
        if ($inputs.filter(':checked').length === 0) {
            showError($inputs.first(), errorMessage);
            return false;
        } else {
            clearError($inputs.first());
            return true;
        }
    }

    function validatePassword(input) {
        var $input = $(input);
        var value = $input.val().trim();
        var errorMessage = $input.data('error') || 'This field is required.';
        var isValid = true;

        // Example password requirements
        var minLength = 8; // Minimum length
        var hasUpperCase = /[A-Z]/.test(value); // Must contain at least one uppercase letter
        var hasLowerCase = /[a-z]/.test(value); // Must contain at least one lowercase letter
        var hasDigit = /\d/.test(value); // Must contain at least one digit
        var hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(value); // Must contain at least one special character

        if (!value) {
            isValid = false;
            errorMessage = 'Password is required.';
        } else if (value.length < minLength) {
            isValid = false;
            errorMessage = 'Password must be at least ' + minLength + ' characters long.';
        } else if (!hasUpperCase || !hasLowerCase || !hasDigit || !hasSpecialChar) {
            isValid = false;
            errorMessage = 'Password must contain at least one uppercase letter, one lowercase letter, one digit, and one special character.';
        }

        // Use showError function to display the error message
        if (!isValid) {
            showError(input, errorMessage);
        } else {
            clearError(input);
        }

        updateErrorState(input, isValid, errorMessage);
        return isValid;
    }

    window.formValidation = {
        validateText: validateText,
        validateEmail: validateEmail,
        validatePhone: validatePhone,
        validateURL: validateURL,
        validateNumber: validateNumber,
        validateFile: validateFile,
        validateDate: validateDate,
        validateTime: validateTime,
        validateBirthDate: validateBirthDate,
        validateSelect: validateSelect,
        validateCheckbox: validateCheckbox,
        validateMultipleCheckbox: validateMultipleCheckbox,
        validateRadio: validateRadio,
        validatePassword: validatePassword
    };
})(jQuery);