# 📄 Transfer Information

This document serves as a guide for developers inheriting this project. It has information about the application's architecture, key components, and contact details. For instructions on running or hosting the application, please see the [README.md](README.md) file.

### ❓ How does the application work?

First, the bootstrap.php file is included (This file is not related to the Bootstrap CSS framework, despite it sharing the same name), which configures the application. Index.php is the front controller, which receives all HTTP requests. It starts by initializing the application and getting the URI. There is a big switch statement in the front controller, which checks if the endpoint of the request exists. If it does, it calls the appropriate functions. If the given endpoint does not exist, a 404 screen is loaded.

### 👤 Authentication & Authorization

The application uses a role-based authentication system with three user types:

-   admin
-   pentester
-   customer

All users are redirected to the root path ('/') after login, which then routes them to their appropriate dashboard based on their role using the `handleDashboardRoute()` function.

### 🏢 Architecture

The application partially follows an MVC pattern with:

-   **Controllers**: Handle business logic (AuthenticatorController, TargetController, etc.)
-   **Views**: HTML templates for rendering pages
-   **Application class**: Central routing and middleware management

### 🌐 Hosting Information

This application is currently being hosted using the [Mijndomein](https://www.mijndomein.nl/) web hosting service, and the database is being hosted using [Supabase](https://supabase.com/). For information on how to host the application or the database, please refer to the [README.md](README.md) file.

### 👟 Getting Started

Users with the admin role can create new users, including new admins. A basic admin account has already been created (if you succesfully imported the database) with the following credentials:

-   **Username**: `youri@secdesk.com`
-   **Password**: `secdesk123`

We recommend changing the password after logging in for the first time.

#### Contact Information

If we are still hosting the application online (both the application itself and the database) and you would like us to take it down so you can host it yourself, please contact us at one of these email addresses:

-   son@vdburg.site
-   svenhoeksema@hotmail.com
-   dorkinat@gmail.com
