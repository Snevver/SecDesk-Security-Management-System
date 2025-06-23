# Transfer Information

### How does the application work?
First, the bootstrap.php file is included, which configures the application. Index.php is the front controller, which receives all HTTP requests. It starts by initializing the application and getting the URI. There is a big switch statement in the front controller, which checks if the endpoint of the request exists. If it does, it calls the appropriate functions and does what the endpoint is supposed to do. If the given endpoint does not exist, a 404 screen is loaded. In the front controller you can create and customize endpoints very easily.

### Authentication & Authorization
The application uses a role-based authentication system with three user types: 
- admin
- pentester 
- customer
  
All users are redirected to the root path ('/') after login, which then routes them to their appropriate dashboard based on their role using the `handleDashboardRoute()` function.

### Architecture
The application follows an MVC pattern with:
- **Controllers**: Handle business logic (AuthenticatorController, TargetController, etc.)
- **Views**: HTML templates for rendering pages
- **Models**: Database interactions through PDO
- **Application class**: Central routing and middleware management


### Hosting Information
This application is currently being hosted using the [Mijndomein](https://www.mijndomein.nl/) web hosting service, and the database is being hosted using [Supabase](https://supabase.com/). If you would like to host the database yourself, run the queries in import.sql to set up the tables. Do note that a new database will not include any user accounts that were stored in the previous database.

If we are still hosting the application online (both the application itself and the database) and you would like us to take it down so you can host it yourself, please contact us at one of these email addresses:

-   son@vdburg.site
-   svenhoeksema@hotmail.com
-   dorkinat@gmail.com
