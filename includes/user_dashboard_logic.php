<?php
require 'db.php';
session_start();

// Check if the user is logged in and is a user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: loginphp.php");
    exit();
}

// Initialize variables
$events = [];
$scheduledEvents = [];
$notifications = [];
$user_id = $_SESSION['user_id'];
$user_profile = [];

// Fetch user profile info
$profileQuery = "SELECT username, email, phone_number, created_at FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $profileQuery);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user_profile = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Fetch all available events
$eventsQuery = "SELECT id, title, event_location, start_date, end_date, description, event_organizer FROM events ORDER BY start_date ASC";
$eventsResult = mysqli_query($conn, $eventsQuery);
if ($eventsResult) {
    while ($row = mysqli_fetch_assoc($eventsResult)) {
        $events[] = $row;
    }
}

// Fetch scheduled events for the logged-in user
$scheduledEventsQuery = "SELECT r.id as reg_id, e.id as event_id, e.title, e.description, e.event_location, e.start_date, e.end_date 
                         FROM registrations r 
                         JOIN events e ON r.event_id = e.id 
                         WHERE r.user_id = ?
                         ORDER BY e.start_date ASC";
$stmt = mysqli_prepare($conn, $scheduledEventsQuery);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$scheduledEventsResult = mysqli_stmt_get_result($stmt);
if ($scheduledEventsResult) {
    while ($row = mysqli_fetch_assoc($scheduledEventsResult)) {
        $scheduledEvents[] = $row;
    }
}

// Fetch notifications for the user's events
$notifQuery = "SELECT n.message, n.sent_at, e.title as event_title 
               FROM notifications n 
               JOIN events e ON n.event_id = e.id 
               JOIN registrations r ON r.event_id = e.id 
               WHERE r.user_id = ? 
               ORDER BY n.sent_at DESC LIMIT 10";
$stmt = mysqli_prepare($conn, $notifQuery);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$notifResult = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($notifResult)) {
    $notifications[] = $row;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POSTAction') {
    // This is a placeholder for better routing if needed
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'register' && isset($_POST['event'])) {
        $event_id = $_POST['event'];
        $email = $user_profile['email'];
        $phone_number = $user_profile['phone_number'] ?? '0000000000';

        // Check if already registered
        $checkQuery = "SELECT id FROM registrations WHERE event_id = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $checkQuery);
        mysqli_stmt_bind_param($stmt, "ii", $event_id, $user_id);
        mysqli_stmt_execute($stmt);
        if (mysqli_num_rows(mysqli_stmt_get_result($stmt)) > 0) {
            header("Location: user_dashboard.php?error=already_registered");
            exit();
        }

        $query = "INSERT INTO registrations (event_id, user_id, email, phone_number) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "iiss", $event_id, $user_id, $email, $phone_number);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: user_dashboard.php?success=registered");
            exit();
        }
    } 
    elseif ($action == 'cancel_registration' && isset($_POST['reg_id'])) {
        $reg_id = $_POST['reg_id'];
        $query = "DELETE FROM registrations WHERE id = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ii", $reg_id, $user_id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: user_dashboard.php?action=cancelled");
            exit();
        }
    }
    elseif ($action == 'update_profile') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone_number']);
        
        $query = "UPDATE users SET username = ?, email = ?, phone_number = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssi", $username, $email, $phone, $user_id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['username'] = $username;
            header("Location: user_dashboard.php?action=profile_updated");
            exit();
        }
    }
    elseif ($action == 'update_password') {
        $current = $_POST['current_password'];
        $new = $_POST['new_password'];
        
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        
        if (password_verify($current, $res['password'])) {
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $update = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $update);
            mysqli_stmt_bind_param($stmt, "si", $hashed, $user_id);
            mysqli_stmt_execute($stmt);
            header("Location: user_dashboard.php?action=password_changed");
            exit();
        } else {
            header("Location: user_dashboard.php?error=invalid_password");
            exit();
        }
    }
}
?>