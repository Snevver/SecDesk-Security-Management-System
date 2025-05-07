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

Start by running `npm install` in the root directory to install all dependencies.

This application uses postgress. If you use Apache, you will need to turn on the "pgsql" extension. You can do this by going to the php.ini config file of Apache and uncommenting the line `extension=pgsql`

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
