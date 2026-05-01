# EventHub Event Management System

EventHUbEvent Management System is a PHP and MySQL-based event management platform for organizing events, registering attendees, sending notifications, and managing users from separate admin and user dashboards.

## Overview

The application includes a public landing page, account registration and login, an admin dashboard for event and user management, and a user dashboard for browsing events, registering, managing profiles, and viewing notifications.

## Features

### Public Access
- Landing page with a modern marketing layout
- Sign up and login pages
- Responsive design for desktop and mobile browsers

### User Dashboard
- Browse upcoming events
- Register for events
- View registered events
- Cancel event registrations
- Update profile details
- Change password
- View event notifications

### Admin Dashboard
- Create, edit, and delete events
- View event lists and attendee lists
- Create, update, promote, and delete users
- Send reminder notifications to event attendees
- Export event data to CSV

## Technology Stack

- Backend: PHP
- Database: MySQL
- Frontend: HTML, CSS, JavaScript
- UI libraries: Font Awesome, Google Fonts

## Project Structure

```text
├── homepage.php            # Public landing page
├── loginphp.php            # Login form and authentication handler
├── Interface_register.php  # Sign-up form
├── admin_dashboard.php     # Admin dashboard UI
├── user_dashboard.php      # User dashboard UI
├── includes/
│   ├── db.php              # Database connection and schema bootstrap
│   ├── header.php          # Shared session/header helper
│   ├── admin_dashboard_logic.php
│   ├── user_dashboard_logic.php
│   ├── events.php
│   ├── cancelRegistration.php
│   ├── export_csv.php
│   ├── fetch_notifications.php
│   ├── register.php
│   └── send_notification.php
└── assets/
    ├── css/
    ├── js/
    └── img/
```

## Database

The database connection file, [includes/db.php](includes/db.php), creates the database automatically if it does not already exist and sets up these tables:

- `users`
- `events`
- `registrations`
- `notifications`

Database name: `event_management`

## Installation

1. Install a local PHP stack such as XAMPP, WAMP, or MAMP.
2. Copy the project folder to your web server directory, such as `htdocs`.
3. Make sure MySQL is running.
4. Open [homepage.php](homepage.php) in your browser.

On first load, the app will create the database and required tables automatically.

## First-Time Admin Setup

New sign-ups are created with the `user` role by default. To access the admin dashboard for the first time, set one account to `admin` in the `users` table through phpMyAdmin or another MySQL client. After that, admins can manage roles from the dashboard.

## Usage

- Public visitors can sign up from [Interface_register.php](Interface_register.php) and log in through [loginphp.php](loginphp.php).
- Regular users are redirected to [user_dashboard.php](user_dashboard.php).
- Admin users are redirected to [admin_dashboard.php](admin_dashboard.php).

## Security Notes

- Passwords are hashed with PHP’s `password_hash()`.
- Admin and user pages check session role before granting access.
- Some legacy helper scripts under `includes/` still use direct SQL interpolation, so the codebase should be reviewed before production deployment.

## Notes

- The project is intended for local development or classroom/demo use.
- Static assets and page styling live under [assets/](assets/).

## License

No license has been specified for this project.