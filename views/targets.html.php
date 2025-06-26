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
                <img src="images/secdesk-logo.webp" alt="SecDesk Logo" class="logo p-0 w-auto m-2 position-absolute top-0 start-0">
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

    <!-- Target List Header -->
    <header class="d-flex">
        <div id="targetSubheader" class="collapse show mb-1 mt-1 ms-1 me-1 targetListHeader rounded subHeader1">
            <div class="h-100 w-100 fs-2 d-flex flex-nowrap justify-content-center align-items-center">
                <a href="/" class="position-absolute back btn start-0 mb-0 ms-1 ms-xxl-3 p-1"
                data-bs-toggle="tooltip"
                data-bs-placement="bottom"
                data-bs-title="Back to tests"
                data-bs-custom-class="custom-tooltip">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 18L9 12L15 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <!-- <span class="d-none d-xl-block">Back</span> -->
                </a>

                <button id="mobileSidebarToggleBtn" class="back border-0 d-xl-none btn-outline-secondary mb-2 ms-0 mt-2"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#targetSidebarMobile"
                aria-controls="targetSidebarMobile">
                    <i class="bi bi-list fs-1"></i>
                </button>

                <h3 class="headerTitle d-none d-xl-block text-center m-0">Targets</h3>
            </div>
        </div>
        <div class="mb-1 mt-1 ms-0 me-1 targetListHeader rounded flex-grow-1 position-relative">
        <button id="desktopSidebarToggleBtn" class="back border-0 d-none d-xl-flex btn-outline-secondary mb-2 position-absolute top-0 start-0 ms-0 mt-2" type="button" data-bs-toggle="collapse" data-bs-target="#targetSidebarDesktop" aria-expanded="false" aria-controls="targetSidebarDesktop">
            <svg class="arrow-icon" width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15 18L9 12L15 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
            <div id="vulnerabilityDetails" class="h-100 fs-2 d-flex flex-nowrap justify-content-start align-items-center">
                <h3 class="text-center w-100 m-0">Vulnerability details</h3>
            </div>
        </div>
    </header>


    <!-- CONTENT -->
<div id="targetsMain" class="d-flex h-100">
    <!-- Left side container for search and sidebar -->
    <div class="d-flex me-1">

<!-- Desktop Sidebar (visible ≥xl) -->
<div id="targetSidebarDesktop" class="targetSidebar collapse show flex-grow-1 ms-1 overflow-auto">
  <div id="targetAccordionDesktop" class="accordion accordion-flush p-0 m-0 target-list h-100 w-100"></div>
</div>

<!-- Offcanvas Sidebar (visible <xl) -->
<div id="targetSidebarMobile" class="targetSidebar offcanvas offcanvas-start d-xl-none"
     tabindex="-1" aria-labelledby="targetSidebarMobileLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="targetSidebarMobileLabel">Targets</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body p-0">
    <div id="targetAccordionMobile" class="accordion accordion-flush target-list h-100 w-100"></div>
  </div>
</div>

    <!-- Vulnerability details (right side) -->
    <div id="vulnerabilityDetails" class="h-100 w-100 ms-1 me-0 overflow-auto">
        <div class="vulnerability-details h-100 w-100">
        </div>
    </div>
</div>

</body>
</html>