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
                    <h1>Teams Management</h1>
                    <p>Create sports teams, assign coaches, and track squad player counts</p>
                </div>
            </div>

            <?php if (!empty($success_msg)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
            <?php endif; ?>

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
            <?php endif; ?>

            <!-- Add / Edit Team Form Card -->
            <section class="card">
                <div class="card-header">
                    <h2><?php echo $edit_mode ? "Edit Team (ID #" . htmlspecialchars($edit_team['team_id']) . ")" : "Add New Team"; ?></h2>
                </div>
                <div class="card-body">
                    <form action="teams.php" method="POST" id="teamForm" onsubmit="return validateTeamForm();">
                        <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update' : 'add'; ?>">
                        <?php if ($edit_mode): ?>
                            <input type="hidden" name="team_id" value="<?php echo htmlspecialchars($edit_team['team_id']); ?>">
                        <?php endif; ?>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="team_name">Team Name *</label>
                                <input type="text" id="team_name" name="team_name" class="form-control"
                                       placeholder="e.g. Titans FC"
                                       value="<?php echo htmlspecialchars($edit_team['team_name']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="sport">Sport *</label>
                                <input type="text" id="sport" name="sport" class="form-control"
                                       placeholder="e.g. Football"
                                       value="<?php echo htmlspecialchars($edit_team['sport']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="category">Team Category *</label>
                                <select id="category" name="category" class="form-control" required>
                                    <option value="">-- Select Category --</option>
                                    <option value="Men" <?php echo ($edit_team['category'] === 'Men') ? 'selected' : ''; ?>>Men</option>
                                    <option value="Women" <?php echo ($edit_team['category'] === 'Women') ? 'selected' : ''; ?>>Women</option>
                                    <option value="Mixed" <?php echo ($edit_team['category'] === 'Mixed') ? 'selected' : ''; ?>>Mixed</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="coach_name">Coach Name *</label>
                                <input type="text" id="coach_name" name="coach_name" class="form-control"
                                       placeholder="e.g. Coach Marcus Reid"
                                       value="<?php echo htmlspecialchars($edit_team['coach_name']); ?>" required>
                            </div>

                            <div class="form-group form-group-full">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" class="form-control"
                                          placeholder="Brief notes about the team..."><?php echo htmlspecialchars($edit_team['description']); ?></textarea>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <?php echo $edit_mode ? "Update Team" : "Add Team"; ?>
                            </button>
                            <?php if ($edit_mode): ?>
                                <a href="teams.php" class="btn btn-secondary">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </section>

            <!-- Teams Table Card -->
            <section class="card">
                <div class="card-header">
                    <h2>All Club Teams (<?php echo $teams_result->num_rows; ?>)</h2>
                </div>
                <div class="card-body">
                    <!-- Teams Data Table -->
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th hidden>ID</th>
                                    <th>Team Name</th>
                                    <th>Sport</th>
                                    <th>Category</th>
                                    <th>Coach</th>
                                    <th>Players</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($teams_result->num_rows > 0): ?>
                                    <?php while ($row = $teams_result->fetch_assoc()): ?>
                                        <tr>
                                            <td hidden>#<?php echo $row['team_id']; ?></td>
                                            <td><strong><?php echo htmlspecialchars($row['team_name']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($row['sport']); ?></td>
                                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                                            <td><?php echo htmlspecialchars($row['coach_name']); ?></td>
                                            <td>
                                                <span class="badge badge-upcoming">
                                                    <?php echo (int)$row['player_count']; ?> player(s)
                                                </span>
                                            </td>
                                            <td><?php echo !empty($row['description']) ? htmlspecialchars($row['description']) : '-'; ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="teams.php?edit_id=<?php echo $row['team_id']; ?>" class="btn btn-sm btn-edit">Edit</a>
                                                    <a href="teams.php?delete_id=<?php echo $row['team_id']; ?>"
                                                       class="btn btn-sm btn-delete"
                                                       onclick="return confirmDelete('team', '<?php echo htmlspecialchars(addslashes($row['team_name'])); ?>');">
                                                        Delete
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="empty-state">No teams found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="script.js"></script>
</body>
</html>
