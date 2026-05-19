<?php
require_once __DIR__ . '/../../BL/exerciseManage.php';
require_once __DIR__ . '/../../BL/WorkoutManager.php';
$em = new ExerciseManage();
$wm = new WorkoutManager();
$stats = $em->getUserStats();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/workout_trackersys/assets/exercises.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>GymTracker - Exercises</title>
    <link rel="stylesheet" href="/workout_trackersys/assets/user-enhancements.css">    <link rel="stylesheet" href="/workout_trackersys/assets/footer.css">
</head>
<body>
    <div class="Sidebar" id="sidebar">
        <div class="NavLinks">
            <button class="toggle-btn" onclick="ToggleSidebar()"><i class="material-icons">menu</i></button>
        <div class="sidebar-brand"><span class="sidebar-logo" aria-hidden="true">&#128170;</span><span class="text"><span class="brand-gym">Gym</span>Tracker</span></div>
            <ul>
                <li onclick="redirectUser(1)">
                    <i class="material-icons icon">dashboard</i>
                    <span class="text">Dashboard</span>
                </li>

                <li onclick="redirectUser(2)">
                    <i class="material-icons icon">fitness_center</i>
                    <span class="text">Workouts</span>
                </li>

                <li class="active" onclick="redirectUser(3)">
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

    <!-- MAIN -->
    <div class="main-content">
        <!-- TOP BAR -->
        <section class="page-hero">
            <div>
                <span class="section-kicker">Exercise Library</span>
                <h2>Exercises</h2>
                <p>Search, filter, and learn proper form for every exercise.</p>
            </div>
            <div class="hero-icon"><i class="material-icons">directions_run</i></div>
        </section>

        <!-- SEARCH & FILTERS -->
        <div class="search-filter-bar">
            <div class="search-wrap">
                <span class="material-icons">search</span>
                <input class="search-input" id="searchInput" placeholder="Search exercises..." oninput="filterExercises()">
            </div>
            <div class="filter-pills">
                <button class="pill active" onclick="setFilter('all', this)">All</button>
                <button class="pill" onclick="setFilter('chest', this)"><i class="material-icons" style="font-size:14px;vertical-align:middle">fitness_center</i> Chest</button>
                <button class="pill" onclick="setFilter('back', this)"><i class="material-icons" style="font-size:14px;vertical-align:middle">accessibility_new</i> Back</button>
                <button class="pill" onclick="setFilter('shoulders', this)"><i class="material-icons" style="font-size:14px;vertical-align:middle">sports_gymnastics</i> Shoulders</button>
                <button class="pill" onclick="setFilter('legs', this)"><i class="material-icons" style="font-size:14px;vertical-align:middle">directions_walk</i> Legs</button>
                <button class="pill" onclick="setFilter('arms', this)"><i class="material-icons" style="font-size:14px;vertical-align:middle">sports_handball</i> Arms</button>
                <button class="pill" onclick="setFilter('core', this)"><i class="material-icons" style="font-size:14px;vertical-align:middle">local_fire_department</i> Core</button>
                <button class="pill" onclick="setFilter('cardio', this)"><i class="material-icons" style="font-size:14px;vertical-align:middle">directions_run</i> Cardio</button>
                <button class="pill" onclick="setFilter('full-body', this)"><i class="material-icons" style="font-size:14px;vertical-align:middle">bolt</i> Full Body</button>
            </div>
        </div>

        <!-- STATS STRIP -->
        <div class="stats-strip">
            <div class="strip-card">
                <div class="strip-icon"><i class="material-icons">menu_book</i></div>
                <div class="strip-info">
                    <div class="strip-val" id="totalCount"><?= $stats['total'] ?></div>
                    <div class="strip-label">Total Exercises</div>
                </div>
            </div>
            <div class="strip-card">
                <div class="strip-icon"><i class="material-icons">fitness_center</i></div>
                <div class="strip-info">
                    <div class="strip-val"><?= $stats['muscles'] ?></div>
                    <div class="strip-label">Muscle Groups</div>
                </div>
            </div>
            <div class="strip-card">
                <div class="strip-icon"><i class="material-icons">home</i></div>
                <div class="strip-info">
                    <div class="strip-val"><?= $stats['home'] ?></div>
                    <div class="strip-label">Home Friendly</div>
                </div>
            </div>
            <div class="strip-card">
                <div class="strip-icon">⭐</div>
                <div class="strip-info">
                    <div class="strip-val"><?= $stats['beginner'] ?></div>
                    <div class="strip-label">Beginner Picks</div>
                </div>
            </div>
        </div>

        <!-- EXERCISE GRID -->
        <div class="exercise-grid" id="exerciseGrid"></div>

    </div>

    <!-- EXERCISE DETAIL MODAL -->
    <div class="modal-overlay" id="modalOverlay" onclick="closeModal(event)">
        <div class="ex-modal">
            <button class="modal-close-btn" onclick="closeModalDirect()">✕</button>
            <div class="modal-ex-header">
                <div class="modal-ex-emoji" id="modalEmoji">🏋️</div>
                <div>
                    <div class="modal-ex-name" id="modalName">—</div>
                    <span class="muscle-badge" id="modalMuscle">—</span>
                </div>
            </div>
            <div class="modal-section-title">About</div>
            <div class="modal-desc" id="modalDesc">—</div>
            <div class="modal-section-title">How to perform</div>
            <div class="modal-steps" id="modalSteps"></div>
            <div class="modal-section-title">Tags</div>
            <div class="modal-tags" id="modalTags"></div>
            <div class="modal-addworkout" id="addToWorkout">
                <button type="button" class="add-to-workout" onclick="toggleAddPanel()">Add To Workout</button>
            </div>
            <div class="add-to-workout-panel" id="addToWorkoutPanel">

                <!-- Step 1: Pick a workout -->
                <div id="step-pick">
                    <div class="panel-title">Step 1 — Select a Workout</div>
                    <div class="workout-select-list" id="workoutSelectList">
                        <!-- populated by JS -->
                    </div>
                </div>

                <!-- Step 2: Dynamic Exercise Form -->
                <div class="srw-form" id="srwForm">
                    <div class="panel-title">Step 2 — Set Details</div>

                    <!-- 🔥 STRENGTH -->
                    <div class="type-section" id="strengthFields">
                        <div class="srw-grid">
                            <div class="srw-field">
                                <label>Sets</label>
                                <input type="number" id="inputSets" placeholder="3" min="1" max="20">
                            </div>
                            <div class="srw-field">
                                <label>Reps</label>
                                <input type="number" id="inputReps" placeholder="10" min="1" max="100">
                            </div>
                            <div class="srw-field">
                                <label>Weight (kg)</label>
                                <input type="number" id="inputWeight" placeholder="0" min="0" max="500">
                            </div>
                            <div class="srw-field">
                                <label>Rest (seconds)</label>
                                <input type="number" id="inputRest" placeholder="60" min="0" max="600">
                            </div>
                        </div>
                    </div>

                    <!-- 🏃 CARDIO -->
                    <div class="type-section" id="cardioFields" style="display:none;">
                        <div class="srw-grid">
                            <div class="srw-field">
                                <label>Duration (minutes)</label>
                                <input type="number" id="inputDuration" placeholder="30" min="1" max="600">
                            </div>
                            <div class="srw-field">
                                <label>Distance (km)</label>
                                <input type="number" id="inputDistance" placeholder="5" step="0.1" min="0" max="1000">
                            </div>
                        </div>
                    </div>

                    <!-- 💪 BODYWEIGHT -->
                    <div class="type-section" id="bodyweightFields" style="display:none;">
                        <div class="srw-grid">
                            <div class="srw-field">
                                <label>Sets</label>
                                <input type="number" id="bwSets" placeholder="3" min="1" max="600">
                            </div>
                            <div class="srw-field">
                                <label>Reps</label>
                                <input type="number" id="bwReps" placeholder="12 " min="1" max="600">
                            </div>
                            <div class="srw-field">
                                <label>Rest</label>
                                <input type="number" id="bwRest" placeholder="60" min="1" max="600">
                            </div>
                        </div>
                    </div>

                    <!-- 🧘 FLEXIBILITY -->
                    <div class="type-section" id="flexibilityFields" style="display:none;">
                        <div class="srw-grid">
                            <div class="srw-field">
                                <label>Duration (minutes)</label>
                                <input type="number" id="flexDuration" placeholder="10" min="1" max="600">
                            </div>
                            <div class="srw-field">
                                <label>Hold Time (seconds)</label>
                                <input type="number" id="flexHold" placeholder="30" min="1" max="600">
                            </div>
                        </div>
                    </div>

                    <button class="srw-confirm-btn" onclick="confirmAddToWorkout()">
                        <span class="material-icons" style="font-size:18px">check_circle</span>
                        Confirm & Add Exercise
                    </button>

                </div>

            </div>
        </div>
    </div>

    <script src="/workout_trackersys/scripts/redirect.js"></script>
    <script src="/workout_trackersys/scripts/exerciseServices.js"></script>
<footer>
    <div class="footer-container">
        <div class="footer-brand">&#128170;<span class="brand-gym">Gym</span>Tracker</div>
    </div>
    <div class="footer-copyright">&copy; 2026 Gym Tracker. Built with &hearts; for fitness lovers by Niko</div>
</footer>
</body>
</html>

