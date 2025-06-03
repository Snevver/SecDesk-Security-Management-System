<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Dashboard</title>
        <script src="/js/adminDashboard.js"></script>

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

        <script src="/js/logout.js"></script>
    </body>
</html>