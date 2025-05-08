/**
 * This script handles:
 * - User login functionality using the login() function in the AuthenticationController.
 */

// Function to log debug info
function debugLog(message) {
    const debugEl = document.getElementById('debug');
    debugEl.style.display = 'block';
    debugEl.innerHTML += message + '<br>';
    console.log(message);
}

    // Function to display error message
    function showError(message) {
        document.getElementById('error-message').textContent = message;
    }

// Script to handle login
document.getElementById('login-form').addEventListener('submit', function(event) {
    event.preventDefault();
    document.getElementById('debug').innerHTML = '';
    document.getElementById('error-message').textContent = '';
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    // Send login request to the server
    fetch(`login`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            email: email,
            password: password
        })
    })
    .then(response => {
        // debugLog(`Response status: ${response.status}`);
        return response.text().then(text => {
            if(text.length === 0) {
                // debugLog('Empty response from server');
                throw new Error('Empty response from server');
            }

            // First check if the response is HTML (error page)
            if (text.trim().startsWith('<!DOCTYPE html>') || text.trim().startsWith('<html')) {
                // debugLog('Received HTML response instead of JSON');
                throw new Error('Server returned HTML instead of JSON');
            }

            try {
                debugLog(`Response text: ${text.substring(0, 100)}...`);
                return JSON.parse(text);
            } catch (e) {
                // debugLog(`JSON parse error: ${e.message} - Text: ${text.substring(0, 100)}`);

                if (text.includes('Connection failed') || text.includes('PDO')) {
                    throw new Error('Database connection issue. Please try again later.');
                }

                throw new Error('Invalid response from server: ' + text.substring(0, 100));
            }
        });
    })
    .then(data => {
        // debugLog(`Parsed data: ${JSON.stringify(data)}`);
        if (data.success) {
            // Store user information in session storage for use in dashboard
            if (data.email) {
                sessionStorage.setItem('userEmail', data.email);
            }
            if (data.role) {
                sessionStorage.setItem('userRole', data.role);
            }
            
            // debugLog(`Login successful, redirecting to: ${data.redirect}`);
            window.location.href = data.redirect;
        } else {
            const errorMsg = data.error || 'Login failed. Please try again.';
            // debugLog(`Login failed: ${errorMsg}`);
            // showError(errorMsg);
        }
    })
    .catch(error => {
        // debugLog(`Error: ${error.message}`);
        // showError(error.message || 'An error occurred. Please try again.');
    });
});