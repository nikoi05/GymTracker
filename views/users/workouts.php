<?php 

require_once __DIR__ . '/../../BL/WorkoutManager.php'; 
require_once __DIR__ . '/../../BL/WorkoutExerciseManager.php';
$wem = new WorkoutExerciseManager();
$wm = new WorkoutManager();
$userID = $_SESSION['userID'];
$workouts=$wm->readWorkouts($userID);

 
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/workout_trackersys/assets/workouts.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
          <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <!-- Compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">  
    <!--- DATATABLE--->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.min.css">
    <script src="https://cdn.datatables.net/2.3.7/js/dataTables.js"></script>
    <title>Document</title>
</head>
<body>
    <div class="Sidebar" id="sidebar">
    <div class="NavLinks">
        <button class="toggle-btn" onclick="ToggleSidebar()">☰</button>
        <span class="text">WorkoutTracker</span>
        <ul>
            <li  onclick="redirectUser(1)">
                <i class="material-icons icon">dashboard</i>
                <span class="text">Dashboard</span>
            </li>

            <li class="active" onclick="redirectUser(2)">
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
                <div class="title-header">
                    <h2>My Workouts</h2>
                    <h5>Check Your Workouts, Add or Remove</h5>
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
            
           <section class="main">
                <div class="workout-wrap">
    <div class="workout-header">
        <button class="btn-add" onclick="openModal();">+ Add Workout</button>
    </div>

    <div class="workout-container">
        <table id="workoutTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Workout Details</th> </tr>
            </thead>
            <tbody>
<?php if (!empty($workouts)): ?>
                    <?php foreach ($workouts as $workout): ?>
                        <?php 
                            $workoutID = $workout['workoutID'];
                            $totals = $wem->getTotalCard($workoutID);
                            $totalSets = $totals[0]['total_Sets'] ?? 0;
                            $totalExercises = $totals[0]['total_exercises'] ?? 0;
                            $totalDuration = $totals[0]['total_minutes'] ?? 0;
                        ?>
                        <tr>
                            <td>
                                <div class="workout-card" data-id="<?= htmlspecialchars($workout['workoutID']) ?>">
                                    <div class="card-header">
                                        <h3><?= htmlspecialchars($workout['name']) ?></h3>
                                        <span class="workout-stats">
                                            <span class="stat-item">
                                                <strong><?= htmlspecialchars($totalSets) ?></strong> Sets
                                            </span>
                                            <span class="stat-item">
                                                <strong><?= htmlspecialchars($totalExercises) ?></strong> Exercises
                                            </span>
                                            <span class="stat-item">
                                                <strong><?= htmlspecialchars($totalDuration) ?></strong> min
                                            </span>
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <?= htmlspecialchars($workout['workout_description']) ?>
                                    </div>
                        
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</section>
</div>
<div class="modal" id="addWorkoutModal">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h3>Add New Workout</h3>
                    <p>Plan your next session and stay on track.</p>
                </div>
                <span class="close-modal" id="closeAddModal" onclick="closeModal()">&times;</span>
            </div>

            <form id="addWorkoutForm">
                <div class="form-group">
                    <label for="workoutName">Workout Name</label>
                    <input type="text" id="workoutName" name="name" placeholder="e.g. Morning Upper Body" required minlength="3" maxlength="100">
                </div>

                <div class="form-group">
                    <label for="workoutDescription">Workout Description</label>
                    <textarea name="description" id="workoutDescription" rows="4" maxlength="500" placeholder="What exercises are we doing today?"></textarea>
                    <div style="display: flex; justify-content: flex-end;">
                        <small id="charCount" style="color: var(--text-muted);">0 / 500</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="button" class="btn-save" onclick="addWorkout()">Save Workout</button>
                </div>
            </form>
        </div>

    <!-- Scripts at the bottom -->
    <script src="/workout_trackersys/scripts/redirect.js"></script>
    <script src="/workout_trackersys/scripts/workouts.js"></script>
</body>
</html>