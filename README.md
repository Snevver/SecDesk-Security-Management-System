# SecDesk Security Management System

This project is a web application developed for SecDesk, aimed at improving the presentation of vulnerabilities found in the infrastructure of the digital environments of clients.

## 📖 Overview

The SecDesk Security Management System provides:

-   A centralized database for storing accounts and penetration test results
-   Different access levels for customers, SecDesk workers (often refered to as "pentesters" in documentation, short for penetration tester), and administrators
-   A detailed overview for each vulnerability per target.
-   Customer comment functionality for vulnerabilities

This web application makes it easy to document affected components, risk statements, recommended fixes, and track the resolution status of identified vulnerabilities.

## 🔧 Setup

### Prerequisites

You need a few things to be able to run this application locally:

1. **PHP 7.4+** with the following extensions:

    - PDO (enabled by default)

2. **Database** - Configuration details in the Database section below

3. **Web Server** (Apache/Nginx) configured to serve the project

### Database Configuration

1. Create a `.env` file in the `Database` directory if this file does not already exist

2. Use the following format for your `.env` file:

    ```
    HOST=your-db-host.com
    PORT=your_db_port
    DBNAME=your_database
    USER=your_username
    PASSWORD="your_password"
    ```

    Remember to use quotes around any values with special characters.

3. Run the database setup script:

    ```
    php Database/import.sql
    ```

4. Install any dependencies. You can do this with NPM by navigating to the root folder of the project and running:
    ```
    npm install
    ```

## ⚙️ Dependencies

This project requires these packages in order to function:

-   BootstrapCSS
-   PopperJS

Refer to step 4 of the 🔧 Setup section for information on how to install these packages.

## 🧑‍💻 Authors

### Son Bram van der Burg

Son is a full-stack web developer and was the SCRUM master during this project. He was also in charge of making sure the project was properly documented.

[Website](https://vdburg.site/) | [GitHub](https://github.com/Penguin-09) | [LinkedIn](https://www.linkedin.com/in/son-bram/) | son@vdburg.site

### Sven Hoeksema

Sven is a back-end developer and was in charge of managing the database during this project. He wrote a large part of the back-end code.

[Website](https://snevver.nl/) | [GitHub](https://github.com/Snevver) | [LinkedIn](https://www.linkedin.com/in/sven-hoeksema/) | svenhoeksema@hotmail.com

### Doncan Dayan

Don is a front-end web developer who designed and wrote the front-end of the application.

[GitHub](https://github.com/donbithub) | dorkinat@gmail.com
