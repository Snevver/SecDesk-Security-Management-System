//======================================================================
// EMPLOYEE DASHBOARD SCRIPT
//======================================================================
function fetchAccounts(accountType) {
    console.log(`Fetching accounts of type: ${accountType}`);
    fetch(`/api/get-all-${accountType}s`, {
        credentials: 'same-origin',
    })
    .then((response) => response.json())
        .then((data) => {
            const userListElement = document.getElementById(`${accountType}s-list`);

            if (!data.success) {
                userListElement.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-exclamation-triangle"></i>
                        <p>Error loading ${accountType}s: ${data.error || 'Unknown error'}</p>
                    </div>
                `;
                return;
            }

            if (!data.users || data.users.length === 0) {
                userListElement.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-people"></i>
                        <p>No ${accountType}s found.</p>
                        <small>Create your first ${accountType} using the button above.</small>
                    </div>
                `;
                return;
            }

            let listElement = '';

            for (let user of data.users) {
                listElement += `
                    <div class="user-item">
                        <div class="user-email">
                            <i class="bi bi-person-badge me-2"></i>${user.email}
                        </div>
                        <div class="user-id">ID: ${user.id}</div>
                    </div>
                `;
            }

            userListElement.innerHTML = listElement;
        })        .catch((error) => {
            document.getElementById(`${accountType}-list`).innerHTML = `
                <div class="empty-state">
                    <i class="bi bi-exclamation-triangle"></i>
                    <p>Error: ${error.message}</p>
                </div>
            `;
        });
}

fetchAccounts("customer");
fetchAccounts("employee");
fetchAccounts("admin");

// Modal functions
function openAccountModal() {
    console.log('Opening account creation modal');
    const modal = document.getElementById('emailModal');
    const emailInput = document.getElementById('emailInput');
    const accountTypeSelect = document.getElementById('accountType');
    
    if (!modal || !emailInput || !accountTypeSelect) {
        console.error('Modal elements not found');
        return;
    }
    
    emailInput.value = '';
    accountTypeSelect.value = '';
    
    // Use Bootstrap 5 modal API
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    // Focus on account type select after modal is shown
    modal.addEventListener('shown.bs.modal', function() {
        accountTypeSelect.focus();
    }, { once: true });
    
    // Remove any existing event listeners
    const form = document.getElementById('emailForm');
    const newForm = form.cloneNode(true);
    form.parentNode.replaceChild(newForm, form);
    
    // Add new event listener
    newForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('emailInput').value.trim();
        const accountType = document.getElementById('accountType').value;
        
        if (email && accountType) {
            createAccount(accountType, email);
            bootstrapModal.hide();
        } else {
            alert('Please fill in both email and account type');
        }
    });
}

function createAccount(accountType, email) {
    console.log('Creating account:', accountType, email);
    
    fetch('/create-account', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ 
            accountType: accountType,
            email: email 
        }),
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`${accountType.charAt(0).toUpperCase() + accountType.slice(1)} created successfully!`);
            
            // Refresh the appropriate list
            if (accountType === 'customer') {
                fetchCustomers();
            } else if (accountType === 'employee') {
                fetchEmployees();
            } else if (accountType === 'admin') {
                fetchAdmins();
            }
        } else {
            alert(`Error creating ${accountType}: ` + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(`Error creating ${accountType}: ` + error.message);
    });
}


// Button event listeners
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up button listeners');
    
    // Single create account button
    const createAccountBtn = document.getElementById('create-account-btn');
    
    console.log('Create account button found:', !!createAccountBtn);

    if (createAccountBtn) {
        createAccountBtn.addEventListener('click', () => {
            console.log('Create Account button clicked');
            openAccountModal();
        });
    } else {
        console.error('Create account button not found!');
    }
});
