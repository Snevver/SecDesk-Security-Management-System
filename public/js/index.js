// Helper function for debugging
function debugLog(message, obj = null) {
    const debugOutput = document.getElementById('debug-output');
    if (debugOutput) {
        debugOutput.style.display = 'block';
        if (obj) {
            debugOutput.textContent += message + ': ' + JSON.stringify(obj, null, 2) + '\n\n';
        } else {
            debugOutput.textContent += message + '\n';
        }
    }
    console.log(message, obj || '');
}

// Check if user is logged in
function checkLoginStatus() {
    // debugLog('Checking login status...');
    
    // Add withCredentials to make sure cookies are sent
    fetch('isLoggedIn', {
        credentials: 'same-origin'
    })
        .then(response => {
            // debugLog('Login status response received', response);
            return response.json();
        })
        .then(data => {
            // debugLog('Login status data', data);
            
            if (!data.success) {
                // debugLog('User not logged in, redirecting to login page');
                window.location.href = 'login.html';
                return;
            }
            
            // debugLog('User is logged in, updating UI');
            
            // First try to get data from the session response
            const userEmailEl = document.getElementById('user-email');
            const userRoleEl = document.getElementById('user-role');
            const userIdEl = document.getElementById('user-id');
            
            if (userEmailEl && data.email) {
                userEmailEl.textContent = data.email;
            }

            if (userRoleEl && data.role) {
                userRoleEl.textContent = data.role;
            }

            if (userIdEl && data.user_id) {
                userIdEl.textContent = data.user_id;
            }

            // Fallback to sessionStorage if needed
            if (userEmailEl && !data.email) {
                const userEmail = sessionStorage.getItem('userEmail');
                if (userEmail) {
                    userEmailEl.textContent = userEmail;
                }
            }

            if (userRoleEl && !data.role) {
                const userRole = sessionStorage.getItem('userRole');
                if (userRole) {
                    userRoleEl.textContent = userRole;
                }
            }
            
            // If the user is logged in, fetch the users tests
            fetchCustomersTests();
        })
        .catch(error => {
            debugLog('Error checking login status: ' + error.message);
            // Redirect to login page on error as a safety measure
            window.location.href = 'login.html';
        });
}

// Fetch test data after confirming login
function fetchCustomersTests() {
    // debugLog('Fetching tests...');
    
    fetch('getCustomersTests', {
        credentials: 'same-origin',
    })
        .then((response) => {
            // debugLog('Tests API response received', response);
            return response.json();
        })
        .then((data) => {
            // debugLog('Tests data received', data);
            
            const testListElement = document.getElementById('test-list');
            
            if (!testListElement) {
                // debugLog('Test list element not found');
                return;
            }
            
            // Clear loading message
            testListElement.classList.remove('loading');
            
            // Check if data exists
            if (!data) {
                testListElement.innerHTML = '<p>No data received.</p>';
                return;
            }
            
            // Extract tests from the data
            let tests;
            tests = data;
            
            // debugLog('Extracted tests', tests);
            
            if (tests.length === 0) {
                testListElement.innerHTML = '<p>No tests found.</p>';
                return;
            }
        
            let html = '<ul>';
            tests.forEach((test) => {                
                html += `<li>
                            <strong>Test Name:</strong> ${test.test_name || 'Not found'} <br>
                            <strong>Description:</strong> ${test.test_description || 'Not found'}
                        </li>`;
            });
            html += '</ul>';
        
            testListElement.innerHTML = html;
        })
        .catch((error) => {
            // debugLog('Error fetching tests: ' + error.message);
            
            const testListElement = document.getElementById('test-list');
            if (testListElement) {
                testListElement.classList.remove('loading');
                testListElement.innerHTML = '<p>Error loading tests: ' + error.message + '</p>';
            }
        });
}

// When loading the page, check if the user is logged in
document.addEventListener('DOMContentLoaded', function() {
    checkLoginStatus();
});