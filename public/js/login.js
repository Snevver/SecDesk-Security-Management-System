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
    console.debug('Page loaded');
    const loginForm = document.getElementById('login-form');
    if (!loginForm) {
        console.error('Login form not found');
        return;
    }
    
    loginForm.addEventListener('submit', function (event) {
        event.preventDefault();
        console.debug('Button clicked, form submitted');
        
        // Clear previous messages
        const debugElement = document.getElementById('debug');
        const errorElement = document.getElementById('error-message');
        if (debugElement) debugElement.innerHTML = '';
        if (errorElement) errorElement.textContent = '';

        // Get form values
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        console.debug('Retrieving form values:', { email, password });

        // Use base URL with path
        fetch(`/api/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                email: email,
                password: password,
            }),
            credentials: 'include'
        })
        .then((rawResponse) => {
            console.debug('Raw response:', rawResponse);
            return rawResponse.json()
        })
        .then((response) => {
            console.debug('JSON response:', response);            
            // Check for redirects. If the login was successful, it should go here.
            if (response.redirect) {
                window.location.href = response.redirect;
                return Promise.reject(new Error('Redirect'));
            }
        })
        .catch((error) => {
            console.error('Login error:', error);
            // Only show error if not a redirect
            if (error.message !== 'Redirected') {
                showError(error.message || 'An error occurred. Please try again.');
            }
        });
    });
});