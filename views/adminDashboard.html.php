<!DOCTYPE html>
<html lang="en">    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Dashboard</title>
        <style>
            .modal {
                position: fixed;
                z-index: 1000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
            }
            
            .modal-content {
                background-color: #fefefe;
                margin: 15% auto;
                padding: 0;
                border: 1px solid #888;
                width: 400px;
                border-radius: 5px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }
            
            .modal-header {
                padding: 15px 20px;
                border-bottom: 1px solid #eee;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .modal-header h3 {
                margin: 0;
                color: #333;
            }
            
            .close {
                color: #aaa;
                font-size: 28px;
                font-weight: bold;
                cursor: pointer;
                line-height: 1;
            }
            
            .close:hover,
            .close:focus {
                color: #000;
            }
            
            .modal-body {
                padding: 20px;
            }
            
            .form-group label {
                display: block;
                margin-bottom: 5px;
                color: #333;
                font-weight: bold;
            }
            
            .form-group input {
                border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 14px;
            }
            
            .form-group input:focus {
                outline: none;
                border-color: #007bff;
                box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
            }
            
            .form-actions button {
                border: 1px solid #ddd;
                border-radius: 4px;
                cursor: pointer;
            }
            
            .form-actions button:hover {
                opacity: 0.9;
            }        
        </style>
    </head>
    
    <body>
        <div class="container">
            <h1>Admin Dashboard</h1>

            <button id="create-customer-btn">Create New Customer</button>
            <button id="create-employee-btn">Create New Employee</button>

            <div>
                <h2>Logged in as <span id="email"><?=$_SESSION['email'] ?? "Unknown"?></span></h2>
            </div>

            <div>
                <h2>Customers</h2>
                <!-- div with list of customers -->
                <div id="customers-list"></div>
            </div>

            <div>
                <h2>Employees</h2>
                <!-- div with list of employees -->
                <div id="employees-list"></div>
            </div>            
            <button id="logout-btn">Logout</button>
        </div>

        <!-- Email Input Modal -->
        <div id="emailModal" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id="modalTitle">Enter Email Address</h3>
                    <span class="close" onclick="closeEmailModal()">&times;</span>
                </div>
                <div class="modal-body">
                    <form id="emailForm">
                        <div class="form-group">
                            <label for="emailInput">Email:</label>
                            <input type="email" id="emailInput" name="email" required placeholder="Enter email address" style="width: 100%; padding: 8px; margin: 8px 0;">
                        </div>
                        <div class="form-actions" style="text-align: right; margin-top: 15px;">
                            <button type="button" onclick="closeEmailModal()" style="margin-right: 10px; padding: 8px 16px;">Cancel</button>
                            <button type="submit" style="padding: 8px 16px; background-color: #007bff; color: white; border: none; cursor: pointer;">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="/js/adminDashboard.js"></script>
        <script src="/js/logout.js"></script>
    </body>
</html>