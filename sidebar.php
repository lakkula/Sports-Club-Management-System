<?php

if (!isset($current_page)) {
    $current_page = "";
}
?>
        <!-- Left Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-title"><?php echo $is_admin ? 'Club Administration' : 'Player Portal'; ?></div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-link <?php echo ($current_page === 'dashboard') ? 'active' : ''; ?>">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="players.php" class="nav-link <?php echo ($current_page === 'players') ? 'active' : ''; ?>">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span>Players</span>
                </a>

                <a href="teams.php" class="nav-link <?php echo ($current_page === 'teams') ? 'active' : ''; ?>">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L4 5v6.09c0 5.05 3.41 9.76 8 10.91 4.59-1.15 8-5.86 8-10.91V5l-8-3z"></path>
                    </svg>
                    <span>Teams</span>
                </a>

                <a href="events.php" class="nav-link <?php echo ($current_page === 'events') ? 'active' : ''; ?>">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>Events</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="logout.php" class="nav-link nav-link-logout">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span>Logout</span>
                </a>
            </div>
        </aside>
