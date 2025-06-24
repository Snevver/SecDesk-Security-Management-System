<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secdesk Targets</title>

    <!-- CSS -->
    <link rel="stylesheet" href="/css/targets.css">
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
                <img src="images/secdesk-logo.webp" alt="SecDesk Logo" class="logo p-0 w-auto m-1 position-absolute top-0 start-0">
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
                <a href="/" class="position-absolute back btn start-0 mb-0 ms-3 p-1"
                data-bs-toggle="tooltip"
                data-bs-placement="bottom"
                data-bs-title="Back to tests"
                data-bs-custom-class="custom-tooltip">
                    <svg class="d-xl-none" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 18L9 12L15 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="d-none d-xl-block">Back</span>
                </a>

                <h3 class="headerTitle text-center m-0">Targets</h3>
            </div>
        </div>

        <div class="mb-1 mt-1 ms-0 me-1 targetListHeader flex-grow-1">
            <div id="vulnerabilityDetails" class="h-100 fs-2 d-flex flex-nowrap justify-content-start align-items-center">
                <h3 class="text-center w-100 m-0">Vulnerability details</h3>
            </div>

        </div>
    </header>


    <!-- CONTENT -->
<div id="targetsMain" class="d-flex h-100">
    <!-- Left side container for search and sidebar -->
    <div class="d-flex flex-column me-1">
        <!-- Search section -->
        <!-- <div class="mb-1 mt-0 ms-1 targetListHeader subHeader1">
            <div class="h-100 w-100 fs-2 d-flex flex-nowrap justify-content-center align-items-center">
                <div class="d-xxl-flex d-none flex-wrap justify-content-center align-items-center">
                        <input class="form-control me-1 w-50" type="search" placeholder="Search" aria-label="Search">
                        <button class="w-auto btn my-2 my-sm-0" type="submit">Search</button>
                    </form>
                </div>
            </div>
        </div> -->

        <!-- Target sidebar -->
        <div id="targetSidebar" class="flex-grow-1 ms-1 overflow-auto">
            <div id="targetAccordion" class="accordion accordion-flush p-0 m-0 target-list h-100 w-100">
            </div>
        </div>
    </div>

    <!-- Vulnerability details (right side) -->
    <div id="vulnerabilityDetails" class="h-100 w-100 ms-0 me-1 overflow-auto">
        <div class="vulnerability-details h-100 w-100">
        </div>
    </div>
</div>

</body>
</html>