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
		<header id="mainHeader" class="d-flex align-items-center justify-content-start justify-content-sm-start">
			<div class="d-flex flex-nowrap justify-content-between align-items-center text-center h-100 w-100">
				<!-- Logo -->
				<div class="d-none d-md-block">
					<img src="images/secdesk-logo.webp" alt="SecDesk Logo" class="logo p-0 w-auto">
				</div>

				<!-- Title -->
				<div class="h-100 d-flex align-items-center">
					<h1 class="m-0 d-none d-lg-flex text-center h-100 d-flex align-items-center">
                    <span>Security Management System</span>
                </h1>

                <img src="images/sms-simple.webp" alt="SecDesk Logo" class="img-fluid d-lg-none" style="max-height: 60px;">
            </div>

            <div class="d-none d-md-block" style="min-width: 101.4px;"></div>
        </div>
    </header>

    <!-- Target List Header -->
    <header class="d-flex">
        <div id="targetSubheader" class="collapse show mb-1 mt-1 ms-1 me-1 targetListHeader rounded subHeader1 d-flex justify-content-between align-items-center">
            <div class="h-100 w-100 fs-2 d-flex flex-nowrap justify-content-center align-items-center">
                <a href="/" class="back btn mb-0 ms-1 p-0"
                data-bs-toggle="tooltip"
                data-bs-placement="bottom"
                data-bs-title="Back to tests"
                data-bs-custom-class="custom-tooltip">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 18L9 12L15 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>

                <button id="mobileSidebarToggleBtn" class="back border-0 d-xl-none btn-outline-secondary mb-2 ms-0 mt-2"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#targetSidebarMobile"
                aria-controls="targetSidebarMobile">
                    <i class="bi bi-list fs-1"></i>
                </button>

                <h3 class="headerTitle d-none d-xl-block text-center m-0">Targets</h3>

                <button id="desktopSidebarToggleBtn" class="border-0 d-none d-xl-flex align-items-center m-0 p-1 ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#targetSidebarDesktop" aria-expanded="false" aria-controls="targetSidebarDesktop" title="Toggle Sidebar">
                    <div class="hamburger-icon position-relative d-flex align-items-center justify-content-center" id="icon">
                        <div class="icon-1 a" id="a"></div>
                        <div class="icon-2 c" id="b"></div>
                        <div class="icon-3 b" id="c"></div>
                        <div class="clear"></div>
                    </div>
                </button>
            </div>
        </div>

        <!-- Vulnerability Details Header -->
        <div class="mb-1 mt-1 ms-0 me-1 targetListHeader rounded flex-grow-1 d-flex justify-content-center align-items-center">
            <div id="vulnerabilityDetailsHeader" class="h-100 fs-2 d-flex flex-nowrap justify-content-start align-items-center text-center">
                <div class="d-flex justify-content-center align-items-center py-5 w-100">
                    Loading test name...
                <div class="spinner-border text-primary ms-2" role="status" aria-label="Loading"></div>
                </div>
            </div>
        </div>
    </header>


    <!-- CONTENT -->
    <div id="targetsMain" class="d-flex h-100 w-100">

    <!-- Sidebar hint for collapsed state -->
    <div id="sidebarHint" class="w-100 h-100 d-flex flex-column justify-content-center align-items-center d-none" style="min-height: 300px;">
        <svg width="48" height="48" fill="none" viewBox="0 0 48 48" class="mb-3">
            <path d="M24 38V10M24 10L14 20M24 10l10 10" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <div class="text-center">
            <h5 class="fw-bold mb-2" style="color: #6862ea;">Sidebar Collapsed</h5>
            <p class="mb-0" style="max-width: 350px;">
                To select a vulnerability, <span class="fw-semibold">open the sidebar</span> using the menu button above.
            </p>
        </div>
    </div>

        <!-- Left side container for sidebar -->
        <div class="d-flex me-1 w-100">
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

        <!-- Vulnerability details -->
        <div id="vulnerabilityDetails" class="h-100 d-flex w-100 ms-1 me-0 flex-grow-1 overflow-auto">
            <div class="vulnerability-details d-flex flex-column h-100 w-100 flex-grow-1">
                <div id="vulnDetailsPlaceholder" class="placeholder-container d-flex flex-column justify-content-center align-items-center h-100 w-100 flex-grow-1">
                    <!-- Optional illustration (SVG or image) -->
                    <img src="images/contentcard-dashboard-grey.webp" alt="Dashboard" class="mb-4" style="max-width: 220px;">

                    <svg width="64" height="64" fill="none" class="mb-3" viewBox="0 0 64 64">
                    <path d="M48 32H16M32 48L16 32l16-16" stroke="#6862ea" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>

                    <div class="text-center">
                        <h4 class="fw-bold mb-2" style="color: #6862ea;">No Vulnerability Selected</h4>
                        <p class="lead mb-0" style="max-width: 400px;">
                        To see vulnerability details, first <span class="fw-semibold">click on a target</span> and then <span class="fw-semibold">select a vulnerability</span> from the list.
                        </p>
                    </div>
                    </div>
            </div>
        </div>
</div>

</body>
</html>