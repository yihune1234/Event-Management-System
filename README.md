# EventHub - Modern Event Management System

EventHub is a professional, responsive, and robust web application built with PHP and MySQL. It streamlines the entire event lifecycle, from creation and registration to attendee management and communication.

## 🚀 Key Features

### For Administrators
- **Comprehensive Dashboard**: Real-time stats on events, registrations, and engagement.
- **Event Lifecycle Management**: Create, edit, and delete events with a modern UI.
- **User Management**: Promote users to admins or remove accounts securely.
- **Communication Hub**: Send mass notifications and reminders to event participants.
- **Data Portability**: Export event and attendee data to CSV for external reporting.

### For Users
- **Event Discovery**: Browse upcoming events through a sleek, card-based interface.
- **Easy Registration**: Quick sign-up process for events with automated profile tracking.
- **Personal Dashboard**: Track "My Registrations" and upcoming scheduled events.
- **Responsive Experience**: Fully optimized for mobile, tablet, and desktop viewing.

## 🛠️ Technology Stack
- **Backend**: PHP 8.x
- **Database**: MySQL (with automated schema generation)
- **Frontend**: Vanilla HTML5, Modern CSS (Flexbox/Grid), Vanilla JavaScript
- **Icons & Fonts**: FontAwesome 6, Google Fonts (Inter)

## 📁 Project Structure
```text
├── assets/                 # Static assets
│   ├── css/                # Design system and page styles
│   ├── js/                 # Frontend logic and interactions
│   └── img/                # Project images and backgrounds
├── includes/               # Core backend logic
│   ├── db.php              # Automated database connection & setup
│   ├── header.php          # Session and global navigation logic
│   ├── admin_dashboard_logic.php
│   └── user_dashboard_logic.php
├── admin_dashboard.php     # Admin interface
├── user_dashboard.php      # User interface
├── loginphp.php            # Secure authentication
├── Interface_register.php  # Unified registration form
└── homepage.php            # Landing page (Auto-initializes DB)
```

## ⚙️ Installation & Setup

1. **Prerequisites**: 
   - XAMPP / WAMP / MAMP or any PHP environment.
   - MySQL Server.

2. **Clone the Repository**:
   ```bash
   git clone https://github.com/yihune1234/Event-Management-System.git
   ```

3. **Deploy to local server**:
   Place the project folder in your `htdocs` or equivalent directory.

4. **Initialize Database**:
   Simply navigate to `homepage.php` in your browser. The system is designed to **automatically create the database and all required tables** on the first run.

5. **First Admin**:
   Register a new account via the sign-up page. To make it an admin, manually update the `role` field to `'admin'` in the `users` table via PHPMyAdmin for the first user. Subsequent admins can be promoted directly through the Admin Dashboard.

## 🔒 Security
- **Prepared Statements**: Used for all SQL queries to prevent SQL injection.
- **Password Hashing**: Uses PHP's `password_hash()` with BCRYPT for secure storage.
- **Role-Based Access Control (RBAC)**: Strict server-side checks for admin and user routes.

## 👥 System Contributors
Developed with ❤️ by the **SWE 3rd Year Vision Group**.

---
© 2026 EventHub Management. All rights reserved.