
<?php 
require_once __DIR__ . '/../../BL/userManager.php';
$user = new managerUser();
require_once __DIR__ . '/../../BL/WorkoutSessionManage.php';
require_once __DIR__ . '/../../BL/GoalsManager.php';
$gm = new GoalsManager();
$wsm = new WorkoutSessionManage();
$totalTime = $wsm->GetTotalDuration($_SESSION['userID']);
$totalWorkoutMonth = $wsm->GetTotalWorkoutMonth($_SESSION['userID']);
$ActiveGoals = $gm->GetGoalsInfos($_SESSION['userID']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="/workout_trackersys/assets/dashboard.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
          <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <!-- Compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
            
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">  
    <title>DASHBOARD</title>
</head>
<body>
 <div class="intro-loader">
    <h1 class="siteName"><span>Welcome!, </span><?= $_SESSION['username']?></h1>
    </div>
<!-- SIDEBAR -->
<div class="Sidebar" id="sidebar">

    <div class="NavLinks">
        <button class="toggle-btn" onclick="ToggleSidebar()">☰</button>
        <span class="text">GymTracker</span>
        <ul>
            <li class="active" onclick="redirectUser(1)">
                <i class="material-icons icon">dashboard</i>
                <span class="text">Dashboard</span>
            </li>

            <li href="#" onclick="redirectUser(2)">
                <i class="material-icons icon">fitness_center</i>
                <span class="text">Workouts</span>
            </li>

            <li onclick="redirectUser(3)">
                <i class="material-icons icon">directions_run</i>
                <span class="text">Exercises</span>
            </li>

            <li onclick="redirectUser(4)">
                <i class="material-icons icon">show_chart</i>
                <span class="text">Progress</span>
            </li>


        </ul>
    </div>



    <div class="Profile-Settings">
        <ul>
            <li onclick="redirectUser(8)">
                <i class="material-icons icon">person</i>
                <span class="text">Profile</span>
            </li>

            <li onclick="redirectUser(7)">
                <i class="material-icons icon">settings</i>
                <span class="text">Settings</span>
            </li>
        </ul>
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
                <h3>Hello, <?= $_SESSION['username'] ?></h3>
                <h5>let’s take a look at your activity today</h5>
            </div>
            <div class="toggle-wrap">
                <span class="toggle-label" id="theme-label">Light</span>
                <label class="toggle">
                    <input type="checkbox" id="themeToggle">
                    <div class="toggle-track"></div>
                    <div class="toggle-thumb"><i class="material-icons light-icon">brightness_5</i></div>
                </label>
            </div>
        </div>
         <div class="stats-grid">
        <div class="stat-card" style="animation-delay:0.05s">
            <div class="stat-top">
                <div class="stat-icon">🏋️</div>
            </div>
            <div class="stat-val" id="statWorkouts"><?=  $totalWorkoutMonth['TotalWorkouts'] ?></div>
            <div class="stat-label">Workouts This Month</div>
        </div>
        <div class="stat-card" style="animation-delay:0.10s">
            <div class="stat-top"><div class="stat-icon">⏱️</div>
        </div>
        <div class="stat-val" id="statHours"><?= $totalTime ?>
    </div>
    <div class="stat-label">Total Time Trained</div>
</div>
        <div class="stat-card" style="animation-delay:0.15s">
            <div class="stat-top">
                <div class="stat-icon">🎯</div>
                <span class="stat-change neutral">steady</span>
            </div>
            <div class="stat-val" id="statGoals"><?= $ActiveGoals[0]['ActiveGoals'] ?></div>
            <div class="stat-label">Active Goals</div>
        </div>
        <div class="stat-card" style="animation-delay:0.20s">
            <div class="stat-top">
                <div class="stat-icon">📈</div>
           
        </div
        ><div class="stat-val" id="statPR"></div><div class="stat-label">Streak</div>
        </div>
    </div>
 
    <div class="grid-main">
        <div class="card" style="animation-delay:0.1s">
            <div class="card-header">
                <div class="card-title">Activity Overview</div>
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
                <div class="qa-btn" onclick="redirectUser(2)"><div class="qa-icon">💪</div><div class="qa-label">Workouts</div></div>
                <div class="qa-btn" onclick="redirectUser(3)"><div class="qa-icon">📚</div><div class="qa-label">Exercises</div></div>
                <div class="qa-btn" onclick="redirectUser(4)"><div class="qa-icon">📈</div><div class="qa-label">Progress</div></div>

                <div class="qa-btn" onclick="redirectUser(5)"><div class="qa-icon">🎯</div><div class="qa-label">Goals</div></div>
            </div>
        </div>
    </div>
 
    <div class="grid-bot">
        <div class="card" style="animation-delay:0.2s">
            <div class="card-header"><div class="card-title">Recent Workouts</div></div>
            <div class="workout-list" id="recentWorkoutList">
                <div style="text-align:center;padding:30px;color:var(--text-muted);font-size:13px" class="loading-State">Loading...

                </div>
        </div>
        </div>
        <div class="card" style="animation-delay:0.25s">
            <div class="card-header"><div class="card-title">Goals Snapshot</div><button class="btn-ghost" onclick="redirectUser(5)" style="font-size:12px;padding:6px 14px">View All</button></div>
            <div id="goalSnapshotList" style="display:flex;flex-direction:column;gap:14px"><div class="loading-State" style="text-align:center;padding:30px;color:var(--text-muted);font-size:13px">Loading...</div></div>
        </div>
    </div>
        
        
        </div>

</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/workout_trackersys/scripts/redirect.js"></script>
<script src="/workout_trackersys/scripts/dashboard.js"></script>
<script src="/workout_trackersys/scripts/magic.js"></script>
<script src="/workout_trackersys/scripts/authservices.js"></script>

</html>