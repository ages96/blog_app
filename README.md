
# Laravel Blog Application

This is a simple blog application built with Laravel, Inertia.js, and Vue.js, featuring user authentication, post management, commenting, and more.

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)
- [License](#license)

## Requirements

- PHP 8.2 or higher
- Composer
- MySQL (or compatible database)
- Node.js (for front-end dependencies)
- NPM or Yarn (for package management)

## Installation

Clone the repository:
   ```bash
   git clone https://github.com/yourusername/blog-app.git
   cd blog-app
   ```
     
Install PHP dependencies:

```bash
composer install
  ```
Copy the .env.example file to .env:

```bash
cp .env.example .env
  ```
Generate the application key:

```bash
php artisan key:generate
  ```
## Set up your database:

Create a new MySQL database (e.g., blog_app_new).
Update the .env file with your database credentials:
```makefile 
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_app_new
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password
Run migrations to set up the database tables:
```
  ```bash
php artisan migrate
  ```
## Install front-end dependencies:

  ```bash
npm install
  ```
  
Compile assets:
  ```bash
npm run dev
  ```
## Configuration
Environment Variables: 
Make sure to update the .env file with the appropriate settings for your local development environment.

Session Driver: 
Ensure your session driver is set correctly (e.g., SESSION_DRIVER=database) for handling user sessions.

Usage
Start the Laravel development server:

  ```bash
php artisan serve
  ```
  
## Access the application: 

Open your web browser and go to http://localhost:8000 or the specified URL.

User Registration and Login:

Navigate to the login or registration page to create a new user account or log in.

Manage Blog Posts:
After logging in, users can create, edit, delete, and repost blog posts. They can also comment on other users' posts.

## Testing
You can run the tests with the following command:

  ```bash
php artisan test
  ```