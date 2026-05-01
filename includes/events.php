<?php
require 'db.php';

session_start(); // Start session to access user_id

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action == 'create') {
        // Check if user_id is set in session
        if (!isset($_SESSION['user_id'])) {
            die("error=User not logged in.");
        }

        // Create event
        $title = $_POST['title'];
        $description = $_POST['description'];
        $event_location = $_POST['event_location'] ?? $_POST['venue'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $admin_id = $_SESSION['user_id']; // Get admin_id from session

        // Insert event into database
        $query = "INSERT INTO events (title, description, event_location, start_date, end_date, admin_id) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssssi", $title, $description, $event_location, $start_date, $end_date, $admin_id);
        if (mysqli_stmt_execute($stmt)) {
            echo "success=Event created successfully!";
        } else {
            echo "error=Failed to create event: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } elseif ($action == 'delete') {
        // Delete event
        $event_id = filter_var($_POST['event_id'], FILTER_VALIDATE_INT);
        if ($event_id === false) {
            echo "error=Invalid event ID.";
            return;
        }
        $query = "DELETE FROM events WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $event_id);
        if (mysqli_stmt_execute($stmt)) {
            echo "success=Event deleted successfully!";
        } else {
            echo "error=Failed to delete event: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    }
}
?>