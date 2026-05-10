
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymTracker — Goals & Progress</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/workout_trackersys/assets/shared.css">
    <link rel="stylesheet" href="/workout_trackersys/assets/progress-goals.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>

    </style>
</head>
<body>
<!-- SIDEBAR -->
<div class="Sidebar" id="sidebar">
    <div class="NavLinks">
        <button class="toggle-btn" onclick="ToggleSidebar()">☰</button>
        <span class="text">WorkoutTracker</span>
        <ul>
            <li onclick="redirectUser(1)"><i class="material-icons icon">dashboard</i><span class="text">Dashboard</span></li>
            <li onclick="redirectUser(2)"><i class="material-icons icon">fitness_center</i><span class="text">Workouts</span></li>
            <li onclick="redirectUser(3)"><i class="material-icons icon">directions_run</i><span class="text">Exercises</span></li>
            <li class="active"><i class="material-icons icon">show_chart</i><span class="text">Progress</span></li>
        </ul>
    </div>

    <div class="Profile-Settings"><ul>
        <li onclick="redirectUser(8)"><i class="material-icons icon">person</i><span class="text">Profile</span></li>
        <li onclick="redirectUser(7)"><i class="material-icons icon">settings</i><span class="text">Settings</span></li>
    </ul></div>
    <div class="Logout"><button type="button" onclick="LogoutFunc()"><i class="material-icons">logout</i><span class="text">Logout</span></button></div>
</div>

<!-- MAIN -->
<div class="main-content">

    <!-- TOP BAR -->
    <div class="top-bar">
        <div class="title-header">
            <h2>Goals & Progress</h2>
            <h5>Track your fitness goals, charts, and personal bests</h5>
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

    <!-- STATS -->
    <div class="stats-row">
        <div class="stat-card" style="animation-delay:0.05s">
            <div class="stat-top"><div class="stat-icon">🎯</div></div>
            <div class="stat-val" id="statActive">—</div>
            <div class="stat-label">Active Goals</div>
        </div>
        <div class="stat-card" style="animation-delay:0.10s">
             <div class="stat-top"><div class="stat-icon">🎯</div></div>
            <div class="stat-val" id="statCompleted">—</div>
            <div class="stat-label">Goals Completed</div>
        </div>
        <div class="stat-card" style="animation-delay:0.15s">

            <div class="stat-top"><div class="stat-icon">🔥</div></div>
            <div class="stat-val" id="statWorkout">—</div>
            <div class="stat-label">Workouts This Month</div>
        </div>
        <div class="stat-card" style="animation-delay:0.20s">
            <div class="stat-top"><div class="stat-icon">📈</div></div>
            <div class="stat-val" id="statAvgProgress">—</div>
            <div class="stat-label">Avg Goal Progress</div>
        </div>
    </div>


    <!-- ════════════════ GOALS ════════════════ -->
    <div class="section-divider"><h3>🎯 My Goals</h3></div>

    <div class="add-goal-row">
        <button class="btn-primary" onclick="openAddGoal()">
            <span class="material-icons" style="font-size:18px">add_circle</span> Add New Goal
        </button>
    </div>

    <div class="goals-grid" id="goalsGrid">
        <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--text-muted)">Loading goals...</div>
    </div>

    <!-- ════════════════ PROGRESS CHARTS ════════════════ -->
    <div class="section-divider"><h3>📈 Progress Charts</h3></div>

    <div class="progress-grid">

        <!-- Frequency Chart -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">Workout Frequency</div>
                <div class="chart-tabs">
                    <button class="chart-tab active" onclick="switchFreqChart('weekly',this)">Weekly</button>
                    <button class="chart-tab" onclick="switchFreqChart('monthly',this)">Monthly</button>
                </div>
            </div>
            <div class="chart-wrap"><canvas id="freqChart"></canvas></div>
        </div>

        <!-- Other Metric (no extra backend query) -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">Consistency Score ✅</div>
                <span class="card-badge">Last 12 weeks</span>
            </div>
            <div class="metric-list" id="metricList">
                <div style="text-align:center;padding:20px;color:var(--text-muted);font-size:13px">Loading...</div>
            </div>
        </div>
    </div>

    <!-- Volume Chart (full width) -->
    <div class="card full-card">
        <div class="card-header">
            <div class="card-title">Volume Over Time (kg lifted)</div>
            <span class="card-badge">Last 12 weeks</span>
        </div>
        <div class="chart-wrap" style="height:200px"><canvas id="volumeChart"></canvas></div>
    </div>

    <!-- ════════════════ ACTIVITY HEATMAP ════════════════ -->
    <div class="section-divider"><h3>📅 Activity Heatmap</h3></div>

    <div class="card heatmap-wrap">
        <div class="card-header">
            <div class="card-title">Workout Activity</div>
            <span class="card-badge">Last 3 months</span>
        </div>
        <div class="heatmap-grid" id="heatmapGrid"></div>
        <div class="heatmap-legend">
            <span>Less</span>
            <div class="heatmap-legend-cells">
                <div class="legend-cell" style="background:var(--surface-2);border:1px solid var(--border)"></div>
                <div class="legend-cell" style="background:rgba(56,189,248,0.20)"></div>
                <div class="legend-cell" style="background:rgba(56,189,248,0.45)"></div>
                <div class="legend-cell" style="background:rgba(59,130,246,0.70)"></div>
                <div class="legend-cell" style="background:#3B82F6"></div>
            </div>
            <span>More</span>
        </div>
    </div>

</div>

<!-- ADD / UPDATE GOAL MODAL -->
<div class="modal-overlay" id="goalModal" onclick="closeModalOutside(event)">
    <div class="modal-box">
        <button class="modal-close" onclick="closeGoalModal()">✕</button>
        <div class="modal-title" id="modalTitle">Add New Goal</div>
        <div class="modal-subtitle">Set a clear, measurable target to stay motivated.</div>

        <input type="hidden" id="goalEditId">

        <div class="form-group">
            <label class="form-label">Goal Name</label>
            <input type="text" class="form-input" id="goalName" placeholder="e.g. Bench Press 100kg">
        </div>
        <div class="form-group">
            <label class="form-label">Description</label>
            <input type="text" class="form-input" id="goalDesc" placeholder="What does achieving this mean to you?">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Category</label>
                <select class="form-select" id="goalCategory">
                    <option value="💪 Strength">💪 Strength</option>
                    <option value="🔥 Weight Loss">🔥 Weight Loss</option>
                    <option value="🏃 Cardio">🏃 Cardio</option>
                    <option value="🧘 Flexibility">🧘 Flexibility</option>
                    <option value="⚡ HIIT">⚡ HIIT</option>
                    <option value="🎯 Custom">🎯 Custom</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Deadline</label>
                <input type="date" class="form-input" id="goalDeadline">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Current Value</label>
                <input type="number" class="form-input" id="goalCurrent" placeholder="e.g. 70">
            </div>
            <div class="form-group">
                <label class="form-label">Target Value</label>
                <input type="number" class="form-input" id="goalTarget" placeholder="e.g. 100">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeGoalModal()">Cancel</button>
            <button class="btn-save" onclick="saveGoal()">
                <span class="material-icons" style="font-size:16px;vertical-align:middle">save</span> Save Goal
            </button>
        </div>
    </div>
</div>

<script src="/workout_trackersys/scripts/progressGoals.js"></script>
<script src="/workout_trackersys/scripts/redirect.js"></script>
</body>
</html>