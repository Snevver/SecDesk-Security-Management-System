<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Dashboard</title>

		<!-- CSS -->
		<link rel="stylesheet" href="/css/bootstrap.css">
		<link rel="stylesheet" href="/css/index.css">

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
		<header id="mainHeader" class="d-flex align-items-center justify-content-start justify-content-sm-start">
			<div class="d-flex flex-nowrap justify-content-center justify-content-md-between align-items-center text-center h-100 w-100">
				<!-- Logo -->
				<div class="d-none d-md-block">
					<img src="images/secdesk-logo.webp" alt="SecDesk Logo" class="logo p-0 w-auto">
				</div>

				<!-- Title -->
				<div class="h-100 d-flex align-items-center">
					<h1 class="m-0 d-none d-lg-flex text-center h-100 d-flex align-items-center">
						<span>Security Management System</span>
					</h1>

					<img src="images/sms-simple.webp" alt="SecDesk Logo" class="h-100 img-fluid d-lg-none pt-2 pb-2">
				</div>

				<div class="d-none d-md-block" style="min-width: 101.4px;"></div>
			</div>
		</header>

		<!-- Sidebar Toggle Button for Mobile -->
		<button
		class="btn btn-light position-fixed top-0 start-0 m-2 d-md-none z-1030"
		type="button"
		style="width: 48px; height: 48px;"
		data-bs-toggle="collapse"
		data-bs-target="#sidebarCollapse"
		aria-controls="sidebarCollapse"
		aria-expanded="false"
		aria-label="Toggle sidebar"
		>
		<i class="bi bi-list fs-3"></i>
		</button>

		<!-- SIDEBAR -->
		<div class="content-wrapper d-flex">
			<div id="sidebarCollapse" class="collapse d-md-flex flex-column flex-shrink-0 bg-light">
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

					<!-- Change Password Button -->
					<li>
						<a href="#" class="nav-link py-3 border-bottom rounded-0" title="Settings" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-key" viewBox="0 0 16 16">
							<path d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8m4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5"/>
							<path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
							</svg>
						</a>
					</li>

					<!-- Contact Us Button -->
					<li>
						<a href="https://secdesk.com/contact-us/" target="_blank" class="nav-link py-3 border-bottom rounded-0" title="Contact Us">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-left-text" viewBox="0 0 16 16">
							<path d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4.414A2 2 0 0 0 3 11.586l-2 2V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
							<path d="M3 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3 6a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 6m0 2.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5"/>
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
			</div>

			<!-- DASHBOARD -->
			<main class="d-flex justify-content-center w-100 align-items-end">
				<section id="userDashboard" class="w-100">
					<div class="row d-flex text-align-center justify-content-center align-items-center">
						<section id="testList" class="container-fluid p-0 ms-3 me-3 rounded">

						<header id="testListHeader" class="mb-3 mt-3 ms-xxl-0 me-xxl-0 ms-3 me-3 rounded">
							<div class="h-100 fs-2 d-flex flex-nowrap justify-content-between align-items-center">
								<h2 class="mb-0 flex-grow-1 text-center" >Security Test Overview</h2>
							</div>
						</header>

						<div id="testListAccordion" class="accordion row ms-3 me-3"></div>
					</div>
				</section>
			</main>
		</div>

		<!-- Change Password Modal -->
		<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="changePasswordModalLabel">
							<i class="bi bi-key"></i> Change Password
						</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<form id="changePasswordForm">
							<div class="mb-3">
								<label for="currentPassword" class="form-label text-white">Current Password</label>
								<input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
							</div>
							<div class="mb-3">
								<label for="newPassword" class="form-label text-white">New Password</label>
								<input type="password" class="form-control" id="newPassword" name="newPassword" required>
								<div class="form-text">Password must be at least 8 characters long and contain uppercase, lowercase, and numbers.</div>
							</div>
							<div class="mb-3">
								<label for="confirmPassword" class="form-label text-white">Confirm New Password</label>
								<input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
							</div>
							<div id="changePasswordMessage"></div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn" data-bs-dismiss="modal">Cancel</button>
						<button type="submit" form="changePasswordForm" class="btn btn-modal-submit">Change Password</button>
					</div>
				</div>
			</div>
		</div>

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
						<button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn" data-bs-dismiss="modal">
							No
						</button>
						<button id="logout-btn" type="button"class="btn" data-bs-dismiss="modal">
							Yes
						</button>
					</div>
				</div>
			</div>
		</div>

		<div id="pageSpinner" class="position-fixed d-none top-0 start-0 w-100 h-100 justify-content-center align-items-center bg-white bg-opacity-50" style="z-index: 2000;">
			<div class="spinner-border spinner-lg text-primary" role="status" aria-label="Loading"></div>
		</div>

		<script src="/js/changePassword.js"></script>
	</body>
</html>