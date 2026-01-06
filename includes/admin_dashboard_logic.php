<?php
require 'db.php';
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: loginphp.php");
    exit();
}

// Initialize error messages
$errors = array(
    'title' => '',
    'description' => '',
    'venue' => '',
    'start_date' => '',
    'end_date' => '',
);

// Function to get all events from the database
function getEvents($conn) {
    $query = "SELECT * FROM events ORDER BY start_date DESC";
    $result = mysqli_query($conn, $query);
    $events = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $events[] = $row;
        }
    }
    
    return $events;
}

// Function to get all users from the database
function getUsers($conn) {
    $query = "SELECT id, username, email, phone_number, role, created_at FROM users ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);
    $users = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
    }
    
    return $users;
}

// Function to get attendees for a specific event
function getAttendees($conn, $event_id) {
    $query = "SELECT u.username as name, u.email, r.registration_date 
              FROM registrations r 
              JOIN users u ON r.user_id = u.id 
              WHERE r.event_id = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $event_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $attendees = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $attendees[] = $row;
    }
    
    return $attendees;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['event-name'])) {
        // Collect form data
        $eventName = $_POST['event-name'];
        $eventDate = $_POST['start-date'];
        $endDate = $_POST['end-date'];
        $eventLocation = $_POST['event-location'];
        $eventDescription = $_POST['event-description'];
        $eventOrganizer = $_POST['event-organizer'];

        if (empty($eventName) || empty($eventDate) || empty($endDate) || empty($eventLocation) || empty($eventDescription) || empty($eventOrganizer)) {
            echo "All fields are required.";
            exit;
        }

        if (empty($errors['title']) && empty($errors['description']) && empty($errors['venue']) && empty($errors['start_date']) && empty($errors['end_date'])) {
            $admin_id = $_SESSION['user_id'];
          
            $sql = "INSERT INTO events (title, start_date, end_date, description, event_organizer, event_location, admin_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param("ssssssi", $eventName, $eventDate, $endDate, $eventDescription, $eventOrganizer, $eventLocation, $admin_id);
            if ($stmt->execute()) {
                header("Location: admin_dashboard.php?action=event_created");
                exit();
            } else {
                echo "<script>alert('Failed to create event: " . $stmt->error . "');</script>";
            }

            // Close statement
            $stmt->close();
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'send_reminders') {
        $event_id = $_POST['reminder-event'];

        if (!empty($event_id)) {
            $attendees = getAttendees($conn, $event_id);
            
            $eventQuery = "SELECT title, start_date, event_location FROM events WHERE id = ?";
            $stmt = mysqli_prepare($conn, $eventQuery);
            mysqli_stmt_bind_param($stmt, "i", $event_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $event = mysqli_fetch_assoc($result);

            foreach ($attendees as $attendee) {
                // Here you would send SMS or Email
                // For now we'll just log it in notifications table
                $msg = "Reminder for " . $event['title'] . " on " . $event['start_date'];
                $sql = "INSERT INTO notifications (event_id, message) VALUES (?, ?)";
                $stmt2 = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt2, "is", $event_id, $msg);
                mysqli_stmt_execute($stmt2);
            }
            
            header("Location: admin_dashboard.php?action=notifications_sent");
            exit();
        } else {
            echo "<script>alert('Please select an event!');</script>";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'delete_user') {
        $user_id_to_delete = $_POST['user_id'];
        if ($user_id_to_delete != $_SESSION['user_id']) {
            $sql = "DELETE FROM users WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $user_id_to_delete);
            mysqli_stmt_execute($stmt);
            header("Location: admin_dashboard.php?action=user_deleted");
            exit();
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'promote_user') {
        $user_id_to_promote = $_POST['user_id'];
        $sql = "UPDATE users SET role = 'admin' WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id_to_promote);
        mysqli_stmt_execute($stmt);
        header("Location: admin_dashboard.php?action=user_promoted");
        exit();
    }
}

$events = getEvents($conn);
$users = getUsers($conn);
?>