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

4. Install any dependencies. You can do this with NPM and composer by navigating to the root folder of the project and running:
    ```
    npm install
    ```
    and
    ```
    composer install
    ```

5. Create log files by running the following commands in the root folder:
    ```
    mkdir logs
    New-Item -ItemType File -Path "logs\info.log"
    New-Item -ItemType File -Path "logs\error.log"
    ```

## ⚙️ Dependencies

This project requires these packages in order to function:

-   BootstrapCSS
-   PopperJS
-   PHPMailer

Refer to step 4 of the 🔧 Setup section for information on how to install these packages.



## 🔒 Security

### How We Secured Our App

Since we're dealing with penetration testing data and vulnerability information, we knew security was super important. But honestly, none of us (Sven, Son, or Don) are cybersecurity experts yet, so we got a lot of help from AI to make sure we did this right.

### 🛡️ What We Built

**Protecting Against Bad Input**
- We made sure all user input gets checked and cleaned before it goes into our database
- Added JavaScript functions that prevent nasty scripts from running in the browser
- Set limits on how long text can be so nobody can crash our app
- Made sure HTML tags just show up as text instead of actually running

**Stopping XSS Attacks**
- All user data gets encoded properly before we show it on the page
- Our JavaScript code uses safe methods that won't execute malicious scripts
- Added Content Security Policy headers to stop the browser from running unauthorized scripts
- Different encoding depending on where we're showing the data

**Preventing Injection Attacks**
- **SQL Injection**: We use PDO prepared statements (this was already working)
- **HTML Injection**: HTML tags just show up as text now
- **Script Injection**: Malicious JavaScript gets neutralized 
- **Other weird stuff**: We strip out dangerous characters

**Access Control**
- Different roles (Admin, Pentester, Customer) can only see what they're supposed to
- Proper login system with sessions
- API endpoints check if you're allowed to access them
- Passwords are handled securely

**Browser Protection**
- Content Security Policy tells the browser what's allowed to run
- Safe JavaScript practices that don't open security holes

### 🔧 How We Did It

We built security in layers so if one thing fails, others still protect the app:

1. **Client-side**: Basic checks in the browser
2. **Server-side**: Serious validation and cleaning of all data
3. **Database**: Safe queries that can't be hacked
4. **Browser**: Security policies that block bad stuff

### 📋 What We Tested

We tried to break our own app with:
- ✅ Script injection attempts
- ✅ HTML injection 
- ✅ SQL injection (already protected by PDO)
- ✅ Trying to bypass our input validation
- ✅ Sending super long inputs
- ✅ Weird characters and symbols

### 📚 More Info

If you want the technical details, check out:
- `libraries/Ssms/InputValidator.php` - How we clean user input
- `libraries/Ssms/OutputSanitizer.php` - How we safely display data

### ⚠️ Real Talk About Security

**Important**: We implemented all this security with a lot of help from AI (mainly Claude), since we're still learning about cybersecurity. We followed best practices and tested everything we could think of, but we're not security experts.

---


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

