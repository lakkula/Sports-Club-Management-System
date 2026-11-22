<?php
// =====================================================
// Dashboard Page (dashboard.php)
// Displays dynamic club statistics, recent players,
// upcoming events, and quick navigation links
// =====================================================

require_once 'auth.php';
require_once 'db.php';

$page_title   = "Dashboard";
$current_page = "dashboard";

//Total Players
$total_players = 0;
$res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM players");
if ($res && $row = mysqli_fetch_assoc($res)) {
    $total_players = (int)$row['total'];
}

//Total Teams
$total_teams = 0;
$res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM teams");
if ($res && $row = mysqli_fetch_assoc($res)) {
    $total_teams = (int)$row['total'];
}

//Total Upcoming Events
$upcoming_events_count = 0;
$res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM events WHERE status = 'Upcoming'");
if ($res && $row = mysqli_fetch_assoc($res)) {
    $upcoming_events_count = (int)$row['total'];
}

//Total Completed Events
$completed_events_count = 0;
$res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM events WHERE status = 'Completed'");
if ($res && $row = mysqli_fetch_assoc($res)) {
    $completed_events_count = (int)$row['total'];
}

//Recently Added Players (Latest 5)
$recent_players_sql = "SELECT p.player_id, p.full_name, p.sport, p.join_date, t.team_name
                       FROM players p
                       LEFT JOIN teams t ON p.team_id = t.team_id
                       ORDER BY p.player_id DESC
                       LIMIT 5";
$recent_players_result = mysqli_query($conn, $recent_players_sql);

//Upcoming Events (Next 5 ordered by date)
$upcoming_events_sql = "SELECT event_id, event_name, sport, event_date, start_time, location
                        FROM events
                        WHERE status = 'Upcoming'
                        ORDER BY event_date ASC, start_time ASC
                        LIMIT 5";
$upcoming_events_result = mysqli_query($conn, $upcoming_events_sql);

include 'header.php';
include 'sidebar.php';
?>

        <!-- Main Page Content -->
        <main class="main-content">
            <div class="page-header">
                <div>
                    <h1>Club Dashboard</h1>
                    <p>Overview of players, teams, and scheduled club events</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <section class="stats-grid">
                <div class="stat-card stat-players">
                    <span class="stat-label">Total Players</span>
                    <span class="stat-number"><?php echo $total_players; ?></span>
                </div>

                <div class="stat-card stat-teams">
                    <span class="stat-label">Total Teams</span>
                    <span class="stat-number"><?php echo $total_teams; ?></span>
                </div>

                <div class="stat-card stat-upcoming">
                    <span class="stat-label">Upcoming Events</span>
                    <span class="stat-number"><?php echo $upcoming_events_count; ?></span>
                </div>

                <div class="stat-card stat-completed">
                    <span class="stat-label">Completed Events</span>
                    <span class="stat-number"><?php echo $completed_events_count; ?></span>
                </div>
            </section>

            <!-- Quick Navigation Links -->
            <section class="quick-links">
                <a href="players.php" class="quick-link-card">
                    <span>Manage Players</span>
                    <span>&rarr;</span>
                </a>
                <a href="teams.php" class="quick-link-card">
                    <span><?php echo $is_admin ? 'Manage Teams' : 'View Teams'; ?></span>
                    <span>&rarr;</span>
                </a>
                <a href="events.php" class="quick-link-card">
                    <span><?php echo $is_admin ? 'Manage Events' : 'View Events'; ?></span>
                    <span>&rarr;</span>
                </a>
            </section>

            <!-- Recent Players & Upcoming Events Tables -->
            <section class="dashboard-grid">
                <!-- Recently Added Players -->
                <div class="card">
                    <div class="card-header">
                        <h2>Recently Added Players</h2>
                        <a href="players.php" class="btn btn-sm btn-secondary">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Sport</th>
                                        <th>Team</th>
                                        <th>Join Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($recent_players_result && mysqli_num_rows($recent_players_result) > 0): ?>
                                        <?php while ($player = mysqli_fetch_assoc($recent_players_result)): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($player['full_name']); ?></td>
                                                <td><?php echo htmlspecialchars($player['sport']); ?></td>
                                                <td><?php echo !empty($player['team_name']) ? htmlspecialchars($player['team_name']) : 'Unassigned'; ?></td>
                                                <td><?php echo htmlspecialchars($player['join_date']); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="empty-state">No players found yet.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Events -->
                <div class="card">
                    <div class="card-header">
                        <h2>Upcoming Events</h2>
                        <a href="events.php" class="btn btn-sm btn-secondary">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Event</th>
                                        <th>Sport</th>
                                        <th>Date &amp; Time</th>
                                        <th>Location</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($upcoming_events_result && mysqli_num_rows($upcoming_events_result) > 0): ?>
                                        <?php while ($event = mysqli_fetch_assoc($upcoming_events_result)): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($event['event_name']); ?></td>
                                                <td><?php echo htmlspecialchars($event['sport']); ?></td>
                                                <td>
                                                    <?php echo htmlspecialchars($event['event_date']); ?><br>
                                                    <small><?php echo htmlspecialchars(substr($event['start_time'], 0, 5)); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($event['location']); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="empty-state">No upcoming events scheduled.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="script.js"></script>
</body>
</html>
