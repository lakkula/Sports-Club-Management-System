<?php
// Common Header & Top Navigation Bar

if (!isset($page_title)) {
    $page_title = "Sports Club Management System";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Sports Club Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="app-body">

    <!-- Top Navigation Bar -->
    <header class="topbar">
        <div class="topbar-left">
            <button type="button" class="menu-toggle" id="sidebarToggle" onclick="toggleSidebar()" aria-label="Toggle navigation menu">
                &#9776;
            </button>
            <a href="dashboard.php" class="topbar-brand">
                <span class="brand-badge" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L4 5v6.09c0 5.05 3.41 9.76 8 10.91 4.59-1.15 8-5.86 8-10.91V5l-8-3z"></path>
                        <circle cx="12" cy="11" r="3"></circle>
                    </svg>
                </span>
                <span class="brand-text">Sports Club Management System</span>
            </a>
        </div>

        <div class="topbar-right">
            <span class="topbar-user">
                Logged in as <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                (<?php echo htmlspecialchars(ucfirst($_SESSION['role'])); ?>)
            </span>
            <a href="logout.php" class="btn btn-logout">Logout</a>
        </div>
    </header>

    <div class="app-container">
