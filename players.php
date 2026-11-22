<?php
require_once 'auth.php';
require_once 'db.php';

$page_title   = "Players Management";
$current_page = "players";

$success_msg = "";
$error_msg   = "";

// Default values for Add/Edit form
$edit_mode = false;
$edit_player = array(
    'player_id' => '',
    'full_name' => '',
    'dob'       => '',
    'gender'    => '',
    'contact'   => '',
    'email'     => '',
    'sport'     => '',
    'team_id'   => '',
    'join_date' => date('Y-m-d')
);

// Display feedback messages passed via $_GET
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') {
        $success_msg = "New player added successfully.";
    } elseif ($_GET['msg'] === 'updated') {
        $success_msg = "Player details updated successfully.";
    } elseif ($_GET['msg'] === 'deleted') {
        $success_msg = "Player deleted successfully.";
    }
}

// DELETE ($_GET['delete_id'])
if (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];

    $stmt = $conn->prepare("DELETE FROM players WHERE player_id = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: players.php?msg=deleted");
        exit();
    } else {
        $error_msg = "Error deleting player: " . $conn->error;
    }
    $stmt->close();
}

// ADD & UPDATE ($_POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action    = isset($_POST['action']) ? $_POST['action'] : 'add';
    $player_id = isset($_POST['player_id']) ? (int)$_POST['player_id'] : 0;
    $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $dob       = isset($_POST['dob']) ? trim($_POST['dob']) : '';
    $gender    = isset($_POST['gender']) ? trim($_POST['gender']) : '';
    $contact   = isset($_POST['contact']) ? trim($_POST['contact']) : '';
    $email     = isset($_POST['email']) ? trim($_POST['email']) : '';
    $sport     = isset($_POST['sport']) ? trim($_POST['sport']) : '';
    $team_id   = (!empty($_POST['team_id'])) ? (int)$_POST['team_id'] : null;
    $join_date = isset($_POST['join_date']) ? trim($_POST['join_date']) : '';

    // Server-side validation of required fields
    if (empty($full_name) || empty($dob) || empty($gender) || empty($contact) || empty($email) || empty($sport) || empty($join_date)) {
        $error_msg = "Please fill in all required player fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Please enter a valid email address.";
    } else {
        if ($action === 'update' && $player_id > 0) {
            // UPDATE existing player
            $sql = "UPDATE players
                    SET full_name = ?, dob = ?, gender = ?, contact = ?, email = ?, sport = ?, team_id = ?, join_date = ?
                    WHERE player_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssisi", $full_name, $dob, $gender, $contact, $email, $sport, $team_id, $join_date, $player_id);

            if ($stmt->execute()) {
                $stmt->close();
                header("Location: players.php?msg=updated");
                exit();
            } else {
                $error_msg = "Error updating player: " . $conn->error;
            }
            $stmt->close();
        } else {
            // INSERT new player
            $sql = "INSERT INTO players (full_name, dob, gender, contact, email, sport, team_id, join_date)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssis", $full_name, $dob, $gender, $contact, $email, $sport, $team_id, $join_date);

            if ($stmt->execute()) {
                $stmt->close();
                header("Location: players.php?msg=added");
                exit();
            } else {
                $error_msg = "Error adding player: " . $conn->error;
            }
            $stmt->close();
        }
    }

    // Preserve entered form values if validation fails
    if ($action === 'update') {
        $edit_mode = true;
    }
    $edit_player = array(
        'player_id' => $player_id,
        'full_name' => $full_name,
        'dob'       => $dob,
        'gender'    => $gender,
        'contact'   => $contact,
        'email'     => $email,
        'sport'     => $sport,
        'team_id'   => $team_id,
        'join_date' => $join_date
    );
}

// LOAD PLAYER FOR EDITING ($_GET['edit_id'])
if (isset($_GET['edit_id']) && !empty($_GET['edit_id']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $edit_id = (int)$_GET['edit_id'];

    $stmt = $conn->prepare("SELECT * FROM players WHERE player_id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $edit_mode   = true;
        $edit_player = $result->fetch_assoc();
    } else {
        $error_msg = "Player record not found.";
    }
    $stmt->close();
}

// TEAMS & DISTINCT SPORTS FOR DROPDOWNS
$teams_list = array();
$teams_result = mysqli_query($conn, "SELECT team_id, team_name, sport FROM teams ORDER BY team_name ASC");
if ($teams_result) {
    while ($team_row = mysqli_fetch_assoc($teams_result)) {
        $teams_list[] = $team_row;
    }
}

// VIEW ALL PLAYERS
$sql = "SELECT p.*, t.team_name
        FROM players p
        LEFT JOIN teams t ON p.team_id = t.team_id
        ORDER BY p.player_id DESC";
$players_result = mysqli_query($conn, $sql);

include 'header.php';
include 'sidebar.php';
?>

        <!-- Main Page Content -->
        <main class="main-content">
            <div class="page-header">
                <div>
                    <h1>Players Management</h1>
                    <p>Register new players, assign them to teams, and manage player records</p>
                </div>
            </div>

            <?php if (!empty($success_msg)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
            <?php endif; ?>

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
            <?php endif; ?>

            <!-- Add / Edit Player Form Card -->
            <section class="card">
                <div class="card-header">
                    <h2><?php echo $edit_mode ? "Edit Player (ID #" . htmlspecialchars($edit_player['player_id']) . ")" : "Add New Player"; ?></h2>
                </div>
                <div class="card-body">
                    <form action="players.php" method="POST" id="playerForm" onsubmit="return validatePlayerForm();">
                        <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update' : 'add'; ?>">
                        <?php if ($edit_mode): ?>
                            <input type="hidden" name="player_id" value="<?php echo htmlspecialchars($edit_player['player_id']); ?>">
                        <?php endif; ?>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="full_name">Full Name *</label>
                                <input type="text" id="full_name" name="full_name" class="form-control"
                                       value="<?php echo htmlspecialchars($edit_player['full_name']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="dob">Date of Birth *</label>
                                <input type="date" id="dob" name="dob" class="form-control"
                                       value="<?php echo htmlspecialchars($edit_player['dob']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="gender">Gender *</label>
                                <select id="gender" name="gender" class="form-control" required>
                                    <option value="">-- Select Gender --</option>
                                    <option value="Male" <?php echo ($edit_player['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo ($edit_player['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                                    <option value="Other" <?php echo ($edit_player['gender'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="contact">Contact Number *</label>
                                <input type="text" id="contact" name="contact" class="form-control"
                                       placeholder="e.g. 555-0199"
                                       value="<?php echo htmlspecialchars($edit_player['contact']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" class="form-control"
                                       placeholder="player@example.com"
                                       value="<?php echo htmlspecialchars($edit_player['email']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="sport">Sport *</label>
                                <input type="text" id="sport" name="sport" class="form-control"
                                       placeholder="e.g. Football, Basketball"
                                       value="<?php echo htmlspecialchars($edit_player['sport']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="team_id">Assigned Team</label>
                                <select id="team_id" name="team_id" class="form-control">
                                    <option value="">-- No Team (Unassigned) --</option>
                                    <?php foreach ($teams_list as $team): ?>
                                        <option value="<?php echo $team['team_id']; ?>"
                                            <?php echo ((string)$edit_player['team_id'] === (string)$team['team_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($team['team_name'] . " (" . $team['sport'] . ")"); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="join_date">Join Date *</label>
                                <input type="date" id="join_date" name="join_date" class="form-control"
                                       value="<?php echo htmlspecialchars($edit_player['join_date']); ?>" required>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <?php echo $edit_mode ? "Update Player" : "Add Player"; ?>
                            </button>
                            <?php if ($edit_mode): ?>
                                <a href="players.php" class="btn btn-secondary">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </section>

            <!-- Players Table Card -->
            <section class="card">
                <div class="card-header">
                    <h2>All Club Players (<?php echo $players_result->num_rows; ?>)</h2>
                </div>
                <div class="card-body">
                    <!-- Players Data Table -->
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th hidden>ID</th>
                                    <th>Full Name</th>
                                    <th>DOB</th>
                                    <th>Gender</th>
                                    <th>Contact</th>
                                    <th>Email</th>
                                    <th>Sport</th>
                                    <th>Team</th>
                                    <th>Join Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($players_result->num_rows > 0): ?>
                                    <?php while ($row = $players_result->fetch_assoc()): ?>
                                        <tr>
                                            <td hidden>#<?php echo $row['player_id']; ?></td>
                                            <td><strong><?php echo htmlspecialchars($row['full_name']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($row['dob']); ?></td>
                                            <td><?php echo htmlspecialchars($row['gender']); ?></td>
                                            <td><?php echo htmlspecialchars($row['contact']); ?></td>
                                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                                            <td><?php echo htmlspecialchars($row['sport']); ?></td>
                                            <td><?php echo !empty($row['team_name']) ? htmlspecialchars($row['team_name']) : 'Unassigned'; ?></td>
                                            <td><?php echo htmlspecialchars($row['join_date']); ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="players.php?edit_id=<?php echo $row['player_id']; ?>" class="btn btn-sm btn-edit">Edit</a>
                                                    <a href="players.php?delete_id=<?php echo $row['player_id']; ?>"
                                                       class="btn btn-sm btn-delete"
                                                       onclick="return confirmDelete('player', '<?php echo htmlspecialchars(addslashes($row['full_name'])); ?>');">
                                                        Delete
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="empty-state">No players found.</td>
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
