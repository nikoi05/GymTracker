<?php
require_once __DIR__ . '/../../BL/AdminReportsManager.php';

function h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }

$reportsManager = new AdminReportsManager();
$stats = $reportsManager->getDashboardStats();
$activityRows = $reportsManager->getActivityRows();
$registrationRows = $reportsManager->getRegistrationRows();
$goalRows = $reportsManager->getGoalRows();
$muscleRows = $reportsManager->getMuscleRows();
$topUsers = $reportsManager->getTopUsers();
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymTracker Admin - Reports</title>
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
            <li class="active" onclick="redirectAdmin(4)"><i class="material-icons icon">bar_chart</i><span class="text">Reports</span></li>
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
    <div class="top-bar"><div class="greeting"><h1>Reports</h1><p>Charts and platform insights powered by your workout tracker database.</p></div><div class="toggle-thumb"><i class="material-icons">brightness_5</i></div></label></div></div>
    <section class="admin-hero"><div><span class="section-kicker">Analytics</span><h2>Performance reports</h2><p>Track registrations, workout activity, goal status, and the most used muscle categories.</p></div><div class="hero-icon"><i class="material-icons">query_stats</i></div></section>
    <div class="stats-row">
        <div class="stat-card"><div class="stat-icon"><i class="material-icons">groups</i></div><div><div class="stat-value"><?= $stats['users'] ?></div><div class="stat-label">Users</div></div></div>
        <div class="stat-card"><div class="stat-icon"><i class="material-icons">today</i></div><div><div class="stat-value"><?= $stats['activeToday'] ?></div><div class="stat-label">Active Today</div></div></div>
        <div class="stat-card"><div class="stat-icon"><i class="material-icons">done_all</i></div><div><div class="stat-value"><?= $stats['sessions'] ?></div><div class="stat-label">Sessions</div></div></div>
        <div class="stat-card"><div class="stat-icon"><i class="material-icons">timer</i></div><div><div class="stat-value"><?= $stats['minutes'] ?></div><div class="stat-label">Total Minutes</div></div></div>
    </div>
    <div class="grid-two">
        <section class="card"><div class="card-header"><div><div class="card-title">Workout Minutes</div><div class="card-subtitle">Total completed duration by day.</div></div></div><div class="chart-wrap"><canvas id="activityChart"></canvas></div></section>
        <section class="card"><div class="card-header"><div><div class="card-title">User Registrations</div><div class="card-subtitle">New accounts by day.</div></div></div><div class="chart-wrap"><canvas id="registrationChart"></canvas></div></section>
        <section class="card"><div class="card-header"><div><div class="card-title">Goal Status</div><div class="card-subtitle">Current goal pipeline.</div></div></div><div class="chart-wrap small-chart"><canvas id="goalChart"></canvas></div></section>
        <section class="card"><div class="card-header"><div><div class="card-title">Muscles Trained</div><div class="card-subtitle">Based on workout exercise assignments.</div></div></div><div class="chart-wrap small-chart"><canvas id="muscleChart"></canvas></div></section>
    </div>
    <section class="card"><div class="card-header"><div><div class="card-title">Top Users by Sessions</div><div class="card-subtitle">Sorted by completed and logged workout sessions.</div></div></div><div class="table-wrap"><table class="manager-table"><thead><tr><th>User</th><th>Sessions</th><th>Total Minutes</th></tr></thead><tbody><?php foreach ($topUsers as $u): ?><tr><td><div class="entity-cell"><div class="entity-icon"><i class="material-icons">person</i></div><div class="entity-name"><?= h($u['username']) ?></div></div></td><td><?= (int)$u['sessions'] ?></td><td><?= (int)$u['minutes'] ?></td></tr><?php endforeach; ?></tbody></table></div></section>
</main>
<footer>
    <div class="footer-container">
        <div class="footer-brand">&#128170;<span class="brand-gym">Gym</span>Tracker</div>
    </div>
    <div class="footer-copyright">&copy; 2026 Gym Tracker. Built with &hearts; for fitness lovers by Niko</div>
</footer>
<script src="/workout_trackersys/scripts/redirect.js"></script><script src="/workout_trackersys/scripts/magic.js"></script>

<script>
// Bootstrapped for /scripts/AdminReports.js
window.__ADMIN_REPORTS_DATASETS__ = {
  activity: <?= json_encode($activityRows) ?>,
  registration: <?= json_encode($registrationRows) ?>,
  goals: <?= json_encode($goalRows) ?>,
  muscles: <?= json_encode($muscleRows) ?>
};
</script>
<script src="/workout_trackersys/scripts/AdminReports.js"></script>
</body>
</html>
