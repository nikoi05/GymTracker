<?php
require_once __DIR__ . '/../../BL/ActivityLogManager.php';

$activityLogManager = new ActivityLogManager();

function h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }

$logs = $activityLogManager->getRecentLogs(200);
$stats = $activityLogManager->getStats();
$typeRows = $activityLogManager->getTypeRows();
$dailyRows = $activityLogManager->getDailyRows();
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymTracker Admin - Activity Logs</title>
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
            <li onclick="redirectAdmin(6)"><i class="material-icons icon">self_improvement</i><span class="text">Workouts</span></li>
            <li onclick="redirectAdmin(4)"><i class="material-icons icon">bar_chart</i><span class="text">Reports</span></li>
            <li class="active" onclick="redirectAdmin(5)"><i class="material-icons icon">receipt_long</i><span class="text">Activity Logs</span></li>
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
    <div class="top-bar"><div class="greeting"><h1>Activity Logs</h1><p>Audit recent platform and user activity from your database.</p></div><div class="toggle-thumb"><i class="material-icons">brightness_5</i></div></label></div></div>
    <section class="admin-hero"><div><span class="section-kicker">Audit Trail</span><h2>Monitor actions and workout events.</h2></div><div class="hero-icon"><i class="material-icons">receipt_long</i></div></section>
    <div class="stats-row">
        <div class="stat-card"><div class="stat-icon"><i class="material-icons">format_list_numbered</i></div><div><div class="stat-value"><?= $stats['all'] ?></div><div class="stat-label">Total Logs</div></div></div>
        <div class="stat-card"><div class="stat-icon"><i class="material-icons">today</i></div><div><div class="stat-value"><?= $stats['today'] ?></div><div class="stat-label">Today</div></div></div>
        <div class="stat-card"><div class="stat-icon"><i class="material-icons">self_improvement</i></div><div><div class="stat-value"><?= $stats['workout'] ?></div><div class="stat-label">Workout Linked</div></div></div>
        <div class="stat-card"><div class="stat-icon"><i class="material-icons">fitness_center</i></div><div><div class="stat-value"><?= $stats['exercise'] ?></div><div class="stat-label">Exercise Linked</div></div></div>
    </div>
    <div class="grid-main">
        <section class="card"><div class="card-header"><div><div class="card-title">Activity Volume</div><div class="card-subtitle">User activity logs during the last 30 days.</div></div></div><div class="chart-wrap"><canvas id="dailyChart"></canvas></div></section>
        <section class="card"><div class="card-header"><div><div class="card-title">Activity Types</div><div class="card-subtitle">Most frequent user activity categories.</div></div></div><div class="chart-wrap"><canvas id="typeChart"></canvas></div></section>
    </div>
    <section class="card">
        <div class="card-header"><div><div class="card-title">Recent Logs</div><div class="card-subtitle">Latest 200 records from activity tables.</div></div></div>
        <div class="table-wrap"><table class="manager-table"><thead><tr><th>Time</th><th>User</th><th>Source</th><th>Type</th><th>Linked Record</th><th>Description</th><th>Status</th></tr></thead><tbody>
            <?php foreach ($logs as $log): ?>
            <tr>
                <td><?= h($log['activity_time']) ?></td>
                <td><div class="entity-cell"><div class="entity-icon"><i class="material-icons">person</i></div><div class="entity-name"><?= h($log['username'] ?: 'Unknown') ?></div></div></td>
                <td><span class="badge <?= $log['source'] === 'System' ? 'orange' : 'green' ?>"><?= h($log['source']) ?></span></td>
                <td><?= h($log['activity_type'] ?: 'Action') ?></td>
                <td><?= h($log['workout_name'] ?: $log['exercise_name'] ?: 'None') ?></td>
                <td><?= h($log['description']) ?></td>
                <td><span class="badge gray"><?= h($log['status'] ?: 'logged') ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody></table></div>
    </section>
</main>
<footer>
    <div class="footer-container">
        <div class="footer-brand">&#128170;<span class="brand-gym">Gym</span>Tracker</div>
    </div>
    <div class="footer-copyright">&copy; 2026 Gym Tracker. Built with &hearts; for fitness lovers by Niko</div>
</footer>
<script src="/workout_trackersys/scripts/redirect.js"></script><script src="/workout_trackersys/scripts/magic.js"></script>

<script>
// Bootstrapped for /scripts/AdminActivity.js
window.__ADMIN_ACTIVITY_DAILY_ROWS__ = <?= json_encode($dailyRows) ?>;
window.__ADMIN_ACTIVITY_TYPE_ROWS__ = <?= json_encode($typeRows) ?>;
</script>
<script src="/workout_trackersys/scripts/AdminActivity.js"></script>
</body>
</html>
