// Change Password Functionality
document.addEventListener('DOMContentLoaded', function () {
    console.log('Change password script loaded');
    const changePasswordForm = document.getElementById('changePasswordForm');
    const passwordError = document.getElementById('passwordError');
    const passwordSuccess = document.getElementById('passwordSuccess');
    const changePasswordModal = document.getElementById('changePasswordModal');
    const changePasswordButtons = document.querySelectorAll(
        '[data-bs-target="#changePasswordModal"]',
    );
    console.log('Elements found:', {
        form: !!changePasswordForm,
        modal: !!changePasswordModal,
        buttons: changePasswordButtons.length,
    });

    // Test modal functionality
    changePasswordButtons.forEach((button) => {
        button.addEventListener('click', function () {
            console.log('Change password button clicked');
        });
    });

    if (changePasswordForm) {
        changePasswordForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const currentPassword =
                document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword =
                document.getElementById('confirmPassword').value;

            // Clear previous messages
            hideMessages();

            // Validate passwords
            if (!validatePasswords(newPassword, confirmPassword)) {
                return;
            }

            // Disable submit button during request
            const submitBtn = document.getElementById('changePasswordSubmit');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Changing...';

            // Send request to change password
            fetch('/api/change-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    currentPassword: currentPassword,
                    newPassword: newPassword,
                }),
                credentials: 'same-origin',
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        showSuccess('Password changed successfully!');
                        clearForm();

                        // Close modal after 2 seconds
                        setTimeout(() => {
                            const modal = bootstrap.Modal.getInstance(
                                document.getElementById('changePasswordModal'),
                            );
                            if (modal) {
                                modal.hide();
                            }
                        }, 2000);
                    } else {
                        showError(
                            'Error changing password: ' +
                                (data.message || data.error || 'Unknown error'),
                        );
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    showError(
                        'An error occurred while changing the password. Please try again.',
                    );
                })
                .finally(() => {
                    // Re-enable submit button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
        });
    }

    // Password validation function
    function validatePasswords(newPassword, confirmPassword) {
        // Check password length
        if (newPassword.length < 8) {
            showError('New password must be at least 8 characters long.');
            return false;
        }

        // Check if passwords match
        if (newPassword !== confirmPassword) {
            showError('New passwords do not match.');
            return false;
        }

        // Check password strength (optional)
        if (!isPasswordStrong(newPassword)) {
            showError(
                'Password should contain at least one uppercase letter, one lowercase letter, and one number.',
            );
            return false;
        }

        return true;
    }

    // Check password strength
    function isPasswordStrong(password) {
        const hasUpper = /[A-Z]/.test(password);
        const hasLower = /[a-z]/.test(password);
        const hasNumber = /\d/.test(password);
        return hasUpper && hasLower && hasNumber;
    }

    // Show error message
    function showError(message) {
        if (passwordError) {
            passwordError.textContent = message;
            passwordError.classList.remove('d-none');
        }
        if (passwordSuccess) {
            passwordSuccess.classList.add('d-none');
        }
    }

    // Show success message
    function showSuccess(message) {
        if (passwordSuccess) {
            passwordSuccess.textContent = message;
            passwordSuccess.classList.remove('d-none');
        }
        if (passwordError) {
            passwordError.classList.add('d-none');
        }
    }

    // Hide all messages
    function hideMessages() {
        if (passwordError) {
            passwordError.classList.add('d-none');
        }
        if (passwordSuccess) {
            passwordSuccess.classList.add('d-none');
        }
    }

    // Clear form
    function clearForm() {
        if (changePasswordForm) {
            changePasswordForm.reset();
        }
    } // Reset form when modal is hidden
    if (changePasswordModal) {
        changePasswordModal.addEventListener('hidden.bs.modal', function () {
            clearForm();
            hideMessages();
        });
    }

    // Real-time password confirmation validation
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const newPasswordInput = document.getElementById('newPassword');

    if (confirmPasswordInput && newPasswordInput) {
        confirmPasswordInput.addEventListener('input', function () {
            const newPassword = newPasswordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            if (confirmPassword && newPassword !== confirmPassword) {
                confirmPasswordInput.classList.add('is-invalid');
            } else {
                confirmPasswordInput.classList.remove('is-invalid');
            }
        });
    }
});
