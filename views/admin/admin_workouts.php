<?php
require_once __DIR__ . '/../../BL/AdminWorkoutsManager.php';

function h($value) { return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8'); }

$workoutsManager = new AdminWorkoutsManager();
$workouts = $workoutsManager->getWorkouts();
$stats = $workoutsManager->getStats();
$weeklyRows = $workoutsManager->getWeeklyRows();
$popularRows = $workoutsManager->getPopularRows();
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymTracker Admin - Workouts</title>
    <link rel="stylesheet" href="/workout_trackersys/assets/admin-managers.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/workout_trackersys/assets/footer.css">
</head>
<body>
<div class="Sidebar" id="sidebar">
    <div class="NavLinks">
        <button class="toggle-btn" onclick="ToggleSidebar()"><i class="material-icons">menu</i></button>
        <div class="sidebar-brand"><span class="sidebar-logo" aria-hidden="true">&#128170;</span><span class="text"><span class="brand-gym">Gym</span>Tracker Admin</span></div>
        <ul>
            <li onclick="redirectAdmin(1)"><i class="material-icons icon">dashboard</i><span class="text">Dashboard</span></li>
            <li onclick="redirectAdmin(2)"><i class="material-icons icon">people</i><span class="text">Users</span></li>
            <li onclick="redirectAdmin(3)"><i class="material-icons icon">fitness_center</i><span class="text">Exercises</span></li>
            <li class="active" onclick="redirectAdmin(6)"><i class="material-icons icon">self_improvement</i><span class="text">Workouts</span></li>
            <li onclick="redirectAdmin(4)"><i class="material-icons icon">bar_chart</i><span class="text">Reports</span></li>
            <li onclick="redirectAdmin(5)"><i class="material-icons icon">receipt_long</i><span class="text">Activity Logs</span></li>
        </ul>
    </div>
        <div class="sidebar-theme">
        <span class="toggle-label" id="theme-label">Light</span>
        <label class="toggle">
            <input type="checkbox" id="themeToggle">
            <div class="toggle-track"></div>
            <div class="toggle-thumb"><i class="material-icons">brightness_5</i></div>
        </label>
    </div>
<div class="Logout">
        <button type="button" onclick="LogoutFunc()">
            <i class="material-icons">logout</i>
            <span class="text">Logout</span>
        </button>
    </div>
</div>

<main class="main-content">
    <div class="top-bar"><div class="greeting"><h1>Workout Manager</h1><p>Workout plans, exercise assignments, and completion activity.</p></div><div class="toggle-thumb"><i class="material-icons">brightness_5</i></div></label></div></div>
    <section class="admin-hero"><div><span class="section-kicker">Workout Operations</span><h2>Manage workout plans across users</h2><p>See which users own each workout, how many exercises are assigned, and whether users are actually completing them.</p></div><div class="hero-icon"><i class="material-icons">self_improvement</i></div></section>

    <div class="stats-row">
        <div class="stat-card"><div class="stat-icon"><i class="material-icons">library_books</i></div><div><div class="stat-value"><?= $stats['workouts'] ?></div><div class="stat-label">Workout Plans</div></div></div>
        <div class="stat-card"><div class="stat-icon"><i class="material-icons">playlist_add_check</i></div><div><div class="stat-value"><?= $stats['assignedExercises'] ?></div><div class="stat-label">Assigned Exercises</div></div></div>
        <div class="stat-card"><div class="stat-icon"><i class="material-icons">timer</i></div><div><div class="stat-value"><?= $stats['sessions'] ?></div><div class="stat-label">Sessions Logged</div></div></div>
        <div class="stat-card"><div class="stat-icon"><i class="material-icons">verified</i></div><div><div class="stat-value"><?= $stats['completed'] ?></div><div class="stat-label">Completed</div></div></div>
    </div>

    <div class="grid-main">
        <section class="card"><div class="card-header"><div><div class="card-title">New Workout Plans</div><div class="card-subtitle">Created during the last 30 days.</div></div></div><div class="chart-wrap"><canvas id="newWorkoutsChart"></canvas></div></section>
        <section class="card"><div class="card-header"><div><div class="card-title">Most Logged Workouts</div><div class="card-subtitle">Ranked by session count.</div></div></div><div class="chart-wrap"><canvas id="popularWorkoutsChart"></canvas></div></section>
    </div>

    <section class="card">
        <div class="card-header"><div><div class="card-title">Workout Directory</div><div class="card-subtitle"><?= count($workouts) ?> plans loaded from tbl_workouts.</div></div></div>
        <div class="table-wrap"><table class="manager-table">
            <thead><tr><th>Workout</th><th>Owner</th><th>Exercises</th><th>Planned</th><th>Sessions</th><th>Status</th><th>Created</th></tr></thead>
            <tbody>
            <?php foreach ($workouts as $workout): ?>
                <tr>
                    <td><div class="entity-cell"><div class="entity-icon"><i class="material-icons">self_improvement</i></div><div><div class="entity-name"><?= h($workout['name']) ?></div><div class="entity-sub"><?= h($workout['workout_description'] ?: 'No description') ?></div></div></div></td>
                    <td><div class="entity-name"><?= h($workout['username'] ?: 'Unknown') ?></div><div class="entity-sub"><?= h($workout['email']) ?></div></td>
                    <td><?= (int)$workout['exercise_count'] ?></td>
                    <td><?= (int)$workout['planned_sets'] ?> sets / <?= (int)$workout['planned_minutes'] ?> min</td>
                    <td><?= (int)$workout['session_count'] ?></td>
                    <td><span class="badge <?= ((int)$workout['completed_count'] > 0) ? 'green' : 'orange' ?>"><?= ((int)$workout['completed_count'] > 0) ? 'Active' : 'No sessions' ?></span></td>
                    <td><?= h($workout['created_At']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
    </section>
</main>
<footer>
    <div class="footer-container">
        <div class="footer-brand">&#128170; <span class="brand-gym">Gym</span>Tracker</div>
    </div>
    <div class="footer-copyright">&copy; 2026 Gym Tracker. Built with &hearts; for fitness lovers by Niko</div>
</footer>
<script src="/workout_trackersys/scripts/redirect.js"></script><script src="/workout_trackersys/scripts/magic.js"></script>

<script>
// Bootstrapped for /scripts/AdminWorkouts.js
window.__ADMIN_WORKOUTS_WEEKLY_ROWS__ = <?= json_encode($weeklyRows) ?>;
window.__ADMIN_WORKOUTS_POPULAR_ROWS__ = <?= json_encode($popularRows) ?>;
</script>
<script src="/workout_trackersys/scripts/AdminWorkouts.js"></script>
</body>
</html>
