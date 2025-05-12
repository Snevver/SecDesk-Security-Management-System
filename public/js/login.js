// Get the base URL of the application
const BASE_URL = window.location.origin + window.location.pathname.split('/public')[0] + '/public';

function debugLog(message) {
    const debugEl = document.getElementById("debug");
    if (debugEl) {
        debugEl.style.display = "block";
        debugEl.innerHTML += message + "<br>";
    }
    console.log(message);
}

function showError(message) {
    const errorEl = document.getElementById('error-message');
    if (errorEl) {
        errorEl.textContent = message;
    }
}


document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    
    if (!loginForm) {
        console.error('Login form not found');
        return;
    }
    
    loginForm.addEventListener('submit', function (event) {
        event.preventDefault();
        
        // Clear previous messages
        const debugElement = document.getElementById('debug');
        const errorElement = document.getElementById('error-message');
        if (debugElement) debugElement.innerHTML = '';
        if (errorElement) errorElement.textContent = '';

        // Get form values
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        // Use base URL with path
        fetch(`${BASE_URL}/api/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                email: email,
                password: password,
            }),
        })
        .then((response) => {            
            // Check for redirects
            if (response.redirected) {
                window.location.href = response.url;
                return Promise.reject(new Error('Redirected'));
            }
            
            // Check content type
            const contentType = response.headers.get('Content-Type');
            
            // if (!contentType || !contentType.includes('application/json')) {
            //     return response.text().then(text => {
            //         throw new Error('Invalid response format');
            //     });
            // }
            
            return response;
        })
        .then((data) => {            
            if (data.success) {                
                // Store session data before redirecting
                if (data.email) {
                    sessionStorage.setItem('userEmail', data.email);
                }
                
                if (data.role) {
                    sessionStorage.setItem('userRole', data.role);
                }

                // Redirect to the specified path or default to home
                let redirectPath = data.redirect || '/';
                if (redirectPath.startsWith('/') && !redirectPath.startsWith('//')) {
                    redirectPath = BASE_URL + redirectPath;
                }

                window.location.href = redirectPath;
            } else {
                // Im currently getting this error message from the server. Been stuck on this for a while. 
                // The server is returning a 200 status code but the response is not JSON. 
                // Rob the saviour will have to help me with this one.
                const errorMsg = data.error || 'Login failed. Please try againn.';
                showError(errorMsg);
            }
        })
        .catch((error) => {
            // Only show error if not a redirect
            if (error.message !== 'Redirected') {
                showError(error.message || 'An error occurred. Please try again.');
            }
        });
    });
});