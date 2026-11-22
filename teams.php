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

<!-- Main Page Content -->
        <main class="main-content">
            <div class="page-header">
                <div>
                    <h1>Events Management</h1>
                    <p>Schedule matches, tournaments, and track event statuses</p>
                </div>
            </div>

            <?php if (!empty($success_msg)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
            <?php endif; ?>

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
            <?php endif; ?>

            <!-- Add / Edit Event Form Card -->
            <section class="card">
                <div class="card-header">
                    <h2><?php echo $edit_mode ? "Edit Event (ID #" . htmlspecialchars($edit_event['event_id']) . ")" : "Add New Event"; ?></h2>
                </div>
                <div class="card-body">
                    <form action="events.php" method="POST" id="eventForm" onsubmit="return validateEventForm();">
                        <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update' : 'add'; ?>">
                        <?php if ($edit_mode): ?>
                            <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($edit_event['event_id']); ?>">
                        <?php endif; ?>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="event_name">Event Name *</label>
                                <input type="text" id="event_name" name="event_name" class="form-control"
                                       placeholder="e.g. Inter-University Football Cup"
                                       value="<?php echo htmlspecialchars($edit_event['event_name']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="sport">Sport *</label>
                                <input type="text" id="sport" name="sport" class="form-control"
                                       placeholder="e.g. Football"
                                       value="<?php echo htmlspecialchars($edit_event['sport']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="event_date">Event Date *</label>
                                <input type="date" id="event_date" name="event_date" class="form-control"
                                       value="<?php echo htmlspecialchars($edit_event['event_date']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="start_time">Start Time *</label>
                                <input type="time" id="start_time" name="start_time" class="form-control"
                                       value="<?php echo htmlspecialchars($edit_event['start_time']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="location">Location *</label>
                                <input type="text" id="location" name="location" class="form-control"
                                       placeholder="e.g. Main Club Stadium"
                                       value="<?php echo htmlspecialchars($edit_event['location']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="status">Event Status *</label>
                                <select id="status" name="status" class="form-control" required>
                                    <option value="Upcoming" <?php echo ($edit_event['status'] === 'Upcoming') ? 'selected' : ''; ?>>Upcoming</option>
                                    <option value="Ongoing" <?php echo ($edit_event['status'] === 'Ongoing') ? 'selected' : ''; ?>>Ongoing</option>
                                    <option value="Completed" <?php echo ($edit_event['status'] === 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                    <option value="Cancelled" <?php echo ($edit_event['status'] === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>

                            <div class="form-group form-group-full">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" class="form-control"
                                          placeholder="Details about the event or participating teams..."><?php echo htmlspecialchars($edit_event['description']); ?></textarea>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <?php echo $edit_mode ? "Update Event" : "Add Event"; ?>
                            </button>
                            <?php if ($edit_mode): ?>
                                <a href="events.php" class="btn btn-secondary">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </section>

            <!-- Events Table Card -->
            <section class="card">
                <div class="card-header">
                    <h2>All Club Events (<?php echo $events_result->num_rows; ?>)</h2>
                </div>
                <div class="card-body">
                    <!-- Events Data Table -->
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Event Name</th>
                                    <th>Sport</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($events_result->num_rows > 0): ?>
                                    <?php while ($row = $events_result->fetch_assoc()): ?>
                                        <?php
                                            $badge_class = 'badge-upcoming';
                                            if ($row['status'] === 'Ongoing') {
                                                $badge_class = 'badge-ongoing';
                                            } elseif ($row['status'] === 'Completed') {
                                                $badge_class = 'badge-completed';
                                            } elseif ($row['status'] === 'Cancelled') {
                                                $badge_class = 'badge-cancelled';
                                            }
                                        ?>
                                        <tr>
                                            <td>#<?php echo $row['event_id']; ?></td>
                                            <td><strong><?php echo htmlspecialchars($row['event_name']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($row['sport']); ?></td>
                                            <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                                            <td><?php echo htmlspecialchars(substr($row['start_time'], 0, 5)); ?></td>
                                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                                            <td>
                                                <span class="badge <?php echo $badge_class; ?>">
                                                    <?php echo htmlspecialchars($row['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo !empty($row['description']) ? htmlspecialchars($row['description']) : '-'; ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="events.php?edit_id=<?php echo $row['event_id']; ?>" class="btn btn-sm btn-edit">Edit</a>
                                                    <a href="events.php?delete_id=<?php echo $row['event_id']; ?>"
                                                       class="btn btn-sm btn-delete"
                                                       onclick="return confirmDelete('event', '<?php echo htmlspecialchars(addslashes($row['event_name'])); ?>');">
                                                        Delete
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="empty-state">No events found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="js/script.js"></script>
</body>
</html>