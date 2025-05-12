/**
 * This script handles:
 * - User login functionality using the login() function in the AuthenticationController.
 */

// Base URL of the application
const BASE_URL = '/SecDesk-Security-Management-System/public';

// Function to log debug info
function debugLog(message) {
    const debugEl = document.getElementById("debug");
    if (debugEl) {
        debugEl.style.display = "block";
        debugEl.innerHTML += message + "<br>";
    }
    console.log(message);
}

// Function to display error message
function showError(message) {
    debugLog(`Error displayed to user: ${message}`);
    const errorEl = document.getElementById('error-message');
    if (errorEl) {
        errorEl.textContent = message;
    }
}

// Script to handle login
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    
    if (!loginForm) {
        console.error('Login form not found');
        return;
    }
    
    loginForm.addEventListener('submit', function (event) {
        debugLog('Login form submitted');
        event.preventDefault();
        
        // Clear previous messages
        const debugElement = document.getElementById('debug');
        const errorElement = document.getElementById('error-message');
        
        if (debugElement) debugElement.innerHTML = '';
        if (errorElement) errorElement.textContent = '';

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        
        debugLog(`Email entered: ${email}`);
        debugLog(`Password length: ${password.length} characters`);

        // Use base URL with path
        fetch(`${BASE_URL}/api/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                email: email,
                password: password,
            }),
        })
        .then((response) => {
            debugLog(`Response received with status: ${response.status}`);
            
            // Check for redirects
            if (response.redirected) {
                debugLog(`Redirect detected to: ${response.url}`);
                window.location.href = response.url;
                return Promise.reject(new Error('Redirected'));
            }
            
            // Check content type
            const contentType = response.headers.get('Content-Type');
            debugLog(`Content-Type: ${contentType}`);
            
            if (!contentType || !contentType.includes('application/json')) {
                debugLog('Expected JSON but got different content type');
                return response.text().then(text => {
                    debugLog(`Non-JSON response: ${text.substring(0, 100)}...`);
                    throw new Error('Invalid response format');
                });
            }
            
            return response.json();
        })
        .then((data) => {
            debugLog(`Parsed data received: ${JSON.stringify(data)}`);
            
            if (data.success) {
                debugLog(`Login successful for user: ${data.email}`);
                
                // Store session data BEFORE redirecting
                if (data.email) {
                    sessionStorage.setItem('userEmail', data.email);
                    debugLog(`User email stored in session: ${data.email}`);
                }
                
                if (data.role) {
                    sessionStorage.setItem('userRole', data.role);
                    debugLog(`User role stored in session: ${data.role}`);
                }

                // Only redirect ONCE at the end
                // Add BASE_URL to the redirect path if it's a relative path
                let redirectPath = data.redirect || '/';
                if (redirectPath.startsWith('/') && !redirectPath.startsWith('//')) {
                    redirectPath = BASE_URL + redirectPath;
                }
                debugLog(`Redirecting to: ${redirectPath}`);
                window.location.href = redirectPath;
            } else {
                const errorMsg = data.error || 'Login failed. Please try again.';
                debugLog(`Login failed with error: ${errorMsg}`);
                showError(errorMsg);
            }
        })
        .catch((error) => {
            // Only show error if not a redirect
            if (error.message !== 'Redirected') {
                debugLog(`Error caught in catch block: ${error.message}`);
                showError(error.message || 'An error occurred. Please try again.');
            }
        });
    });
});