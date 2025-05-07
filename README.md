# SecDesk Security Management System

This project is a web application developed for SecDesk, aimed at improving the presentation of vulnerabilities found in the infrastructure of clients.

## 📖 Overview

The SecDesk Security Management System provides:

-   A centralized database for storing accounts and penetration test results
-   Different access levels for customers, SecDesk workers (often refered to as "pen testers" in documentation), and administrators
-   A detailed overview for each vulnerability per target.
-   Customer note functionality per vulnerabilities

This web application makes it easy to document affected components, risk statements, recommended fixes, and track the resolution status of identified vulnerabilities.

## 🔧 Setup

### Prerequisites

You need a few things to be able to run this application locally:

1. **PHP 7.4+** with the following extensions:

    - PDO (enabled by default)

2. **Database** - Configuration details in the Database section below

3. **Web Server** (Apache/Nginx) configured to serve the project

### Database Configuration

1. Create a `.env` file in the `Database` directory

2. Use the following format for your `.env` file:

    ```
    HOST=your-db-host.com
    PORT=your_db_port
    DBNAME=your_database
    USER=your_username
    PASSWORD="your_password"
    ```

    Note: Remember to use quotes around any values with special characters.

3. Run the database setup script:
    ```
    php Database/import.sql
    ```

### Sample Users

The setup script creates the following accounts in the database:

-   Admin: admin@example.com / admin
-   Customer: customer@example.com / customer
-   SecDesk: secdesk@example.com / secdesk

## ⚙️ Dependencies

This project does not have any dependencies that need to be installed seperately from cloning the repository at the moment.

## 📄 Transfer info

This application is currently being hosted using the [Mijndomein](https://www.mijndomein.nl/) webhosting service, and the database is being hosted using [Supabase](https://supabase.com/). If you would like to host the database yourself, run the queries in import.sql to set up the tables. Do note that a new database will not include any user accounts that were stored in the previous database.

## 🧑‍💻 Authors

### Son Bram van der Burg

Son is a full-stack web developer and was the SCRUM master during this project. He was also in charge of making sure the project was properly documented.

[Website](https://vdburg.site/) | [GitHub](https://github.com/Penguin-09) | [LinkedIn](https://www.linkedin.com/in/son-bram/)

### Sven Hoeksema

Sven is a back-end developer and was in charge of managing the database during this project. He wrote a large part of the back-end code.

[Website](https://snevver.nl/) | [GitHub](https://github.com/Snevver) | [LinkedIn](https://www.linkedin.com/in/sven-hoeksema/)

### Doncan Dayan

Don is a front-end web developer who designed and wrote the front-end of the application.

[GitHub](https://github.com/donbithub)
