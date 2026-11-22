<?php

session_start();

if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && isset($_SESSION['role'])) {
    header("Location: dashboard.php");
    exit();
}

require_once 'db.php';

$error = "";
$success = "";
$remembered_username = "";
$remember_checked = false;

if (isset($_COOKIE['remember_username']) && !empty($_COOKIE['remember_username'])) {
    $remembered_username = $_COOKIE['remember_username'];
    $remember_checked = true;
}

if (isset($_GET['logged_out']) && $_GET['logged_out'] == '1') {
    $success = "You have been logged out successfully.";
}

if (isset($_GET['registered']) && $_GET['registered'] == '1') {
    $success = "Registration successful! You can now log in.";
}

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $stmt = $conn->prepare("SELECT user_id, username, password,role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Store user details in PHP session
                $_SESSION['user_id']  = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];

                // Handle Remember Me cookie (expires in 30 days)
                if (isset($_POST['remember_me'])) {
                    setcookie("remember_username", $user['username'], time() + (86400 * 30), "/");
                } else {
                    // Remove cookie if unchecked
                    setcookie("remember_username", "", time() - 3600, "/");
                }

                $stmt->close();
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }

        $stmt->close();
    }

    // Keep entered username in input field on failed login
    $remembered_username = $username;
    $remember_checked = isset($_POST['remember_me']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sports Club Management System</title>
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
                <h1>Sports Club</h1>
                <p class="login-subtitle">Management System</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success" role="status">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" id="loginForm" onsubmit="return validateLoginForm();">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-control"
                        placeholder="Enter username"
                        value="<?php echo htmlspecialchars($remembered_username); ?>"
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
                            placeholder="Enter password"
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

                <div class="checkbox-row">
                    <label class="checkbox-label">
                        <input
                            type="checkbox"
                            name="remember_me"
                            value="1"
                            <?php echo $remember_checked ? 'checked' : ''; ?>
                        >
                        <span>Remember Me</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Login to Dashboard</button>
            </form>

            <div class="login-footer">
                <p>New player? <a href="register.php" style="color: #2563eb; font-weight: 600;">Register here</a></p>
                <p>Default Admin: <strong>admin</strong> / <strong>admin123</strong></p>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
