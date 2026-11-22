<?php
session_start();

if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

require_once 'db.php';

$error = "";
$username = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username         = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password         = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all fields.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if username already exists
        $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = "Username is already taken. Please choose another.";
            $check_stmt->close();
        } else {
            $check_stmt->close();

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'player'; // Always register as player

            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed_password, $role);

            if ($stmt->execute()) {
                $stmt->close();
                header("Location: login.php?registered=1");
                exit();
            } else {
                $error = "Registration failed: " . $conn->error;
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Registration - Sports Club Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L4 5v6.09c0 5.05 3.41 9.76 8 10.91 4.59-1.15 8-5.86 8-10.91V5l-8-3z"></path>
                        <circle cx="12" cy="11" r="3"></circle>
                    </svg>
                </div>
                <h1>Player Registration</h1>
                <p class="login-subtitle">Create a Player Account</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST" id="registerForm" onsubmit="return validateRegisterForm();">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-control"
                        placeholder="Choose a username"
                        value="<?php echo htmlspecialchars($username); ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="At least 6 characters"
                            required
                        >
                        <button
                            type="button"
                            class="toggle-password-btn"
                            id="togglePasswordBtn"
                            onclick="togglePasswordVisibility('password', 'togglePasswordBtn')"
                        >Show</button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        class="form-control"
                        placeholder="Re-enter password"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </form>

            <div class="login-footer">
                <p>Already have an account? <a href="login.php" style="color: #2563eb; font-weight: 600;">Login here</a></p>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>