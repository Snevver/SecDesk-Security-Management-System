<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secdesk Targets</title>

    <!-- CSS -->
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/index.css">
    <!-- <link rel="stylesheet" href="./public/css/targets.css"> -->
    <link rel="stylesheet" href="/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- JS -->
    <script src="/js/targets.js" defer></script>
    <script src="/js/bootstrap.js" defer></script>

    <!-- Favicon -->
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />
</head>

<body class="p-0 m-0 w-100">
    <!-- HEADER -->
    <header id="mainHeader" class="container-fluid text-white d-flex w-100 p-0">
        <div class="d-flex flex-nowrap justify-content-center align-items-center text-center w-100">
            <!-- Logo -->
            <div class="d-none d-md-block">
                <img src="images/secdesk-logo.webp" alt="SecDesk Logo" class="logo w-auto m-1 position-absolute top-0 start-0">
            </div>

            <!-- Title -->
            <div class="h-100 d-flex align-items-center">
                <h1 class="m-0 d-none d-lg-block text-center h-100">
                    <span>Security Management System</span>
                </h1>

                <img src="images/sms-simple.webp" alt="SecDesk Logo" class="img-fluid d-lg-none" style="max-height: 60px;">
            </div>
        </div>
    </header>

    <header class="d-flex">
        <div class="mb-1 mt-1 ms-1 me-1 targetListHeader subHeader1">
            <div class="h-100 w-100 fs-2 d-flex flex-nowrap justify-content-center align-items-center">
                <a href="/index.html.php" class="position-absolute start-0">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 18L9 12L15 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>

                <h3 class="headerTitle text-center m-0">Targets</h3>
            </div>
        </div>

        <!-- <div class="mb-1 mt-1 ms-0 me-1 targetListHeader subHeader2">
            <div class="h-100 w-100 fs-2 d-flex flex-nowrap justify-content-center align-items-center">
                <h3 class="headerTitle text-center m-0">Vulnerabilities</h3>
            </div>
        </div> -->

        <div class="mb-1 mt-1 ms-0 me-1 targetListHeader flex-grow-1">
            <div id="vulnerabilityDetails" class="h-100 fs-2 d-flex flex-nowrap justify-content-start align-items-center">
                <h3 class="text-center w-100 m-0">Vulnerability details</h3>
            </div>

        </div>
    </header>


    <!-- CONTENT -->
    <div id="targetsMain" class="d-flex h-100">
        <div id="targetSidebar" class="h-100 ms-1 me-0 overflow-auto">


            <div id="targetAccordion" class="target-list h-100 w-100">

            </div>
        </div>


        <!-- <div id="vulnerabilitySidebar" class="vulnerability-list">

        </div> -->

        <div id="vulnerabilityDetails" class="h-100 w-100 ms-1 me-1 overflow-auto">
            <div class="vulnerability-details h-100 w-100">

            </div>
        </div>

    </div>

    <!-- <div class="target-list"></div> -->
    <!-- <div class="vulnerability-list"></div> -->
    <!-- <div class="vulnerability-details"></div> -->
</body>
</html>