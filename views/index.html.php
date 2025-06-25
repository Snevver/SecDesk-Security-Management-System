<!-- Don zou je de test display aan kunnen passen zodat het niet meer op een dropdown lijkt?:) -->

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Dashboard</title>

		<!-- CSS -->
		<link rel="stylesheet" href="/css/index.css">
		<link rel="stylesheet" href="/css/bootstrap.css">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

		<!-- JS -->
		<script src="/js/bootstrap.js" defer></script>
		<script src="/js/index.js" defer></script>
		<script src="/js/logout.js" defer></script>

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

		<!-- SIDEBAR -->
		<div class="content-wrapper d-flex">
			<div id="sidebar" class="d-flex flex-column flex-shrink-0 bg-light">
				<!-- Logo only -->
				<a href="/" class="d-block p-3 link-dark text-decoration-none" title="Icon-only" data-bs-toggle="tooltip" data-bs-placement="right">
					<img src="images/logo-only.webp" alt="SecDesk Logo" class="logo-only">
				</a>

				<ul class="nav nav-pills nav-flush flex-column mb-auto text-center">
					<!-- Dashboard Button -->
					<li class="nav-item">
						<a href="#" class="nav-link active py-3 border-bottom rounded-0" aria-current="page" title="Home" data-bs-toggle="tooltip" data-bs-placement="right">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-door" viewBox="0 0 16 16">
								<path d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293zM2.5 14V7.707l5.5-5.5 5.5 5.5V14H10v-4a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v4z" />
							</svg>
						</a>
					</li>

					<!-- Settings Button -->
					<li>
						<a href="#" class="nav-link py-3 border-bottom rounded-0" title="Settings" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
								<path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0" />

								<path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z" />
							</svg>
						</a>
					</li>
				</ul>

				<!-- Logout Button -->
				<a href="#" data-bs-toggle="modal" data-bs-target="#logoutModal" class="d-flex align-items-center justify-content-center p-3 link-dark text-decoration-none">
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
						<path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
						<path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
					</svg>
				</a>

				<!-- Logout Modal -->
				<div class="modal fade" id="logoutModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h2 class="modal-title fs-5" id="logoutModalLabel">
									You're logging out
								</h2>

								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>

							<div class="modal-body">
								<p>Are you sure you want to log out?</p>
							</div>

							<div class="modal-footer">
								<button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn text-white" data-bs-dismiss="modal">
									No
								</button>
								<button id="logout-btn" type="button"class="btn text-white" data-bs-dismiss="modal">
									Yes
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- DASHBOARD -->
			<main class="d-flex justify-content-center w-100 align-items-end">
				<section id="userDashboard">
					<div class="row d-flex text-align-center justify-content-center align-items-center">
						<section id="testList" class="container-fluid p-0">

						<header id="testListHeader" class="mb-3 mt-3 ms-xxl-0 me-xxl-0 ms-3 me-3">
							<div class="h-100 fs-2 d-flex flex-nowrap justify-content-between align-items-center">
								<h2 class="mb-0 flex-grow-1 text-center" >Security Test Overview</h2>
							</div>
						</header>

						<div id="testListAccordion" class="accordion row ms-3 me-3"></div>
					</div>
				</section>
			</main>		</div>

		<!-- Change Password Modal -->
		<div class="modal fade" id="changePasswordModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h2 class="modal-title fs-5" id="changePasswordModalLabel">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-key-fill me-2" viewBox="0 0 16 16">
								<path d="M3.5 11.5a3.5 3.5 0 1 1 3.163-5h1.837a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1h-1a.5.5 0 0 1-.5-.5v-1h-1a.5.5 0 0 1-.5-.5v-1h-1a.5.5 0 0 1-.5-.5z"/>
								<path d="M12 10.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/>
							</svg>
							Change Password
						</h2>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>

					<form id="changePasswordForm">
						<div class="modal-body">
							<div class="mb-3">
								<label for="currentPassword" class="form-label text-white">Current Password</label>
								<input type="password" class="form-control" id="currentPassword" required>
							</div>
							<div class="mb-3">
								<label for="newPassword" class="form-label text-white">New Password</label>
								<input type="password" class="form-control" id="newPassword" required>
							</div>
							<div class="mb-3">
								<label for="confirmPassword" class="form-label text-white">Confirm New Password</label>
								<input type="password" class="form-control" id="confirmPassword" required>
							</div>
							<div id="passwordError" class="text-danger d-none"></div>
							<div id="passwordSuccess" class="text-success d-none"></div>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary" id="changePasswordSubmit">Change Password</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<script src="/js/changePassword.js"></script>
	</body>
</html>