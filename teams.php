<?php

require_once 'auth.php';
require_once 'db.php';

$page_title   = "Teams Management";
$current_page = "teams";

$success_msg = "";
$error_msg   = "";

// Default values for Add/Edit form
$edit_mode = false;
$edit_team = array(
    'team_id'     => '',
    'team_name'   => '',
    'sport'       => '',
    'category'    => '',
    'coach_name'  => '',
    'description' => ''
);

// Display feedback messages passed via $_GET
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') {
        $success_msg = "New team added successfully.";
    } elseif ($_GET['msg'] === 'updated') {
        $success_msg = "Team details updated successfully.";
    } elseif ($_GET['msg'] === 'deleted') {
        $success_msg = "Team deleted successfully.";
    }
}

// DELETE OPERATION ($_GET['delete_id'])
if (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];

    $stmt = $conn->prepare("DELETE FROM teams WHERE team_id = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: teams.php?msg=deleted");
        exit();
    } else {
        $error_msg = "Error deleting team: " . $conn->error;
    }
    $stmt->close();
}

// ADD & UPDATE OPERATIONS ($_POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action      = isset($_POST['action']) ? $_POST['action'] : 'add';
    $team_id     = isset($_POST['team_id']) ? (int)$_POST['team_id'] : 0;
    $team_name   = isset($_POST['team_name']) ? trim($_POST['team_name']) : '';
    $sport       = isset($_POST['sport']) ? trim($_POST['sport']) : '';
    $category    = isset($_POST['category']) ? trim($_POST['category']) : '';
    $coach_name  = isset($_POST['coach_name']) ? trim($_POST['coach_name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';

    // Validate required fields
    if (empty($team_name) || empty($sport) || empty($category) || empty($coach_name)) {
        $error_msg = "Please fill in all required team fields.";
    } else {
        if ($action === 'update' && $team_id > 0) {
            // UPDATE existing team
            $sql = "UPDATE teams
                    SET team_name = ?, sport = ?, category = ?, coach_name = ?, description = ?
                    WHERE team_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssi", $team_name, $sport, $category, $coach_name, $description, $team_id);

            if ($stmt->execute()) {
                $stmt->close();
                header("Location: teams.php?msg=updated");
                exit();
            } else {
                $error_msg = "Error updating team: " . $conn->error;
            }
            $stmt->close();
        } else {
            // INSERT new team
            $sql = "INSERT INTO teams (team_name, sport, category, coach_name, description)
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $team_name, $sport, $category, $coach_name, $description);

            if ($stmt->execute()) {
                $stmt->close();
                header("Location: teams.php?msg=added");
                exit();
            } else {
                $error_msg = "Error adding team: " . $conn->error;
            }
            $stmt->close();
        }
    }

    // Preserve form inputs if validation fails
    if ($action === 'update') {
        $edit_mode = true;
    }
    $edit_team = array(
        'team_id'     => $team_id,
        'team_name'   => $team_name,
        'sport'       => $sport,
        'category'    => $category,
        'coach_name'  => $coach_name,
        'description' => $description
    );
}

// LOAD TEAM FOR EDITING ($_GET['edit_id'])
if (isset($_GET['edit_id']) && !empty($_GET['edit_id']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $edit_id = (int)$_GET['edit_id'];

    $stmt = $conn->prepare("SELECT * FROM teams WHERE team_id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $edit_mode = true;
        $edit_team = $result->fetch_assoc();
    } else {
        $error_msg = "Team record not found.";
    }
    $stmt->close();
}

// VIEW ALL TEAMS WITH PLAYER COUNT
$sql = "SELECT t.*, COUNT(p.player_id) AS player_count
        FROM teams t
        LEFT JOIN players p ON t.team_id = p.team_id
        GROUP BY t.team_id
        ORDER BY t.team_id DESC";
$teams_result = mysqli_query($conn, $sql);

include 'header.php';
include 'sidebar.php';
?>