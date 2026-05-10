
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymTracker — Goals & Progress</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/workout_trackersys/assets/shared.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>

        /* ── Stats Row ── */
        .stats-row { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:28px; }

        /* ── Section Divider ── */
        .section-divider {
            display:flex; align-items:center; gap:14px;
            margin:36px 0 22px;
            animation:fadeUp 0.4s ease;
        }
        .section-divider h3 { font-size:18px; font-weight:700; color:var(--text); white-space:nowrap; }
        .section-divider::after { content:''; flex:1; height:1px; background:var(--border); }

        /* ── Goals Grid ── */
        .goals-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:18px; margin-bottom:12px; animation:fadeUp 0.45s ease; }

        .goal-card {
            background:var(--surface); border:1px solid var(--border); border-radius:var(--radius);
            padding:22px 24px; box-shadow:var(--shadow); transition:all 0.3s ease;
            position:relative; overflow:hidden;
        }
        .goal-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--accent-grad); }
        .goal-card:hover { transform:translateY(-3px); box-shadow:var(--shadow-hover); }

        .goal-card-top { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:14px; }
        .goal-emoji { font-size:28px; }
        .goal-status-badge { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; padding:4px 10px; border-radius:99px; display:inline-flex; align-items:center; gap:4px; }
        .goal-status-badge::before { content:''; width:5px; height:5px; border-radius:50%; background:currentColor; }
        .status-in-progress { background:rgba(56,189,248,0.12);  color:#38BDF8; }
        .status-completed   { background:rgba(34,197,94,0.12);   color:#22C55E; }
        .status-not-started { background:rgba(245,158,11,0.12);  color:#F59E0B; }

        .goal-name { font-size:16px; font-weight:700; color:var(--text); margin-bottom:4px; }
        .goal-desc { font-size:13px; color:var(--text-muted); line-height:1.6; margin-bottom:14px; }

        .goal-progress-meta { display:flex; justify-content:space-between; margin-bottom:7px; }
        .goal-progress-label { font-size:12px; font-weight:500; color:var(--text-muted); }
        .goal-progress-val   { font-size:12px; font-weight:700; color:var(--accent); }

        .goal-bar-bg { width:100%; height:8px; background:var(--surface-2); border-radius:99px; overflow:hidden; border:1px solid var(--border); margin-bottom:10px; }
        .goal-bar-fill { height:100%; border-radius:99px; transition:width 1s cubic-bezier(.4,0,.2,1); }

        .goal-values { display:flex; justify-content:space-between; font-size:12px; color:var(--text-muted); margin-bottom:10px; }
        .goal-values span:last-child { font-weight:600; color:var(--text); }

        .goal-deadline { font-size:11px; color:var(--text-muted); display:flex; align-items:center; gap:4px; margin-bottom:14px; }
        .goal-deadline .material-icons { font-size:14px; color:var(--accent); }

        .goal-actions { display:flex; gap:8px; padding-top:12px; border-top:1px solid var(--border); }
        .goal-action-btn { flex:1; display:flex; align-items:center; justify-content:center; gap:5px; padding:8px; border-radius:var(--radius-sm); border:1px solid var(--border); background:var(--surface-2); color:var(--text-muted); font-family:'Poppins',sans-serif; font-size:12px; font-weight:600; cursor:pointer; transition:all 0.25s ease; }
        .goal-action-btn:hover { border-color:var(--accent); color:var(--accent); background:var(--accent-light); }
        .goal-action-btn.del:hover { border-color:var(--danger); color:var(--danger); background:rgba(239,68,68,0.08); }
        .goal-action-btn .material-icons { font-size:15px; }

        /* ── Add Goal Button ── */
        .add-goal-row { display:flex; justify-content:flex-end; margin-bottom:20px; }

        /* ── Progress Section ── */
        .progress-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px; animation:fadeUp 0.5s ease; }
        .chart-wrap { position:relative; height:230px; }
        .chart-tabs { display:flex; gap:6px; }
        .chart-tab { font-size:12px; font-weight:600; padding:5px 12px; border-radius:99px; border:1px solid var(--border); background:transparent; color:var(--text-muted); cursor:pointer; transition:var(--transition); font-family:'Poppins',sans-serif; }
        .chart-tab.active { background:var(--accent); color:white; border-color:var(--accent); }

        /* ── Personal Bests ── */
        .pb-list { display:flex; flex-direction:column; gap:10px; }
        .pb-item { display:flex; align-items:center; gap:14px; padding:12px 14px; background:var(--surface-2); border:1px solid var(--border); border-radius:var(--radius-sm); transition:var(--transition); cursor:pointer; }
        .pb-item:hover { border-color:var(--accent); background:var(--accent-light); }
        .pb-icon { font-size:20px; flex-shrink:0; }
        .pb-info { flex:1; }
        .pb-name { font-size:14px; font-weight:600; color:var(--text); }
        .pb-date { font-size:11px; color:var(--text-muted); margin-top:2px; }
        .pb-val  { font-size:18px; font-weight:700; color:var(--accent); flex-shrink:0; }
        .pb-val.new { color:#22C55E; }

        /* ── Volume chart (full width) ── */
        .full-card { animation:fadeUp 0.55s ease; margin-bottom:20px; }

        /* ── Heatmap ── */
        .heatmap-wrap { animation:fadeUp 0.6s ease; }
        .heatmap-grid { display:grid; grid-template-columns:repeat(13,1fr); gap:4px; margin-top:8px; }
        .heatmap-cell { aspect-ratio:1; border-radius:3px; background:var(--surface-2); border:1px solid var(--border); transition:var(--transition); position:relative; cursor:default; }
        .heatmap-cell.level-1 { background:rgba(56,189,248,0.20); border-color:rgba(56,189,248,0.30); }
        .heatmap-cell.level-2 { background:rgba(56,189,248,0.45); border-color:rgba(56,189,248,0.55); }
        .heatmap-cell.level-3 { background:rgba(59,130,246,0.70); border-color:rgba(59,130,246,0.80); }
        .heatmap-cell.level-4 { background:#3B82F6; border-color:#38BDF8; }
        .heatmap-cell:hover::after { content:attr(data-tip); position:absolute; bottom:120%; left:50%; transform:translateX(-50%); background:var(--text); color:var(--bg); font-size:10px; font-family:'Poppins',sans-serif; padding:3px 7px; border-radius:4px; white-space:nowrap; z-index:10; }
        .heatmap-legend { display:flex; align-items:center; gap:6px; margin-top:10px; font-size:11px; color:var(--text-muted); }
        .heatmap-legend-cells { display:flex; gap:4px; }
        .legend-cell { width:12px; height:12px; border-radius:2px; }

        /* ── Modal ── */
        .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); backdrop-filter:blur(6px); z-index:9999; justify-content:center; align-items:center; padding:16px; }
        .modal-overlay.visible { display:flex; }
        .modal-box { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:32px; width:100%; max-width:520px; box-shadow:0 24px 60px rgba(0,0,0,0.3); position:relative; animation:modalFade 0.3s ease; max-height:90vh; overflow-y:auto; }
        @keyframes modalFade{from{opacity:0;transform:translateY(16px) scale(0.97)}to{opacity:1;transform:translateY(0) scale(1)}}
        .modal-close { position:absolute; top:16px; right:16px; width:32px; height:32px; border-radius:8px; border:1px solid var(--border); background:var(--surface-2); color:var(--text-muted); cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:18px; transition:var(--transition); }
        .modal-close:hover { background:rgba(239,68,68,0.10); color:var(--danger); border-color:var(--danger); }
        .modal-title { font-size:20px; font-weight:700; color:var(--text); margin-bottom:5px; padding-right:40px; }
        .modal-subtitle { font-size:13px; color:var(--text-muted); margin-bottom:22px; }
        .form-group { display:flex; flex-direction:column; gap:6px; margin-bottom:16px; }
        .form-label { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:var(--text-muted); }
        .form-input,.form-select { padding:11px 14px; border:1px solid var(--border); border-radius:var(--radius-sm); background:var(--surface-2); color:var(--text); font-family:'Poppins',sans-serif; font-size:14px; outline:none; transition:var(--transition); width:100%; }
        .form-input:focus,.form-select:focus { border-color:var(--accent); box-shadow:0 0 0 3px var(--accent-light); background:var(--surface); }
        .form-input::placeholder { color:var(--text-muted); }
        .form-select { appearance:none; cursor:pointer; background-image:url("data:image/svg+xml,%3Csvg fill='%235B7BAF' height='20' viewBox='0 0 24 24' width='20' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 12px center; background-size:16px; padding-right:36px; }
        .form-select option { background:#090979; color:white; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
        .modal-footer { display:flex; justify-content:flex-end; gap:10px; margin-top:10px; padding-top:16px; border-top:1px solid var(--border); }
        .btn-cancel { padding:11px 22px; border-radius:25px; border:1px solid var(--border); background:var(--surface-2); color:var(--text-muted); font-family:'Poppins',sans-serif; font-size:14px; font-weight:600; cursor:pointer; transition:var(--transition); }
        .btn-cancel:hover { background:var(--border); color:var(--text); }
        .btn-save { padding:11px 26px; border-radius:25px; border:none; background:linear-gradient(45deg,#38BDF8,#3B82F6); color:white; font-family:'Poppins',sans-serif; font-size:14px; font-weight:600; cursor:pointer; transition:all 0.3s ease; box-shadow:0 6px 20px rgba(56,189,248,0.35); }
        .btn-save:hover { transform:translateY(-2px); box-shadow:0 12px 28px rgba(56,189,248,0.5); }

        /* ── Empty state ── */
        .empty-goals { text-align:center; padding:48px 20px; color:var(--text-muted); font-size:14px; background:var(--surface-2); border:2px dashed var(--border); border-radius:var(--radius); }
        .empty-goals .material-icons { font-size:42px; color:var(--border); margin-bottom:12px; }

        /* ── GymSwal ── */
        .swal-on-top { z-index:99999 !important; }

        @media(max-width:1100px) { .progress-grid { grid-template-columns:1fr; } .stats-row { grid-template-columns:repeat(2,1fr); } }
        @media(max-width:768px)  { .goals-grid { grid-template-columns:1fr; } .form-row { grid-template-columns:1fr; } }
        @media(max-width:480px)  { .stats-row { grid-template-columns:1fr 1fr; } .heatmap-grid { grid-template-columns:repeat(7,1fr); } }
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
            <li class="active"><i class="material-icons icon">flag</i><span class="text">Goals</span></li>
        </ul>
    </div>
    <div class="Planner"><ul>
        <li onclick="redirectUser(6)"><i class="material-icons icon">event_note</i><span class="text">Recommendations</span></li>
        <li onclick="redirectUser(9)"><i class="material-icons icon">calendar_today</i><span class="text">Calendar</span></li>
    </ul></div>
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
            <label class="toggle"><input type="checkbox" id="themeToggle"><div class="toggle-track"></div><div class="toggle-thumb">☀️</div></label>
        </div>
    </div>

    <!-- STATS -->
    <div class="stats-row">
        <div class="stat-card" style="animation-delay:0.05s">
            <div class="stat-top"><div class="stat-icon">🎯</div><span class="stat-change up" id="statActiveChange">↑ 2</span></div>
            <div class="stat-val" id="statActive">—</div>
            <div class="stat-label">Active Goals</div>
        </div>
        <div class="stat-card" style="animation-delay:0.10s">
            <div class="stat-top"><div class="stat-icon">✅</div><span class="stat-change up">+3 this month</span></div>
            <div class="stat-val" id="statCompleted">—</div>
            <div class="stat-label">Goals Completed</div>
        </div>
        <div class="stat-card" style="animation-delay:0.15s">
            <div class="stat-top"><div class="stat-icon">🔥</div><span class="stat-change up" id="statWorkoutsChange">↑ 12%</span></div>
            <div class="stat-val" id="statWorkouts">—</div>
            <div class="stat-label">Workouts This Month</div>
        </div>
        <div class="stat-card" style="animation-delay:0.20s">
            <div class="stat-top"><div class="stat-icon">📈</div><span class="stat-change neutral">steady</span></div>
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

        <!-- Personal Bests -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">Personal Bests 🏆</div>
                <span class="card-badge">All time</span>
            </div>
            <div class="pb-list" id="pbList">
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

<script>
/* ── Theme ── */
const themeToggle = document.getElementById('themeToggle');
const themeLabel  = document.getElementById('theme-label');
const thumbEl     = document.querySelector('.toggle-thumb');
let freqChart = null, volumeChart = null;

window.addEventListener('DOMContentLoaded', () => {
    applyTheme(localStorage.getItem('theme') || 'light');
    loadAll();
    buildHeatmap();
});

themeToggle.addEventListener('change', () => {
    const t = themeToggle.checked ? 'dark' : 'light';
    applyTheme(t); localStorage.setItem('theme', t);
    if (freqChart)   updateChartColors(freqChart);
    if (volumeChart) updateChartColors(volumeChart);
});

function applyTheme(t) {
    document.documentElement.setAttribute('data-theme', t);
    themeToggle.checked    = t === 'dark';
    themeLabel.textContent = t === 'dark' ? 'Dark' : 'Light';
    thumbEl.textContent    = t === 'dark' ? '🌙' : '☀️';
}

function ToggleSidebar() { document.getElementById('sidebar').classList.toggle('active'); }

/* ============================================================
   LOAD ALL DATA
============================================================ */
let goals = [], weeklyFreq = [], monthlyFreq = [];

function loadAll() {
    // JS-only sample data (no backend)
    goals = [
        { id:1, name:'Bench Press 100kg', desc:'Hit a 100kg bench press by end of May',  category:'💪 Strength',   current:80,  target:100, deadline:'2026-05-30', status:'in-progress', color:'linear-gradient(45deg,#38BDF8,#3B82F6)' },
        { id:2, name:'Lose 5kg',           desc:'Drop from 85kg to 80kg',                  category:'🔥 Weight Loss', current:3,   target:5,   deadline:'2026-06-15', status:'in-progress', color:'linear-gradient(45deg,#F59E0B,#EF4444)' },
        { id:3, name:'Run 5km < 25min',    desc:'Improve 5km time from 28min',             category:'🏃 Cardio',     current:100, target:100, deadline:'2026-04-20', status:'completed',   color:'linear-gradient(45deg,#22C55E,#38BDF8)' },
        { id:4, name:'20 Pull-ups',        desc:'Do 20 consecutive pull-ups',              category:'💪 Strength',   current:15,  target:20,  deadline:'2026-07-01', status:'in-progress', color:'linear-gradient(45deg,#6366F1,#8B5CF6)' },
        { id:5, name:'Deadlift 200kg',     desc:'Progress deadlift from 180kg to 200kg',  category:'💪 Strength',   current:180, target:200, deadline:'2026-08-01', status:'not-started', color:'linear-gradient(45deg,#F97316,#EF4444)' },
    ];

    renderStats({ active:4, completed:1, workouts:32, avgProgress:68 });
    renderGoals();

    renderPBs([
        { icon:'🏋️‍♀️', name:'Bench Press', val:'100kg', date:'Apr 10, 2026', isNew:true },
        { icon:'🤎',    name:'Squat',        val:'140kg', date:'Apr 18, 2026', isNew:false },
        { icon:'💪',    name:'Pull-Ups',     val:'18 reps', date:'Apr 20, 2026', isNew:true },
        { icon:'🏋️‍♂️', name:'Deadlift',     val:'180kg', date:'Mar 28, 2026', isNew:false },
        { icon:'🏃‍♂️', name:'5km Run',      val:'24:30',  date:'Apr 5, 2026',  isNew:false },
    ]);

    weeklyFreq  = [1,2,0,2,1,3,2];
    monthlyFreq = [10,14,12,18,16,20,17,22,19,25,23,28];

    buildFreqChart(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'], weeklyFreq);
    buildVolumeChart([2400,2800,3100,2700,3400,3200,3800,4100,3900,4500,4300,4800]);
}


/* ── Stats ── */
function renderStats(s) {
    document.getElementById('statActive').textContent = s.active ?? '—';
    document.getElementById('statCompleted').textContent = s.completed ?? '—';
    document.getElementById('statWorkouts').textContent = s.workouts ?? '—';
    document.getElementById('statAvgProgress').textContent = (s.avgProgress || s.avgProgress === 0) ? (s.avgProgress + '%') : '—';

    // JS-only trend badges for the stat cards (no backend)
    const trends = s.trends || {
        active: { label: '↑ 2', cls: 'up' },
        completed: { label: ' +3 this month', cls: 'up' },
        workouts: { label: '↑ 12%', cls: 'up' },
        avgProgress: { label: 'steady', cls: 'neutral' }
    };

    const setBadgeById = (badgeId, { label, cls }) => {
        const el = document.getElementById(badgeId);
        if (!el) return;
        el.textContent = label;
        el.classList.remove('up', 'neutral', 'down');
        el.classList.add(cls);
    };

    // Active goals badge uses an id
    setBadgeById('statActiveChange', trends.active);

    // Completed goals badge is the first .stat-change inside the stat card for statCompleted
    const completedCard = document.getElementById('statCompleted')?.closest('.stat-card');
    if (completedCard) {
        const badge = completedCard.querySelector('.stat-change');
        if (badge) {
            badge.textContent = trends.completed.label.trim();
            badge.classList.remove('up', 'neutral', 'down');
            badge.classList.add(trends.completed.cls);
        }
    }

    // Workouts badge uses an id
    setBadgeById('statWorkoutsChange', trends.workouts);

    // Avg progress badge is the first .stat-change inside the stat card for statAvgProgress
    const avgCard = document.getElementById('statAvgProgress')?.closest('.stat-card');
    if (avgCard) {
        const badge = avgCard.querySelector('.stat-change');
        if (badge) {
            badge.textContent = trends.avgProgress.label;
            badge.classList.remove('up', 'neutral', 'down');
            badge.classList.add(trends.avgProgress.cls);
        }
    }
}



/* ============================================================
   GOALS
============================================================ */
let nextGoalId = 100;

function getPercent(g) {
    if (g.status === 'completed') return 100;
    if (!g.target || g.target === 0) return 0;
    return Math.min(100, Math.round((g.current / g.target) * 100));
}

function renderGoals() {
    const grid = document.getElementById('goalsGrid');

    // Update stat cards
    document.getElementById('statActive').textContent    = goals.filter(g=>g.status!=='completed').length;
    document.getElementById('statCompleted').textContent = goals.filter(g=>g.status==='completed').length;
    const total = goals.length;
    const avgPct = total ? Math.round(goals.reduce((s,g)=>s+getPercent(g),0)/total) : 0;
    document.getElementById('statAvgProgress').textContent = avgPct + '%';

    if (!goals.length) {
        grid.innerHTML = `
            <div class="empty-goals" style="grid-column:1/-1">
                <div class="material-icons">flag</div>
                <div>No goals yet.</div>
                <div style="margin-top:6px;font-size:13px">Click <strong>Add New Goal</strong> to set your first target!</div>
            </div>`;
        return;
    }

    grid.innerHTML = '';
    goals.forEach((g, i) => {
        const pct = getPercent(g);
        const card = document.createElement('div');
        card.className = 'goal-card';
        card.style.animationDelay = (i * 0.06) + 's';
        card.style.animation = 'fadeUp 0.45s ease both';
        const deadlineStr = g.deadline ? new Date(g.deadline).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'}) : '—';
        card.innerHTML = `
            <div class="goal-card-top">
                <div class="goal-emoji">${g.category ? g.category.split(' ')[0] : '🎯'}</div>
                <span class="goal-status-badge status-${g.status}">${g.status.replace('-',' ')}</span>
            </div>
            <div class="goal-name">${g.name}</div>
            <div class="goal-desc">${g.desc || ''}</div>
            <div class="goal-progress-meta">
                <span class="goal-progress-label">${g.category || 'Goal'}</span>
                <span class="goal-progress-val">${pct}%</span>
            </div>
            <div class="goal-bar-bg">
                <div class="goal-bar-fill" style="width:0%;background:${g.color||'var(--accent-grad)'}" data-target="${pct}%"></div>
            </div>
            <div class="goal-values">
                <span>Current: <strong style="color:var(--text)">${g.current}</strong></span>
                <span>Target: ${g.target}</span>
            </div>
            <div class="goal-deadline"><span class="material-icons">event</span> Deadline: ${deadlineStr}</div>
            <div class="goal-actions">
                <button class="goal-action-btn" onclick="openUpdateGoal(${g.id})"><span class="material-icons">edit</span> Update</button>
                <button class="goal-action-btn" onclick="markComplete(${g.id})"><span class="material-icons">check_circle</span> Complete</button>
                <button class="goal-action-btn del" onclick="deleteGoal(${g.id})"><span class="material-icons">delete</span></button>
            </div>`;
        grid.appendChild(card);
    });

    // Animate bars
    setTimeout(() => document.querySelectorAll('.goal-bar-fill').forEach(b => b.style.width = b.dataset.target), 200);
}

/* ── Mark Complete ── */
function markComplete(id) {
    const g = goals.find(x => x.id === id); if(!g) return;
    if (g.status === 'completed') { GymToast.fire({ icon:'info', title:'Already completed!' }); return; }
    GymSwal.fire({ title:`Complete "${g.name}"?`, text:'Mark this goal as achieved! 🎉', icon:'question', showCancelButton:true, confirmButtonText:'Yes, complete!', cancelButtonText:'Cancel' })
    .then(r => {
        if (!r.isConfirmed) return;
        g.status  = 'completed';
        g.current = g.target;
        saveGoalToBackend(g, false);
        renderGoals();
        GymSwal.fire({ icon:'success', title:'Goal completed! 🎉', text:`"${g.name}" — you crushed it!`, timer:2500, showConfirmButton:false });
    });
}

/* ── Delete Goal ── */
function deleteGoal(id) {
    const g = goals.find(x => x.id === id); if(!g) return;
    GymSwal.fire({ title:`Delete "${g.name}"?`, text:'This cannot be undone.', icon:'warning', showCancelButton:true, confirmButtonText:'Yes, delete', cancelButtonText:'Cancel' })
    .then(r => {
        if (!r.isConfirmed) return;
        goals = goals.filter(x => x.id !== id);
        // JS-only mode: no backend calls yet
        renderGoals();
        GymToast.fire({ icon:'success', title:'Goal deleted.' });
    });
}


/* ── Modal ── */
let isEditing = false, editingId = null;

function openAddGoal() {
    isEditing = false; editingId = null;
    document.getElementById('modalTitle').textContent = 'Add New Goal';
    document.getElementById('goalEditId').value   = '';
    document.getElementById('goalName').value     = '';
    document.getElementById('goalDesc').value     = '';
    document.getElementById('goalCategory').value = '💪 Strength';
    document.getElementById('goalDeadline').value = '';
    document.getElementById('goalCurrent').value  = '';
    document.getElementById('goalTarget').value   = '';
    document.getElementById('goalModal').classList.add('visible');
}

function openUpdateGoal(id) {
    const g = goals.find(x => x.id === id); if(!g) return;
    isEditing = true; editingId = id;
    document.getElementById('modalTitle').textContent    = 'Update Goal';
    document.getElementById('goalEditId').value          = id;
    document.getElementById('goalName').value            = g.name;
    document.getElementById('goalDesc').value            = g.desc || '';
    document.getElementById('goalCategory').value        = g.category || '💪 Strength';
    document.getElementById('goalDeadline').value        = g.deadline || '';
    document.getElementById('goalCurrent').value         = g.current;
    document.getElementById('goalTarget').value          = g.target;
    document.getElementById('goalModal').classList.add('visible');
}

function closeGoalModal() { document.getElementById('goalModal').classList.remove('visible'); }
function closeModalOutside(e) { if(e.target === document.getElementById('goalModal')) closeGoalModal(); }

function saveGoal() {
    const name     = document.getElementById('goalName').value.trim();
    const desc     = document.getElementById('goalDesc').value.trim();
    const category = document.getElementById('goalCategory').value;
    const deadline = document.getElementById('goalDeadline').value;
    const current  = parseFloat(document.getElementById('goalCurrent').value) || 0;
    const target   = parseFloat(document.getElementById('goalTarget').value)  || 100;

    if (!name)     { GymSwal.fire({ icon:'warning', title:'Please enter a goal name.' }); return; }
    if (!deadline) { GymSwal.fire({ icon:'warning', title:'Please set a deadline.' });    return; }

    const colors = ['linear-gradient(45deg,#38BDF8,#3B82F6)','linear-gradient(45deg,#F59E0B,#EF4444)','linear-gradient(45deg,#22C55E,#38BDF8)','linear-gradient(45deg,#6366F1,#8B5CF6)','linear-gradient(45deg,#F97316,#EF4444)'];

    if (isEditing) {
        const g = goals.find(x => x.id === editingId);
        if (g) { Object.assign(g, { name, desc, category, current, target, deadline, status: current >= target ? 'completed' : 'in-progress' }); }
    } else {
        goals.push({ id: nextGoalId++, name, desc, category, current, target, deadline, status: current >= target ? 'completed' : 'in-progress', color: colors[goals.length % colors.length] });
    }

    saveGoalToBackend({ name, desc, category, current, target, deadline }, isEditing);
    closeGoalModal();
    renderGoals();
    GymToast.fire({ icon:'success', title: isEditing ? 'Goal updated!' : 'Goal added! 🎯' });
}

/* ── Save goal to backend ── */
function saveGoalToBackend(g, isEdit) {
    // JS-only mode: backend not implemented yet. Keep UI functional locally.
}


/* ============================================================
   PERSONAL BESTS
============================================================ */
function renderPBs(pbs) {
    const list = document.getElementById('pbList');
    if (!pbs.length) { list.innerHTML = '<div style="text-align:center;padding:20px;color:var(--text-muted);font-size:13px">No personal bests logged yet.</div>'; return; }
    list.innerHTML = pbs.map(pb => `
        <div class="pb-item">
            <div class="pb-icon">${pb.icon}</div>
            <div class="pb-info"><div class="pb-name">${pb.name}</div><div class="pb-date">${pb.date}</div></div>
            <div class="pb-val${pb.isNew?' new':''}">${pb.val} ${pb.isNew?'🆕':''}</div>
        </div>`).join('');
}

/* ============================================================
   CHARTS
============================================================ */
function getChartColors() {
    const dark = document.documentElement.getAttribute('data-theme') === 'dark';
    return { gc: dark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.05)', tc: dark ? '#94A3B8' : '#5B7BAF' };
}

function buildFreqChart(labels, data) {
    const ctx = document.getElementById('freqChart').getContext('2d');
    if (freqChart) freqChart.destroy();
    const { gc, tc } = getChartColors();
    freqChart = new Chart(ctx, {
        type: 'bar',
        data: { labels, datasets: [{ label:'Workouts', data, backgroundColor:'rgba(59,130,246,0.18)', borderColor:'#3B82F6', borderWidth:2, borderRadius:8, borderSkipped:false }] },
        options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:false } }, scales:{ x:{ grid:{ color:gc }, ticks:{ color:tc, font:{ family:'Poppins', size:11 } } }, y:{ grid:{ color:gc }, ticks:{ color:tc, font:{ family:'Poppins', size:11 }, stepSize:1 }, beginAtZero:true } } }
    });
}

function buildVolumeChart(data) {
    const ctx = document.getElementById('volumeChart').getContext('2d');
    if (volumeChart) volumeChart.destroy();
    const { gc, tc } = getChartColors();
    const labels = ['W1','W2','W3','W4','W5','W6','W7','W8','W9','W10','W11','W12'];
    volumeChart = new Chart(ctx, {
        type: 'line',
        data: { labels, datasets: [{ label:'Volume (kg)', data, borderColor:'#38BDF8', backgroundColor:'rgba(56,189,248,0.08)', tension:0.4, fill:true, pointBackgroundColor:'#38BDF8', pointRadius:4, borderWidth:2.5 }] },
        options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:false } }, scales:{ x:{ grid:{ color:gc }, ticks:{ color:tc, font:{ family:'Poppins', size:11 } } }, y:{ grid:{ color:gc }, ticks:{ color:tc, font:{ family:'Poppins', size:11 } }, beginAtZero:false } } }
    });
}

function switchFreqChart(type, btn) {
    document.querySelectorAll('.chart-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    const labels = type === 'weekly' ? ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] : ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    buildFreqChart(labels, type === 'weekly' ? weeklyFreq : monthlyFreq);
}

function updateChartColors(chart) {
    const { gc, tc } = getChartColors();
    ['x','y'].forEach(ax => { chart.options.scales[ax].grid.color = gc; chart.options.scales[ax].ticks.color = tc; });
    chart.update();
}

/* ============================================================
   HEATMAP
============================================================ */
function buildHeatmap() {
    // JS-only sample data (no backend)
    const levels = [0,0,1,0,2,0,1,3,0,2,1,0,4,2,0,1,3,0,2,1,0,1,0,2,3,1,0,0,2,1,4,0,1,2,0,3,1,0,2,0,1,3,2,0,1,0,2,1,3,0,4,1,2,0,1,3,0,2,1,0,4,1,2,3,0,1,2,0,3,1,4,0,2,1,3,0,1,2,4,0,1,3,2,1,0,2,3,1,0,4,2];
    renderHeatmap(levels);
}


function renderHeatmap(levels) {
    const grid = document.getElementById('heatmapGrid');
    grid.innerHTML = '';
    for (let i = 0; i < 91; i++) {
        const cell = document.createElement('div');
        const lvl  = levels[i % levels.length] || 0;
        cell.className = `heatmap-cell${lvl > 0 ? ' level-' + Math.min(lvl,4) : ''}`;
        cell.setAttribute('data-tip', lvl === 0 ? 'No workout' : `${lvl} workout${lvl > 1 ? 's' : ''}`);
        grid.appendChild(cell);
    }
}

/* ============================================================
   GYMSWAL & GYMTOAST
============================================================ */
const GymSwal = Swal.mixin({
    customClass:{ container:'swal-on-top' }, backdrop:'rgba(0,0,0,0.5)',
    didOpen:(p)=>{
        p.style.background='rgba(9,9,121,0.95)'; p.style.backdropFilter='blur(20px)';
        p.style.border='1px solid rgba(255,255,255,0.2)'; p.style.borderRadius='20px';
        p.style.color='#fff'; p.style.fontFamily='Poppins,sans-serif';
        const t=p.querySelector('.swal2-title'); if(t)t.style.color='#fff';
        const tx=p.querySelector('.swal2-html-container'); if(tx)tx.style.color='rgba(255,255,255,0.8)';
        const c=p.querySelector('.swal2-confirm'); if(c){c.style.background='linear-gradient(45deg,#38BDF8,#3B82F6)';c.style.border='none';c.style.borderRadius='50px';c.style.fontFamily='Poppins,sans-serif';c.style.fontWeight='600';}
        const x=p.querySelector('.swal2-cancel'); if(x){x.style.background='rgba(255,255,255,0.15)';x.style.color='white';x.style.border='1px solid rgba(255,255,255,0.2)';x.style.borderRadius='50px';x.style.fontFamily='Poppins,sans-serif';x.style.fontWeight='600';}
    }
});

const GymToast = Swal.mixin({
    toast:true, position:'top-end', showConfirmButton:false, timer:3000, timerProgressBar:true,
    customClass:{ container:'swal-on-top' },
    didOpen:(t)=>{ t.style.background='rgba(9,9,121,0.95)'; t.style.backdropFilter='blur(20px)'; t.style.border='1px solid rgba(255,255,255,0.2)'; t.style.borderRadius='12px'; t.style.color='#fff'; t.addEventListener('mouseenter',Swal.stopTimer); t.addEventListener('mouseleave',Swal.resumeTimer); }
});

function LogoutFunc() {
    GymSwal.fire({ title:'Leaving so soon?', icon:'question', showCancelButton:true, confirmButtonText:'Yes, log out', cancelButtonText:'Stay' })
    .then(r => { if(r.isConfirmed) $.ajax({ url:'/workout_trackersys/controllers/logout.php', type:'POST', success:()=>window.location.href='?page=login' }); });
}
</script>
<script src="/workout_trackersys/scripts/redirect.js"></script>
</body>
</html>