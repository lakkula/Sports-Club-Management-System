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
                                    <th>ID</th>
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
                                            <td>#<?php echo $row['team_id']; ?></td>
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
