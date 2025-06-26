<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>

        <!-- CSS -->
        <link rel="stylesheet" href="/css/bootstrap.css">
        <link rel="stylesheet" href="/css/login.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

        <!-- JS -->
        <script src="/js/login.js" defer></script>
        <script src="/js/bootstrap.js" defer></script>

        <!-- Favicon -->
        <link rel="icon" href="/favicon.ico" type="image/x-icon" />
    </head>

    <body class="p-0 m-0 w-100">
        <!-- HEADER -->
        <header id="mainHeader" class="d-flex align-items-center justify-content-center justify-content-sm-start">
            <img src="images/secdesk-logo.webp" alt="SecDesk Logo" class="logo p-0 w-auto">
        </header>

        <!-- Debugging !!! NOT TO BE USED IN PRODUCTION !!! -->
        <div id="debug" style="display: none;"></div>
        <div id="error-message"></div>

        <!-- LOGIN SECTION -->
        <main id="loginMain" class="container-fluid d-flex flex-column">
            <div id="mainFlexWrapper" class="row flex-grow-1 d-flex align-items-start">
                <div class="col-12 col-md-4 d-flex ps-xl-5 ps-3 pe-md-0 pb-3 pb-md-0">
                    <div id="loginWrapper" class="w-100 d-flex align-items-center justify-content-center">
                        <section id="loginSection" class="w-100 p-0 p-sm-4 p-xxl-5">
                            <div id="loginContainer" class="col-12 h-100 h-sm-0 d-flex flex-column justify-content-center w-100 align-items-center">
                                <img src="images/sms-simple.webp" alt="SecDesk Logo" class="img-fluid w-75 mb-sm-0 mb-0">

                                <form id="login-form" class="w-75 pb-lg-3">
                                    <div class="form-group mb-2">
                                        <label class="text-white" for="email">Email address</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter email">
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="text-white" for="password">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                                    </div>

                                    <button type="submit" id="login" class="btn w-100 mt-1">
                                    <span id="loginSpinner" class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                                    <span id="loginBtnText">Login</span>
                                    </button>
                                </form>
                            </div>
                        </section>
                    </div>
                </div>

                <!-- DESCRIPTION SECTION -->
                <div class="col-12 col-md-8 d-flex ps-3 pe-3 ps-xl-5 pe-xl-5">
                    <div id="contentWrapper" class="w-100 d-flex align-items-center justify-content-center">
                        <section id="homeContent" class="w-100 p-3">

                            <!-- Content Header -->
                            <header id="contentHeader" class="d-none d-sm-block">
                                <div class=" col-12 m-0 p-0">
                                    <div class="row align-items-center">
                                        <div class="col-12 d-flex justify-content-center align-items-center">
                                            <div class="text-white d-flex flex-column align-items-center">
                                                <h2 class="m-0 pb-3">
                                                    <span class="d-xxl-inline d-none">Achieve success with our</span>
                                                    <span class="highlight">Security Management System</span>
                                                </h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </header>

                            <hr class="m-0 d-none d-sm-block" style="border-top: 1px solid #ccc;">

                            <!-- Carousel -->
                            <div id="fadeCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="50000000">
                                <div class="carousel-indicators mb-0 mb-sm-1">
                                    <button type=" button" data-bs-target="#fadeCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                                    <button type="button" data-bs-target="#fadeCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                                    <button type="button" data-bs-target="#fadeCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                                    <button type="button" data-bs-target="#fadeCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
                                </div>

                                <div class="carousel-inner h-100">
                                    <!-- First slide -->
                                    <div class="carousel-item h-100 active d-flex justify-content-center align-items-center">
                                        <div class="row align-items-center homeContentCard">
                                            <div class="col-12 col-sm-5 p-0 d-flex justify-content-center align-items-center">
                                                <img src="images/contentcard-hackerman.webp" alt="Dashboard" class="contentCard mt-3">
                                            </div>
                                            <div class="col-12 col-sm-7 p-0 pe-6 pe-4 d-flex">
                                                <div class="contentCardText text-white d-flex flex-column">
                                                    <h2 class="fs-1">
                                                        <span class="highlight">Step One:</span> We'll hack you for
                                                        <span class="highlight">everything</span> you're worth
                                                    </h2>

                                                    <p class="fs-4">You pick the targets, and we do what we do best</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Second slide -->
                                    <div class="carousel-item h-100 d-flex justify-content-center align-items-center">
                                        <div class="row align-items-center homeContentCard">
                                            <div class="col-12 col-sm-5 p-0 d-flex justify-content-center align-items-center">
                                                <img src="images/contentcard-report.webp" alt="Dashboard" class="contentCard mt-3">
                                            </div>

                                            <div class="col-12 col-sm-7 p-0 pe-6 pe-4">
                                                <div class="contentCardText text-white d-flex flex-column">
                                                    <h2 class="fs-1">
                                                        <span class="highlight">Step Two:</span> We'll put <span class="highlight">special care</span> into what matters most
                                                    </h2>

                                                    <p class="fs-4">With a detailed report of your vulnerabilities</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Third slide -->
                                    <div class="carousel-item h-100 d-flex justify-content-center align-items-center">
                                        <div class="row align-items-center homeContentCard">
                                            <div class="col-12 col-sm-5 p-0 d-flex justify-content-center align-items-center">
                                                <img src="images/contentcard-sms-grey.webp" alt="Dashboard" class="contentCard mt-3">
                                            </div>

                                            <div class="col-12 col-sm-7 p-0 pe-6 pe-4">
                                                <div class="contentCardText text-white d-flex flex-column">
                                                    <h2 class="fs-1">
                                                        <span class="highlight">Step Three:</span> Enter Secdesk's <span
                                                            class="highlight">Security
                                                            Management
                                                            System
                                                        </span>
                                                    </h2>

                                                    <p class="fs-4">Our detailed report in a user-friendly environment</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Fourth slide -->
                                    <div class="carousel-item h-100 d-flex justify-content-center align-items-center">
                                        <div class="row align-items-center homeContentCard">
                                            <div class="col-12 col-sm-5 p-0 d-flex justify-content-center align-items-center">
                                                <img src="images/contentcard-dashboard-grey.webp" alt="Dashboard" class="contentCard mt-3">
                                            </div>

                                            <div class="col-12 col-sm-7 p-0 pe-6 pe-4">
                                                <div class="contentCardText text-white d-flex flex-column">
                                                    <h2 class="fs-1">
                                                        <span class="highlight">Step Four:</span> This is where <span
                                                            class="highlight">you</span>
                                                        make a
                                                        <span>big change!</span>
                                                    </h2>

                                                    <p class="fs-4">Track your progress till everything is fixed!
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </main>

        <!-- FOOTER -->
        <div class="container-fluid">
            <footer class="row row-cols-1 row-cols-sm-2 row-cols-md-5 py-5 border-top">
                <div class="col mb-3">
                    <a href="/" class="d-flex align-items-center mb-3 link-body-emphasis text-decoration-none"
                        aria-label="Bootstrap">
                        <svg class="bi me-2" width="40" height="32" aria-hidden="true">
                            <use xlink:href="#bootstrap"></use>
                        </svg>
                    </a>
                    <p class="text-body-secondary">© 2025 SecDesk</p>
                </div>

                <div class="col mb-3"></div>

                <div class="col mb-3">
                    <h5>SecDesk</h5>

                    <ul class="nav flex-column">
                        <li class="nav-item mb-2"><a href="https://secdesk.com/" class="nav-link p-0 text-body-secondary" data-wpel-link="internal">Secdesk.com</a></li>
                        <li class="nav-item mb-2"><a href="https://secdesk.com/ask-secdesk/" class="nav-link p-0 text-body-secondary" data-wpel-link="internal">Helpdesk</a></li>
                        <li class="nav-item mb-2"><a href="https://secdesk.com/blog/" class="nav-link p-0 text-body-secondary" data-wpel-link="internal">Blog</a></li>
                        <li class="nav-item mb-2"><a href="https://secdesk.com/contact-us/" class="nav-link p-0 text-body-secondary" data-wpel-link="internal">Contact</a></li>
                    </ul>
                </div>

                <div class="col mb-3">
                    <h5>Our core service</h5>

                    <ul class="nav flex-column">
                        <li class="nav-item mb-2">
                            <a href="https://secdesk.com/full-service-security/" class="nav-link p-0 text-body-secondary" data-wpel-link="internal">SecDesk Subscription</a>
                        </li>
                    </ul>
                </div>

                <div class="col mb-3">
                    <h5>Additional services</h5>

                    <ul class="nav flex-column">
                        <li class="nav-item mb-2"><a href="https://secdesk.com/penetration-test/" class="nav-link p-0 text-body-secondary" data-wpel-link="internal">Penetration test</a></li>
                        <li class="nav-item mb-2"><a href="https://secdesk.com/vulnerability-scanning/" class="nav-link p-0 text-body-secondary" data-wpel-link="internal">Vulnerability scanning</a></li>
                        <li class="nav-item mb-2"><a href="https://secdesk.com/phishing-campaign/" class="nav-link p-0 text-body-secondary" data-wpel-link="internal">Phishing campaign</a></li>
                        <li class="nav-item mb-2"><a href="https://secdesk.com/security-awareness-training-secdesk/" class="nav-link p-0 text-body-secondary" data-wpel-link="internal">Security awareness</a></li>
                    </ul>
                </div>
            </footer>
        </div>
    </body>
</html>