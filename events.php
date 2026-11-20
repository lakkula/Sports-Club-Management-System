<?php
require_once 'auth.php';
require_once 'db.php';

$page_title   = "Events Management";
$current_page = "events";

$success_msg = "";
$error_msg   = "";

// Default values for Add/Edit form
$edit_mode = false;
$edit_event = array(
    'event_id'    => '',
    'event_name'  => '',
    'sport'       => '',
    'event_date'  => '',
    'start_time'  => '',
    'location'    => '',
    'description' => '',
    'status'      => 'Upcoming'
);

// Display feedback messages passed via $_GET
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') {
        $success_msg = "New event added successfully.";
    } elseif ($_GET['msg'] === 'updated') {
        $success_msg = "Event details updated successfully.";
    } elseif ($_GET['msg'] === 'deleted') {
        $success_msg = "Event deleted successfully.";
    }
}

// DELETE OPERATION ($_GET['delete_id'])
if (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];

    $stmt = $conn->prepare("DELETE FROM events WHERE event_id = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: events.php?msg=deleted");
        exit();
    } else {
        $error_msg = "Error deleting event: " . $conn->error;
    }
    $stmt->close();
}

// ADD & UPDATE OPERATIONS ($_POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action      = isset($_POST['action']) ? $_POST['action'] : 'add';
    $event_id    = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;
    $event_name  = isset($_POST['event_name']) ? trim($_POST['event_name']) : '';
    $sport       = isset($_POST['sport']) ? trim($_POST['sport']) : '';
    $event_date  = isset($_POST['event_date']) ? trim($_POST['event_date']) : '';
    $start_time  = isset($_POST['start_time']) ? trim($_POST['start_time']) : '';
    $location    = isset($_POST['location']) ? trim($_POST['location']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $status      = isset($_POST['status']) ? trim($_POST['status']) : 'Upcoming';

    // Validate required fields
    if (empty($event_name) || empty($sport) || empty($event_date) || empty($start_time) || empty($location) || empty($status)) {
        $error_msg = "Please fill in all required event fields.";
    } else {
        if ($action === 'update' && $event_id > 0) {
            // UPDATE existing event
            $sql = "UPDATE events
                    SET event_name = ?, sport = ?, event_date = ?, start_time = ?, location = ?, description = ?, status = ?
                    WHERE event_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssi", $event_name, $sport, $event_date, $start_time, $location, $description, $status, $event_id);

            if ($stmt->execute()) {
                $stmt->close();
                header("Location: events.php?msg=updated");
                exit();
            } else {
                $error_msg = "Error updating event: " . $conn->error;
            }
            $stmt->close();
        } else {
            // INSERT new event
            $sql = "INSERT INTO events (event_name, sport, event_date, start_time, location, description, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssss", $event_name, $sport, $event_date, $start_time, $location, $description, $status);

            if ($stmt->execute()) {
                $stmt->close();
                header("Location: events.php?msg=added");
                exit();
            } else {
                $error_msg = "Error adding event: " . $conn->error;
            }
            $stmt->close();
        }
    }

    // Preserve form inputs if validation fails
    if ($action === 'update') {
        $edit_mode = true;
    }
    $edit_event = array(
        'event_id'    => $event_id,
        'event_name'  => $event_name,
        'sport'       => $sport,
        'event_date'  => $event_date,
        'start_time'  => $start_time,
        'location'    => $location,
        'description' => $description,
        'status'      => $status
    );
}

// LOAD EVENT FOR EDITING ($_GET['edit_id'])
if (isset($_GET['edit_id']) && !empty($_GET['edit_id']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $edit_id = (int)$_GET['edit_id'];

    $stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $edit_mode  = true;
        $edit_event = $result->fetch_assoc();
    } else {
        $error_msg = "Event record not found.";
    }
    $stmt->close();
}

// VIEW ALL EVENTS
$sql = "SELECT * FROM events ORDER BY event_date DESC, start_time DESC";
$events_result = mysqli_query($conn, $sql);

include 'header.php';
include 'sidebar.php';
?>