<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Employee Dashboard</title>
        <script src="/js/employeeDashboard.js"></script>
    </head>
    
    <body>
        <div class="container">
            <h1>Employee Dashboard</h1>

            <a href="/edit" id="create-report-btn">Create New Report</a>

            <div>
                <h2>Logged in as <span id="email"><?=$_SESSION['email'] ?? "Unknown"?></span></h2>
            </div>            <div>
                <h2>Tests in progress</h2>
                <!-- div with list of tests in progress -->
                 <div id="reports-in-progress"></div>
            </div>

            <div>
                <h2>Completed tests</h2>
                <!-- div with list of completed tests -->
                <div id="completed-reports"></div>
            </div>

            <button id="logout-btn">Logout</button>
        </div>

        <script src="/js/logout.js"></script>
    </body>
</html>