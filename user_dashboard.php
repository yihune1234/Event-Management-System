<?php
require 'includes/user_dashboard_logic.php';
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Dashboard - EventHub</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Design System CSS -->
    <link rel="stylesheet" href="assets/css/modern-design-system.css?v=2.2" />
    
    <style>
        :root {
            --sidebar-width: 280px;
            --header-height: 70px;
        }

        body {
            background-color: var(--bg-secondary);
            font-family: 'Inter', sans-serif;
        }

        .dashboard-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles (Simplified from Admin) */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--gray-900);
            color: white;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar-logo {
            padding: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-content {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            color: rgba(255, 255, 255, 0.7);
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .nav-item:hover, .nav-item.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .nav-item.active {
            background: var(--primary-600);
        }

        /* Main Content */
        .main-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 2.5rem;
        }

        .section-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: var(--shadow-sm);
            margin-bottom: 2.5rem;
            border: 1px solid var(--border-color);
        }

        .event-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .event-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            transition: transform 0.3s ease;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .event-card-header {
            padding: 1.5rem;
            background: linear-gradient(135deg, var(--primary-600), var(--primary-700));
            color: white;
        }

        .event-card-body {
            padding: 1.5rem;
        }

        .event-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .btn-register-small {
            width: 100%;
            margin-top: 1rem;
        }

        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-wrapper { margin-left: 0; padding: 1.5rem; }
            .mobile-toggle { display: block; }
        }

        .mobile-toggle {
            display: none;
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 60px;
            height: 60px;
            background: var(--primary-600);
            color: white;
            border-radius: 50%;
            border: none;
            z-index: 1001;
            box-shadow: var(--shadow-lg);
        }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-logo">
                <i class="fas fa-calendar-check fa-2x text-primary"></i>
                <span class="h4 mb-0 fw-bold">EventHub</span>
            </div>
            <div class="sidebar-content">
                <a href="#events" class="nav-item active" onclick="showSection('events', this)">
                    <i class="fas fa-search"></i>
                    <span>Browse Events</span>
                </a>
                <a href="#my-events" class="nav-item" onclick="showSection('my-events', this)">
                    <i class="fas fa-ticket-alt"></i>
                    <span>My Registrations</span>
                </a>
                <a href="#notifications" class="nav-item" onclick="showSection('notifications', this)">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                </a>
                <a href="#profile" class="nav-item" onclick="showSection('profile', this)">
                    <i class="fas fa-user-circle"></i>
                    <span>My Profile</span>
                </a>
                <a href="loginphp.php" class="nav-item mt-auto text-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <main class="main-wrapper">
            <header class="mb-5 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 fw-bold text-gray-900">User Dashboard</h1>
                    <p class="text-gray-500">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                </div>
            </header>

            <!-- Browse Events Section -->
            <section id="events" class="content-section">
                <div class="section-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="mb-0">Upcoming Events</h3>
                        <div class="d-flex gap-2">
                             <?php if(isset($_GET['success'])): ?>
                                <span class="badge bg-success p-2">Registration Successful!</span>
                             <?php endif; ?>
                             <?php if(isset($_GET['error'])): ?>
                                <span class="badge bg-danger p-2">Already Registered!</span>
                             <?php endif; ?>
                        </div>
                    </div>
                    <div class="event-grid">
                        <?php foreach ($events as $event): 
                            $isRegistered = false;
                            foreach($scheduledEvents as $se) {
                                if($se['event_id'] == $event['id']) $isRegistered = true;
                            }
                        ?>
                            <div class="event-card">
                                <div class="event-card-header">
                                    <h5 class="mb-0"><?php echo htmlspecialchars($event['title']); ?></h5>
                                </div>
                                <div class="event-card-body">
                                    <div class="event-info">
                                        <i class="fas fa-map-marker-alt text-primary"></i>
                                        <span><?php echo htmlspecialchars($event['event_location']); ?></span>
                                    </div>
                                    <div class="event-info">
                                        <i class="fas fa-calendar-day text-primary"></i>
                                        <span><?php echo date('M d, Y', strtotime($event['start_date'])); ?></span>
                                    </div>
                                    <div class="event-info">
                                        <i class="fas fa-user-tie text-primary"></i>
                                        <span><?php echo htmlspecialchars($event['event_organizer']); ?></span>
                                    </div>
                                    <p class="text-muted small mb-4" style="height: 4.5em; overflow: hidden;"><?php echo htmlspecialchars(substr($event['description'], 0, 120)) . '...'; ?></p>
                                    
                                    <?php if($isRegistered): ?>
                                        <button class="btn btn-success btn-register-small" disabled>
                                            <i class="fas fa-check-circle me-1"></i> Registered
                                        </button>
                                    <?php else: ?>
                                        <form method="POST">
                                            <input type="hidden" name="action" value="register">
                                            <input type="hidden" name="event" value="<?php echo $event['id']; ?>">
                                            <button type="submit" class="btn btn-primary btn-register-small">Register Now</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <!-- My Registrations Section -->
            <section id="my-events" class="content-section" style="display:none;">
                <div class="section-card">
                    <h3 class="mb-4">My Scheduled Events</h3>
                    <div class="table-responsive">
                        <table class="table hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Event Title</th>
                                    <th>Location</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($scheduledEvents)): ?>
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No events scheduled yet.</td></tr>
                                <?php endif; ?>
                                <?php foreach ($scheduledEvents as $event): ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo htmlspecialchars($event['title']); ?></td>
                                        <td><?php echo htmlspecialchars($event['event_location']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($event['start_date'])); ?></td>
                                        <td><?php echo date('H:i', strtotime($event['start_date'])); ?></td>
                                        <td class="text-center">
                                            <form method="POST" onsubmit="return confirm('Cancel registration for this event?');">
                                                <input type="hidden" name="action" value="cancel_registration">
                                                <input type="hidden" name="reg_id" value="<?php echo $event['reg_id']; ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-times me-1"></i> Cancel
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Notifications Section -->
            <section id="notifications" class="content-section" style="display:none;">
                <div class="section-card">
                    <h3 class="mb-4">Recent Notifications</h3>
                    <div class="notification-list">
                        <?php if(empty($notifications)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-bell-slash fa-3x text-light mb-3"></i>
                                <p class="text-muted">No new notifications.</p>
                            </div>
                        <?php endif; ?>
                        <?php foreach($notifications as $notif): ?>
                            <div class="p-3 border-bottom d-flex gap-3">
                                <div class="bg-primary bg-opacity-10 p-2 rounded-circle text-primary" style="height: fit-content;">
                                    <i class="fas fa-bullhorn"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($notif['event_title']); ?></h6>
                                        <small class="text-muted"><?php echo date('M d, H:i', strtotime($notif['sent_at'])); ?></small>
                                    </div>
                                    <p class="mb-0 text-secondary"><?php echo htmlspecialchars($notif['message']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <!-- Profile Management Section -->
            <section id="profile" class="content-section" style="display:none;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="section-card">
                            <h3 class="mb-4">Edit Profile</h3>
                            <form method="POST">
                                <input type="hidden" name="action" value="update_profile">
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user_profile['username']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user_profile['email']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($user_profile['phone_number'] ?? ''); ?>">
                                </div>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="section-card">
                            <h3 class="mb-4">Security</h3>
                            <form method="POST">
                                <input type="hidden" name="action" value="update_password">
                                <div class="mb-3">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="new_password" class="form-control" minlength="8" required>
                                </div>
                                <button type="submit" class="btn btn-outline-primary">Update Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <button class="mobile-toggle" onclick="document.getElementById('sidebar').classList.toggle('active')">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <script>
        function showSection(id, el) {
            document.querySelectorAll('.content-section').forEach(s => s.style.display = 'none');
            document.getElementById(id).style.display = 'block';
            document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
            el.classList.add('active');
            if (window.innerWidth <= 1024) document.getElementById('sidebar').classList.remove('active');
        }
    </script>
</body>
</html>

