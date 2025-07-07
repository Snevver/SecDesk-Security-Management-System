# SecDesk Security Management System

This project is a web application developed for [SecDesk](https://secdesk.com/), aimed at improving the presentation of vulnerabilities found in the infrastructure of the digital environments of clients.

## 📖 Overview

The SecDesk Security Management System provides:

-   A centralized database for storing accounts and penetration test results
-   Different access levels for customers, SecDesk workers (often refered to as "pentesters" in documentation, which is short for penetration tester), and administrators
-   A detailed overview for each vulnerability per target.

This web application makes it easy to document affected components, risk statements, recommended fixes, and track the resolution status of identified vulnerabilities.

## ⚙️ Dependencies

This project requires these packages in order to function:

-   BootstrapCSS
-   PopperJS
-   PHPMailer

Refer to step 4 of the 🔧 Local Setup section for information on how to install these packages.

## 🔧 Local Setup

### Prerequisites

You need a few things to be able to run this application locally:

1. **PHP** - The application should work with any version after 7.4, but we recommend using version 8.4.5. The 'PDO' extension also needs to be enabled, which is usually enabled by default.

2. **Database** - We used Supabase to host the database, but any other PostgreSQL database should work. More information about configuring the database can be found in the "Database Configuration" section.

3. **Web Server** - We used Apache, but something like Nginx should also work.

### Initialization

1. Install the dependencies. You can do this with NPM and composer by navigating to the root folder of the project and running:

    ```
    npm install
    ```

    and

    ```
    composer install
    ```

2. Create the log files. This can either be done manually, or with commands.

#### Manually

Create a folder named `logs` in the root directory of the project, and create two files named `info.log` and `error.log` inside that folder.

#### With Commands

Run the following PowerShell commands in the root directory of the project:

```powershell
    mkdir logs
    New-Item -ItemType File -Path "logs\info.log"
    New-Item -ItemType File -Path "logs\error.log"
```

## 🌐 Web Hosting

We used Apache and the Mijndomein webhosting service to host this application, but you can use any web hosting service that supports PHP and PostgreSQL.

## 📦 Database Configuration

We used Supabase to host the database, which is a PostgreSQL database. Below is a short guide on how to setup the database.

1. Log in to your Supabase account, or create a new account if you don't have one.

2. Create a new project and select PostgreSQL as the database type.

3. Create a `.env` file in the `Database` directory if this file does not already exist.

4. Go to the 'connect' tab in Supabase, and use the information under the 'session pooler' tab in the next step.

5. Use the following format for your `.env` file:

    ```
    HOST=your-db-host.com
    PORT=your_db_port
    DBNAME=your_database
    USER=your_username
    PASSWORD="your_password"
    ```

    Remember to use quotes around any values with special characters.

6. Run the Queries in the 'import.sql' file. This query creates the necessary tables, and creates an admin account. Do note that a new database will not include any data that was stored in the previous database.

## 🔒 Security

### 🤔 How We Secured Our App

Because this application handles sensitive information about the digital infrastructure of SecDesk's clients, we made sure the application was as secure as possible. Do note that we are not security experts, so we had AI help us implement some of the security features.

### 📄 What We Built

**Protecting Against Bad Input**

-   All user input gets checked and cleaned before it goes into our database
-   JavaScript functions prevent nasty scripts from running in the browser
-   There are limits on how long text can be to prevent abuse
-   HTML tags show up as text instead of running
-   Dangerous characters are removed

**Stopping XSS Attacks**

-   All user data gets encoded properly before we show it on the page
-   Our JavaScript code uses safe methods that won't execute malicious scripts
-   Content Security Policy headers stop the browser from running unauthorized scripts
-   There is different encoding depending on where we're showing the data

**Preventing Injection Attacks**

-   PDO statements are prepared to stop SQL injection attacks

**Access Control**

-   Different roles (Admin, Pentester, Customer) can only see what they're supposed to be able to see
-   The login system uses sessions to keep track of who is logged in
-   API endpoints check if you're allowed to access them
-   Passwords are handled securely

**Browser Protection**

-   Content Security Policy tells the browser what's allowed to run
-   Safe JavaScript practices that don't open security holes

### 🔧 How We Did It

We built the security in layers. This means other security measures are in place if one layer fails.

1. **Client-side**: Basic checks in the browser
2. **Server-side**: Serious validation and cleaning of all data
3. **Database**: Safe queries that can't be hacked
4. **Browser**: Security policies that block bad stuff

### 📋 What We Tested

We tested the security of the application with:

-   ✅ Script injection attempts
-   ✅ HTML injection
-   ✅ SQL injection (already protected by PDO)
-   ✅ Trying to bypass our input validation
-   ✅ Sending super long inputs
-   ✅ Weird characters and symbols

### 📚 More Info

If you want the technical details, check out:

-   `libraries/Ssms/InputValidator.php` - How we clean user input
-   `libraries/Ssms/OutputSanitizer.php` - How we safely display data

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
