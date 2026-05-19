<?php 
require_once __DIR__ . '/../../BL/userManager.php';
require_once __DIR__ . '/../../BL/WorkoutManager.php';
require_once __DIR__ . '/../../BL/ActivityLogManager.php';
$user = new managerUser();
$usercount = $user->getAllUsers();
$activetoday = $user->getActiveToday();
 
$wm = new WorkoutManager();
$totalWorkouts = $wm->totalWorkoutsLogged();
$activityLogManager = new ActivityLogManager();
$activityStats = $activityLogManager->getStats();
?>


<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymTracker Admin - Dashboard</title>
    <link rel="stylesheet" href="/workout_trackersys/assets/Admindashboard.css">
    <link rel="stylesheet" href="/workout_trackersys/assets/AdminUsers.css">
    <link rel="stylesheet" href="/workout_trackersys/assets/admin-managers.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/workout_trackersys/assets/footer.css">
</head>

<body>
 <div class="intro-loader">
    <h1 class="siteName"><span>Welcome!, </span><?= $_SESSION['username']?></h1>
 </div>

<!-- SIDEBAR -->
<div class="Sidebar" id="sidebar">
    <div class="NavLinks">
        <button class="toggle-btn" onclick="ToggleSidebar()"><i class="material-icons">menu</i></button>
        <div class="sidebar-brand"><span class="sidebar-logo" aria-hidden="true">&#128170;</span><span class="text"><span class="brand-gym">Gym</span>Tracker Admin</span></div>
        <ul>
            <li class="active" onclick="redirectAdmin(1)">
                <i class="material-icons icon">dashboard</i>
                <span class="text">Dashboard</span>
            </li>
            <li onclick="redirectAdmin(2)">
                <i class="material-icons icon">people</i>
                <span class="text">Users</span>
            </li>
            <li onclick="redirectAdmin(3)">
                <i class="material-icons icon">fitness_center</i>
                <span class="text">Exercises</span>
            </li>
            <li onclick="redirectAdmin(6)">
                <i class="material-icons icon">self_improvement</i>
                <span class="text">Workouts</span>
            </li>
            <li onclick="redirectAdmin(4)">
                <i class="material-icons icon">bar_chart</i>
                <span class="text">Reports</span>
            </li>
            <li onclick="redirectAdmin(5)">
                <i class="material-icons icon">receipt_long</i>
                <span class="text">Activity Logs</span>
            </li>
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

<!-- MAIN CONTENT -->
<div class="main-content">
    <div class="main-container">
        <div class="top-bar">
             <div class="greeting">
                <h3>Hello, Admin <?= $_SESSION['username'] ?></h3>
                <h5>let&rsquo;s take a look at your platform activity today</h5>
            </div>
        </div>
        
        <div class="stats-row">
            <div class="stat-card" style="animation-delay:0.05s">
                <div class="stat-icon"><i class="material-icons">groups</i></div>
                <div>
                    <div class="stat-val" id="statUsers"><?= $usercount ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
            </div>
            <div class="stat-card" style="animation-delay:0.10s">
                <div class="stat-icon"><i class="material-icons">fitness_center</i></div>
                <div>
                    <div class="stat-val" id="statWorkouts"><?= $totalWorkouts ?></div>
                    <div class="stat-label">Workouts Logged</div>
                </div>
            </div>
            <div class="stat-card" style="animation-delay:0.15s">
                <div class="stat-icon"><i class="material-icons">trending_up</i></div>
                <div>
                    <div class="stat-val" id="statActive"><?= $activetoday ?></div>
                    <div class="stat-label">Active Today</div>
                </div>
            </div>
            <div class="stat-card" style="animation-delay:0.20s">
                <div class="stat-icon"><i class="material-icons">receipt_long</i></div>
                <div>
                    <div class="stat-val" id="statAlerts"><?= $activityStats['today'] ?></div>
                    <div class="stat-label">Logs Today</div>
                </div>
            </div>
        </div>
 
        <div class="grid-main">
            <div class="card" style="animation-delay:0.1s">
                <div class="card-header">
                    <div class="card-title">Platform Analytics</div>
                    <div class="chart-tabs">
                        <button class="chart-tab active">Weekly</button>
                        <button class="chart-tab">Monthly</button>
                    </div>
                </div>
                <div class="chart-wrap"><canvas id="activityChart"></canvas></div>
            </div>
            <div class="card" style="animation-delay:0.15s">
                <div class="card-header"><div class="card-title">Quick Actions</div></div>
                <div class="quick-actions">
                    <div class="qa-btn" onclick="redirectAdmin(2)"><div class="qa-icon"><i class="material-icons">people</i></div><div class="qa-label">Users</div></div>
                    <div class="qa-btn" onclick="redirectAdmin(3)"><div class="qa-icon"><i class="material-icons">fitness_center</i></div><div class="qa-label">Exercises</div></div>
                    <div class="qa-btn" onclick="redirectAdmin(6)"><div class="qa-icon"><i class="material-icons">self_improvement</i></div><div class="qa-label">Workouts</div></div>
                    <div class="qa-btn" onclick="redirectAdmin(4)"><div class="qa-icon"><i class="material-icons">bar_chart</i></div><div class="qa-label">Reports</div></div>
                    <div class="qa-btn" onclick="redirectAdmin(5)"><div class="qa-icon"><i class="material-icons">receipt_long</i></div><div class="qa-label">Logs</div></div>
                </div>
            </div>
           
        </div>
        
 
        <div class="grid-bot">
             <div class="card">
                <div class="card-header">
                    <div class="card-title">User Registrations</div>

                    <div class="chart-tabs">
                        <button class="chart-tab active">Weekly</button>
                        <button class="chart-tab">Monthly</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-wrap">
                    <canvas id="UserReg"></canvas>
                </div>
                </div>
            </div>
             <div class="card">
                <div class="card-header">
                    <div class="card-title">Activity Total Duration <span style="color: var(--text-muted);">mins</span></div>

                    <div class="chart-tabs">
                        <button class="chart-tab active">Weekly</button>
                        <button class="chart-tab">Monthly</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-wrap">
                    <canvas id="ActivityChartsAll"></canvas>
                </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- SCRIPTS -->
<script src="/workout_trackersys/scripts/redirect.js"></script>
<script src="/workout_trackersys/scripts/AdminDash.js"></script>
<script src="/workout_trackersys/scripts/magic.js"></script>



<footer>
    <div class="footer-container">
        <div class="footer-brand">&#128170;<span class="brand-gym">Gym</span>Tracker</div>
    </div>
    <div class="footer-copyright">&copy; 2026 Gym Tracker. Built with &hearts; for fitness lovers by Niko</div>
</footer>
</body>
</html>
