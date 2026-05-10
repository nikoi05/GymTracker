<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymTracker Admin — Workouts</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* ── Variables ── */
        :root {
            --bg:#F0F6FF; --surface:#FFFFFF; --surface-2:#E8F0FE; --border:#DBEAFE;
            --text:#020024; --text-muted:#5B7BAF; --accent:#3B82F6; --accent-sky:#38BDF8;
            --accent-light:rgba(59,130,246,0.10); --accent-grad:linear-gradient(45deg,#38BDF8,#3B82F6);
            --shadow:0 4px 24px rgba(9,9,121,0.10); --shadow-hover:0 8px 32px rgba(59,130,246,0.22);
            --danger:#EF4444; --warning:#F59E0B; --success:#22C55E;
            --radius:16px; --radius-sm:10px; --transition:0.25s ease;
        }
        [data-theme="dark"] {
            --bg:#020024; --surface:rgba(255,255,255,0.07); --surface-2:rgba(255,255,255,0.04);
            --border:rgba(255,255,255,0.12); --text:#F1F5F9; --text-muted:rgba(255,255,255,0.55);
            --accent:#38BDF8; --accent-light:rgba(56,189,248,0.12);
            --shadow:0 4px 24px rgba(0,0,0,0.5); --shadow-hover:0 8px 32px rgba(0,0,0,0.6);
        }
        *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
        body { font-family:'Poppins',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; transition:background var(--transition),color var(--transition); }
        [data-theme="dark"] body { background:linear-gradient(-45deg,#020024,#090979,#0f3460); background-size:400% 400%; animation:gradientShift 15s ease infinite; }
        @keyframes gradientShift{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        @keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
        @keyframes modalFade{from{opacity:0;transform:translateY(16px) scale(0.97)}to{opacity:1;transform:translateY(0) scale(1)}}

        /* ── Layout ── */
        .wrapper { display:flex; min-height:100vh; }

        /* ── Sidebar (same as exercises page) ── */
        .sidebar { width:230px; min-height:100vh; background:linear-gradient(180deg,#090979,#020024); border-right:1px solid rgba(255,255,255,0.12); padding:28px 16px; display:flex; flex-direction:column; gap:4px; flex-shrink:0; position:relative; }
        .sidebar::before { content:''; position:absolute; inset:0; background:rgba(255,255,255,0.03); pointer-events:none; }
        .sidebar-brand { font-size:20px; font-weight:700; color:white; margin-bottom:8px; padding-left:8px; }
        .sidebar-brand span { background:linear-gradient(45deg,#38BDF8,#3B82F6); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
        .admin-badge { font-size:10px; font-weight:600; letter-spacing:2px; text-transform:uppercase; color:rgba(255,255,255,0.4); padding-left:8px; margin-bottom:24px; }
        .sidebar-section { font-size:10px; font-weight:600; letter-spacing:2px; text-transform:uppercase; color:rgba(255,255,255,0.3); padding:12px 12px 6px; }
        .sidebar-link { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:25px; color:rgba(255,255,255,0.7); text-decoration:none; font-size:14px; font-weight:500; transition:all 0.3s ease; cursor:pointer; border:1px solid transparent; }
        .sidebar-link:hover { color:white; background:rgba(255,255,255,0.12); transform:translateX(3px); }
        .sidebar-link.active { color:white; background:rgba(56,189,248,0.18); border-color:rgba(56,189,248,0.35); font-weight:600; }
        .sidebar-link .material-icons { font-size:20px; }
        .sidebar-divider { height:1px; background:rgba(255,255,255,0.08); margin:8px 0; }
        .sidebar-logout { margin-top:auto; display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:25px; color:rgba(255,255,255,0.6); font-size:14px; font-weight:500; cursor:pointer; transition:all 0.3s ease; background:none; border:none; font-family:'Poppins',sans-serif; width:100%; }
        .sidebar-logout:hover { background:rgba(239,68,68,0.18); color:#FCA5A5; transform:translateX(3px); }

        /* ── Main ── */
        .main { flex:1; padding:32px 36px; overflow-y:auto; min-width:0; }

        /* ── Topbar ── */
        .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; animation:fadeUp 0.4s ease; }
        .topbar-left h1 { font-size:clamp(22px,3vw,30px); font-weight:700; color:var(--text); }
        .topbar-left p { font-size:14px; color:var(--text-muted); margin-top:4px; }
        .topbar-right { display:flex; align-items:center; gap:12px; }
        .toggle-wrap { display:flex; align-items:center; gap:8px; }
        .toggle-label { font-size:13px; color:var(--text-muted); font-weight:500; }
        .toggle { position:relative; width:52px; height:28px; cursor:pointer; }
        .toggle input { display:none; }
        .toggle-track { position:absolute; inset:0; background:var(--border); border-radius:99px; transition:var(--transition); }
        .toggle input:checked + .toggle-track { background:var(--accent); }
        .toggle-thumb { position:absolute; top:4px; left:4px; width:20px; height:20px; background:white; border-radius:50%; transition:var(--transition); box-shadow:0 2px 6px rgba(0,0,0,0.15); display:flex; align-items:center; justify-content:center; font-size:11px; }
        .toggle input:checked ~ .toggle-thumb { left:28px; }

        /* ── Stats Strip ── */
        .stats-strip { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:22px; animation:fadeUp 0.45s ease; }
        .strip-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:18px 20px; display:flex; align-items:center; gap:14px; box-shadow:var(--shadow); transition:all 0.3s ease; position:relative; overflow:hidden; }
        .strip-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--accent-grad); }
        .strip-card:hover { transform:translateY(-3px); box-shadow:var(--shadow-hover); }
        .strip-icon { width:42px; height:42px; border-radius:12px; background:var(--accent-light); display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; }
        .strip-val { font-size:22px; font-weight:700; color:var(--text); line-height:1; }
        .strip-label { font-size:11px; color:var(--text-muted); font-weight:500; text-transform:uppercase; letter-spacing:0.5px; margin-top:3px; }

        /* ── Toolbar ── */
        .toolbar { display:flex; gap:12px; align-items:center; margin-bottom:18px; flex-wrap:wrap; animation:fadeUp 0.5s ease; }
        .search-wrap { flex:1; min-width:220px; position:relative; }
        .search-wrap .material-icons { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:20px; }
        .search-input { width:100%; padding:11px 16px 11px 44px; border-radius:25px; border:1px solid var(--border); background:var(--surface); color:var(--text); font-family:'Poppins',sans-serif; font-size:14px; outline:none; transition:var(--transition); box-shadow:var(--shadow); }
        .search-input:focus { border-color:var(--accent); box-shadow:0 0 0 3px var(--accent-light); }
        .search-input::placeholder { color:var(--text-muted); }
        .filter-select { padding:11px 36px 11px 16px; border-radius:25px; border:1px solid var(--border); background:var(--surface); color:var(--text); font-family:'Poppins',sans-serif; font-size:14px; outline:none; cursor:pointer; transition:var(--transition); appearance:none; background-image:url("data:image/svg+xml,%3Csvg fill='%235B7BAF' height='20' viewBox='0 0 24 24' width='20' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 12px center; background-size:16px; box-shadow:var(--shadow); }
        .filter-select:focus { border-color:var(--accent); }
        .filter-select option { background:#090979; color:white; }

        /* ── Table ── */
        .table-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; animation:fadeUp 0.55s ease; }
        .table-wrap { overflow-x:auto; }
        table { width:100%; border-collapse:collapse; }
        thead { background:var(--surface-2); border-bottom:1px solid var(--border); }
        th { padding:14px 18px; text-align:left; font-size:11px; font-weight:600; letter-spacing:1px; text-transform:uppercase; color:var(--text-muted); white-space:nowrap; }
        td { padding:14px 18px; font-size:14px; color:var(--text); border-bottom:1px solid var(--border); vertical-align:middle; }
        tr:last-child td { border-bottom:none; }
        tbody tr { transition:background var(--transition); }
        tbody tr:hover { background:var(--surface-2); }

        /* ── User Cell ── */
        .user-cell { display:flex; align-items:center; gap:10px; }
        .user-avatar { width:34px; height:34px; border-radius:50%; background:var(--accent-grad); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; color:white; flex-shrink:0; }
        .user-name  { font-size:13px; font-weight:600; color:var(--text); }
        .user-email { font-size:11px; color:var(--text-muted); margin-top:2px; }

        /* ── Status Badge ── */
        .status-badge { display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.7px; padding:4px 10px; border-radius:99px; white-space:nowrap; }
        .status-badge::before { content:''; width:5px; height:5px; border-radius:50%; background:currentColor; flex-shrink:0; }
        .status-completed  { background:rgba(34,197,94,0.10);  color:#22C55E; border:1px solid rgba(34,197,94,0.25); }
        .status-ongoing    { background:rgba(56,189,248,0.10); color:#38BDF8; border:1px solid rgba(56,189,248,0.25); }
        .status-not-started{ background:rgba(245,158,11,0.10); color:#F59E0B; border:1px solid rgba(245,158,11,0.25); }

        /* ── Action Buttons ── */
        .action-btns { display:flex; gap:6px; }
        .action-btn { width:32px; height:32px; border-radius:8px; border:1px solid var(--border); background:var(--surface-2); color:var(--text-muted); cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all 0.3s ease; }
        .action-btn .material-icons { font-size:17px; }
        .action-btn:hover { transform:translateY(-2px); }
        .action-btn.view:hover   { background:var(--accent-light); color:var(--accent); border-color:var(--accent); }
        .action-btn.status-btn:hover { background:rgba(245,158,11,0.10); color:var(--warning); border-color:var(--warning); }
        .action-btn.delete:hover { background:rgba(239,68,68,0.10); color:var(--danger); border-color:var(--danger); }

        /* ── Pagination ── */
        .pagination { display:flex; justify-content:space-between; align-items:center; padding:16px 20px; border-top:1px solid var(--border); background:var(--surface-2); }
        .pagination-info { font-size:13px; color:var(--text-muted); }
        .pagination-btns { display:flex; gap:6px; }
        .page-btn { width:34px; height:34px; border-radius:8px; border:1px solid var(--border); background:var(--surface); color:var(--text-muted); cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:600; transition:all 0.3s ease; font-family:'Poppins',sans-serif; }
        .page-btn:hover { border-color:var(--accent); color:var(--accent); }
        .page-btn.active { background:linear-gradient(45deg,#38BDF8,#3B82F6); color:white; border-color:transparent; box-shadow:0 4px 14px rgba(56,189,248,0.4); }

        /* ── Workout Detail Modal ── */
        .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); backdrop-filter:blur(6px); z-index:9999; justify-content:center; align-items:center; padding:16px; }
        .modal-overlay.visible { display:flex; }
        .modal-box { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:32px; width:100%; max-width:560px; box-shadow:0 24px 60px rgba(0,0,0,0.3); position:relative; animation:modalFade 0.3s ease; max-height:90vh; overflow-y:auto; }
        .modal-close-btn { position:absolute; top:16px; right:16px; width:32px; height:32px; border-radius:8px; border:1px solid var(--border); background:var(--surface-2); color:var(--text-muted); cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:18px; transition:var(--transition); }
        .modal-close-btn:hover { background:rgba(239,68,68,0.10); color:var(--danger); border-color:var(--danger); }
        .modal-header { margin-bottom:20px; padding-right:40px; }
        .modal-workout-name { font-size:20px; font-weight:700; color:var(--text); margin-bottom:6px; }
        .modal-meta-row { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:18px; }
        .modal-stat-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:20px; }
        .modal-stat { background:var(--surface-2); border:1px solid var(--border); border-radius:var(--radius-sm); padding:12px 14px; text-align:center; }
        .modal-stat-val { font-size:20px; font-weight:700; color:var(--accent); }
        .modal-stat-label { font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-top:3px; }
        .modal-section-title { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted); margin-bottom:10px; }
        .ex-list { display:flex; flex-direction:column; gap:8px; }
        .ex-item { display:flex; align-items:center; gap:12px; padding:10px 14px; background:var(--surface-2); border:1px solid var(--border); border-radius:var(--radius-sm); }
        .ex-item-dot { width:8px; height:8px; border-radius:50%; background:var(--accent-grad); flex-shrink:0; }
        .ex-item-name { flex:1; font-size:13px; font-weight:600; color:var(--text); }
        .ex-item-detail { font-size:12px; color:var(--text-muted); }
        .modal-footer { display:flex; justify-content:flex-end; gap:10px; margin-top:20px; padding-top:16px; border-top:1px solid var(--border); }
        .btn-ghost { padding:10px 18px; border-radius:25px; border:1px solid var(--border); background:var(--surface-2); color:var(--text-muted); font-family:'Poppins',sans-serif; font-size:13px; font-weight:600; cursor:pointer; transition:var(--transition); }
        .btn-ghost:hover { border-color:var(--accent); color:var(--accent); background:var(--accent-light); }
        .btn-danger { padding:10px 18px; border-radius:25px; border:none; background:rgba(239,68,68,0.10); color:var(--danger); font-family:'Poppins',sans-serif; font-size:13px; font-weight:600; cursor:pointer; transition:all 0.25s ease; border:1px solid rgba(239,68,68,0.25); }
        .btn-danger:hover { background:var(--danger); color:white; }

        .swal-on-top { z-index:99999 !important; }
        @media(max-width:1100px){.stats-strip{grid-template-columns:repeat(2,1fr);}}
        @media(max-width:768px){.sidebar{display:none;} .main{padding:20px 16px;} .modal-stat-grid{grid-template-columns:1fr 1fr;}}
    </style>
</head>
<body>
<div class="wrapper">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-brand">💪 Gym<span>Tracker</span></div>
        <div class="admin-badge">Admin Panel</div>
        <div class="sidebar-section">Overview</div>
        <a class="sidebar-link" href="admin_dashboard.php"><span class="material-icons">dashboard</span> Dashboard</a>
        <div class="sidebar-section">Manage</div>
        <a class="sidebar-link" href="admin_users.php"><span class="material-icons">group</span> Users</a>
        <a class="sidebar-link" href="admin_exercises.php"><span class="material-icons">directions_run</span> Exercises</a>
        <a class="sidebar-link active"><span class="material-icons">fitness_center</span> Workouts</a>
        <a class="sidebar-link"><span class="material-icons">bar_chart</span> Reports</a>
        <div class="sidebar-divider"></div>
        <a class="sidebar-link"><span class="material-icons">settings</span> Settings</a>
        <button class="sidebar-logout" onclick="LogoutFunc()"><span class="material-icons">logout</span> Logout</button>
    </aside>

    <!-- MAIN -->
    <main class="main">

        <!-- TOPBAR -->
        <div class="topbar">
            <div class="topbar-left">
                <h1>All Workouts</h1>
                <p>View and manage workouts across all users</p>
            </div>
            <div class="topbar-right">
                <div class="toggle-wrap">
                    <span class="toggle-label" id="theme-label">Light</span>
                    <label class="toggle"><input type="checkbox" id="themeToggle"><div class="toggle-track"></div><div class="toggle-thumb">☀️</div></label>
                </div>
            </div>
        </div>

        <!-- STATS -->
        <div class="stats-strip">
            <div class="strip-card"><div class="strip-icon">🏋️</div><div><div class="strip-val" id="totalWorkouts">—</div><div class="strip-label">Total Workouts</div></div></div>
            <div class="strip-card"><div class="strip-icon">✅</div><div><div class="strip-val" id="totalCompleted">—</div><div class="strip-label">Completed</div></div></div>
            <div class="strip-card"><div class="strip-icon">🔄</div><div><div class="strip-val" id="totalOngoing">—</div><div class="strip-label">Ongoing</div></div></div>
            <div class="strip-card"><div class="strip-icon">⏳</div><div><div class="strip-val" id="totalNotStarted">—</div><div class="strip-label">Not Started</div></div></div>
        </div>

        <!-- TOOLBAR -->
        <div class="toolbar">
            <div class="search-wrap">
                <span class="material-icons">search</span>
                <input class="search-input" id="searchInput" placeholder="Search by workout name or username..." oninput="filterTable()">
            </div>
            <select class="filter-select" id="statusFilter" onchange="filterTable()">
                <option value="">All Status</option>
                <option value="completed">Completed</option>
                <option value="ongoing">Ongoing</option>
                <option value="not-started">Not Started</option>
            </select>
            <select class="filter-select" id="sortFilter" onchange="filterTable()">
                <option value="newest">Newest First</option>
                <option value="oldest">Oldest First</option>
                <option value="name">Name A–Z</option>
                <option value="exercises">Most Exercises</option>
            </select>
        </div>

        <!-- TABLE -->
        <div class="table-card">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Workout</th>
                            <th>User</th>
                            <th>Status</th>
                            <th>Exercises</th>
                            <th>Sets</th>
                            <th>Duration</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="workoutTableBody"></tbody>
                </table>
            </div>
            <div class="pagination">
                <div class="pagination-info" id="paginationInfo">—</div>
                <div class="pagination-btns" id="paginationBtns"></div>
            </div>
        </div>
    </main>
</div>

<!-- WORKOUT DETAIL MODAL -->
<div class="modal-overlay" id="modalOverlay" onclick="closeModalOutside(event)">
    <div class="modal-box">
        <button class="modal-close-btn" onclick="closeModal()">✕</button>
        <div class="modal-header">
            <div class="modal-workout-name" id="modalWorkoutName">—</div>
            <div class="modal-meta-row" id="modalMetaRow"></div>
        </div>
        <div class="modal-stat-grid">
            <div class="modal-stat"><div class="modal-stat-val" id="modalExCount">0</div><div class="modal-stat-label">Exercises</div></div>
            <div class="modal-stat"><div class="modal-stat-val" id="modalSets">0</div><div class="modal-stat-label">Total Sets</div></div>
            <div class="modal-stat"><div class="modal-stat-val" id="modalDuration">—</div><div class="modal-stat-label">Duration</div></div>
        </div>
        <div class="modal-section-title">Description</div>
        <p id="modalDesc" style="font-size:14px;color:var(--text-muted);margin-bottom:18px;line-height:1.6">—</p>
        <div class="modal-section-title">Exercises</div>
        <div class="ex-list" id="modalExList"></div>
        <div class="modal-footer">
            <button class="btn-ghost" onclick="closeModal()">Close</button>
            <button class="btn-danger" id="modalDeleteBtn" onclick="deleteFromModal()">🗑️ Delete Workout</button>
        </div>
    </div>
</div>

<script>
/* ── Theme ── */
const themeToggle = document.getElementById('themeToggle');
const themeLabel  = document.getElementById('theme-label');
const thumbEl     = document.querySelector('.toggle-thumb');
window.addEventListener('DOMContentLoaded', () => { applyTheme(localStorage.getItem('theme')||'light'); loadWorkouts(); });
themeToggle.addEventListener('change', () => { const t=themeToggle.checked?'dark':'light'; applyTheme(t); localStorage.setItem('theme',t); });
function applyTheme(t) { document.documentElement.setAttribute('data-theme',t); themeToggle.checked=t==='dark'; themeLabel.textContent=t==='dark'?'Dark':'Light'; thumbEl.textContent=t==='dark'?'🌙':'☀️'; }

/* ── Data ── */
let workouts=[], filtered=[], currentPage=1;
const perPage=10;
let modalWorkoutId=null;

function loadWorkouts() {
    $.ajax({
        url: '/workout_trackersys/controllers/AdminWorkoutController.php',
        type: 'POST', dataType: 'json',
        data: { action: 'getAllWorkouts' },
        success: function(d) { workouts=d; updateStats(); filterTable(); },
        error: function() {
            // Fallback sample data
            workouts = [
                { id:1,  name:'Chest Day',        user:'Juan Dela Cruz', email:'juan@email.com',   status:'completed',   exercises:4, sets:16, duration:'45 min', created:'May 1, 2026',  desc:'Focus on chest with compound and isolation movements.', exList:[{name:'Bench Press',detail:'4×8 @ 80kg'},{name:'Push-Up',detail:'3×15'},{name:'Incline DB Press',detail:'3×10 @ 30kg'},{name:'Cable Fly',detail:'3×12 @ 20kg'}] },
                { id:2,  name:'Leg Day',           user:'Maria Reyes',    email:'maria@email.com',  status:'completed',   exercises:5, sets:20, duration:'60 min', created:'Apr 30, 2026', desc:'Full lower body session targeting quads, hamstrings and glutes.', exList:[{name:'Squat',detail:'4×8 @ 100kg'},{name:'Leg Press',detail:'3×12 @ 120kg'},{name:'Romanian Deadlift',detail:'3×10 @ 80kg'},{name:'Lunges',detail:'3×12 each'},{name:'Leg Curl',detail:'3×12 @ 40kg'}] },
                { id:3,  name:'Push Day',          user:'Carlo Santos',   email:'carlo@email.com',  status:'ongoing',     exercises:4, sets:14, duration:'40 min', created:'Apr 29, 2026', desc:'Push movements for chest, shoulders and triceps.', exList:[{name:'Overhead Press',detail:'4×6 @ 60kg'},{name:'Bench Press',detail:'3×8 @ 80kg'},{name:'Lateral Raise',detail:'3×12 @ 10kg'},{name:'Tricep Dip',detail:'3×12'}] },
                { id:4,  name:'Pull Day',          user:'Ana Lim',        email:'ana@email.com',    status:'not-started', exercises:4, sets:15, duration:'—',       created:'Apr 28, 2026', desc:'Back and bicep pulling movements.', exList:[{name:'Pull-Up',detail:'4×8'},{name:'Barbell Row',detail:'4×8 @ 70kg'},{name:'Cable Row',detail:'3×10 @ 50kg'},{name:'Bicep Curl',detail:'3×12 @ 20kg'}] },
                { id:5,  name:'HIIT Cardio',       user:'Rico Garcia',    email:'rico@email.com',   status:'completed',   exercises:5, sets:20, duration:'30 min', created:'Apr 27, 2026', desc:'High intensity cardio session for fat burn.', exList:[{name:'Burpees',detail:'4×15'},{name:'Mountain Climbers',detail:'4×30s'},{name:'Jump Squats',detail:'4×15'},{name:'High Knees',detail:'4×30s'},{name:'Jump Rope',detail:'4×60s'}] },
                { id:6,  name:'Upper Body Power',  user:'Gina Flores',    email:'gina@email.com',   status:'ongoing',     exercises:5, sets:18, duration:'50 min', created:'Apr 26, 2026', desc:'Upper body strength with heavy compound lifts.', exList:[{name:'Bench Press',detail:'5×5 @ 100kg'},{name:'Overhead Press',detail:'4×5 @ 65kg'},{name:'Pull-Up',detail:'4×6'},{name:'Barbell Row',detail:'4×6 @ 80kg'},{name:'Face Pull',detail:'3×15'}] },
                { id:7,  name:'Core Strength',     user:'Paolo Mendoza',  email:'paolo@email.com',  status:'completed',   exercises:4, sets:16, duration:'25 min', created:'Apr 25, 2026', desc:'Core stability and strength workout.', exList:[{name:'Plank',detail:'4×60s'},{name:'Crunches',detail:'4×20'},{name:'Russian Twist',detail:'4×20'},{name:'Leg Raise',detail:'4×15'}] },
                { id:8,  name:'Full Body Blast',   user:'Juan Dela Cruz', email:'juan@email.com',   status:'not-started', exercises:6, sets:22, duration:'—',       created:'Apr 24, 2026', desc:'Complete full body workout for all muscle groups.', exList:[{name:'Squat',detail:'4×8'},{name:'Bench Press',detail:'4×8'},{name:'Deadlift',detail:'3×5'},{name:'Overhead Press',detail:'3×8'},{name:'Pull-Up',detail:'3×8'},{name:'Plank',detail:'3×60s'}] },
                { id:9,  name:'Flexibility Flow',  user:'Maria Reyes',    email:'maria@email.com',  status:'completed',   exercises:5, sets:15, duration:'30 min', created:'Apr 23, 2026', desc:'Flexibility and mobility focused session.', exList:[{name:'Cat-Cow',detail:'3×12'},{name:'Hip Flexor Stretch',detail:'3×30s'},{name:'Pigeon Pose',detail:'3×45s each'},{name:'Shoulder Mobility',detail:'3×10'},{name:'Thoracic Rotation',detail:'3×10'}] },
                { id:10, name:'Arm Isolations',    user:'Carlo Santos',   email:'carlo@email.com',  status:'ongoing',     exercises:4, sets:16, duration:'35 min', created:'Apr 22, 2026', desc:'Dedicated arm training for size and definition.', exList:[{name:'Bicep Curl',detail:'4×12 @ 20kg'},{name:'Hammer Curl',detail:'3×12 @ 18kg'},{name:'Tricep Pushdown',detail:'4×12 @ 30kg'},{name:'Skull Crusher',detail:'3×12 @ 30kg'}] },
                { id:11, name:'Shoulder Press Day',user:'Lea Ramos',      email:'lea@email.com',    status:'not-started', exercises:4, sets:14, duration:'—',       created:'Apr 21, 2026', desc:'Shoulder-focused pressing and isolation work.', exList:[{name:'Overhead Press',detail:'4×8 @ 50kg'},{name:'Lateral Raise',detail:'4×12 @ 10kg'},{name:'Front Raise',detail:'3×12 @ 8kg'},{name:'Face Pull',detail:'3×15 @ 20kg'}] },
                { id:12, name:'Back Thickness',    user:'Rico Garcia',    email:'rico@email.com',   status:'completed',   exercises:4, sets:16, duration:'50 min', created:'Apr 20, 2026', desc:'Back thickness and width development workout.', exList:[{name:'Deadlift',detail:'4×5 @ 140kg'},{name:'Barbell Row',detail:'4×8 @ 90kg'},{name:'Cable Row',detail:'4×10 @ 60kg'},{name:'Pull-Up',detail:'4×8'}] },
            ];
            updateStats(); filterTable();
        }
    });
}

/* ── Stats ── */
function updateStats() {
    document.getElementById('totalWorkouts').textContent    = workouts.length;
    document.getElementById('totalCompleted').textContent   = workouts.filter(w=>w.status==='completed').length;
    document.getElementById('totalOngoing').textContent     = workouts.filter(w=>w.status==='ongoing').length;
    document.getElementById('totalNotStarted').textContent  = workouts.filter(w=>w.status==='not-started').length;
}

/* ── Filter & Sort ── */
function filterTable() {
    const q      = document.getElementById('searchInput').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    const sort   = document.getElementById('sortFilter').value;
    filtered = workouts.filter(w =>
        (w.name.toLowerCase().includes(q) || w.user.toLowerCase().includes(q)) &&
        (!status || w.status === status)
    );
    if (sort==='name')      filtered.sort((a,b)=>a.name.localeCompare(b.name));
    else if (sort==='exercises') filtered.sort((a,b)=>b.exercises-a.exercises);
    else if (sort==='oldest') filtered.reverse();
    currentPage=1; renderTable(); renderPagination();
}

/* ── Render Table ── */
function getInitials(name) { return name.split(' ').slice(0,2).map(n=>n[0]).join('').toUpperCase(); }
function statusClass(s) { return s==='completed'?'status-completed':s==='ongoing'?'status-ongoing':'status-not-started'; }
function statusLabel(s) { return s==='completed'?'Completed':s==='ongoing'?'Ongoing':'Not Started'; }

function renderTable() {
    const tbody = document.getElementById('workoutTableBody');
    const start = (currentPage-1)*perPage;
    const paged = filtered.slice(start, start+perPage);
    if (!paged.length) { tbody.innerHTML=`<tr><td colspan="8" style="text-align:center;padding:32px;color:var(--text-muted)">No workouts found.</td></tr>`; document.getElementById('paginationInfo').textContent='No results'; return; }
    tbody.innerHTML = paged.map(w => `
        <tr>
            <td><div style="font-weight:600;color:var(--text)">${w.name}</div><div style="font-size:12px;color:var(--text-muted);margin-top:2px">${w.desc?w.desc.substring(0,40)+'…':''}</div></td>
            <td><div class="user-cell"><div class="user-avatar">${getInitials(w.user)}</div><div><div class="user-name">${w.user}</div><div class="user-email">${w.email}</div></div></div></td>
            <td><span class="status-badge ${statusClass(w.status)}">${statusLabel(w.status)}</span></td>
            <td style="font-size:14px;font-weight:600;color:var(--accent)">${w.exercises}</td>
            <td style="font-size:14px;color:var(--text-muted)">${w.sets}</td>
            <td style="font-size:13px;color:var(--text-muted)">${w.duration}</td>
            <td style="font-size:13px;color:var(--text-muted)">${w.created}</td>
            <td><div class="action-btns">
                <button class="action-btn view"       title="View Details"  onclick="viewWorkout(${w.id})"><span class="material-icons">visibility</span></button>
                <button class="action-btn status-btn" title="Change Status" onclick="changeStatus(${w.id})"><span class="material-icons">sync</span></button>
                <button class="action-btn delete"     title="Delete"        onclick="deleteWorkout(${w.id})"><span class="material-icons">delete</span></button>
            </div></td>
        </tr>`).join('');
    const end = Math.min(start+perPage, filtered.length);
    document.getElementById('paginationInfo').textContent = `Showing ${start+1}–${end} of ${filtered.length} workouts`;
}

/* ── Pagination ── */
function renderPagination() {
    const totalPages = Math.ceil(filtered.length/perPage);
    const btns = document.getElementById('paginationBtns'); btns.innerHTML='';
    const prev=document.createElement('button'); prev.className='page-btn'; prev.innerHTML='<span class="material-icons" style="font-size:16px">chevron_left</span>'; prev.onclick=()=>goPage(currentPage-1); btns.appendChild(prev);
    for(let i=1;i<=Math.min(totalPages,5);i++){const b=document.createElement('button');b.className=`page-btn${i===currentPage?' active':''}`;b.textContent=i;b.onclick=()=>goPage(i);btns.appendChild(b);}
    const next=document.createElement('button'); next.className='page-btn'; next.innerHTML='<span class="material-icons" style="font-size:16px">chevron_right</span>'; next.onclick=()=>goPage(currentPage+1); btns.appendChild(next);
}
function goPage(p){const max=Math.ceil(filtered.length/perPage);if(p<1||p>max)return;currentPage=p;renderTable();renderPagination();}

/* ── View Workout Modal ── */
function viewWorkout(id) {
    const w = workouts.find(x=>x.id===id); if(!w) return;
    modalWorkoutId = id;
    document.getElementById('modalWorkoutName').textContent = w.name;
    document.getElementById('modalMetaRow').innerHTML = `
        <span class="status-badge ${statusClass(w.status)}">${statusLabel(w.status)}</span>
        <span style="font-size:12px;color:var(--text-muted)">by ${w.user}</span>
        <span style="font-size:12px;color:var(--text-muted)">· ${w.created}</span>`;
    document.getElementById('modalExCount').textContent = w.exercises;
    document.getElementById('modalSets').textContent    = w.sets;
    document.getElementById('modalDuration').textContent= w.duration;
    document.getElementById('modalDesc').textContent    = w.desc || '—';
    const exList = document.getElementById('modalExList');
    exList.innerHTML = (w.exList||[]).map(e=>`
        <div class="ex-item">
            <div class="ex-item-dot"></div>
            <div class="ex-item-name">${e.name}</div>
            <div class="ex-item-detail">${e.detail}</div>
        </div>`).join('');
    document.getElementById('modalOverlay').classList.add('visible');
}
function closeModal() { document.getElementById('modalOverlay').classList.remove('visible'); modalWorkoutId=null; }
function closeModalOutside(e) { if(e.target===document.getElementById('modalOverlay')) closeModal(); }
function deleteFromModal() { closeModal(); if(modalWorkoutId) deleteWorkout(modalWorkoutId); }

/* ── Change Status ── */
function changeStatus(id) {
    const w = workouts.find(x=>x.id===id); if(!w) return;
    const statuses = ['not-started','ongoing','completed'];
    const labels   = ['Not Started','Ongoing','Completed'];
    GymSwal.fire({
        title: `Change Status: "${w.name}"`,
        html: statuses.map((s,i)=>`<button onclick="setStatus(${id},'${s}');Swal.close()" style="margin:6px;padding:9px 16px;border-radius:25px;border:none;font-family:Poppins,sans-serif;font-size:13px;font-weight:600;cursor:pointer;background:${s===w.status?'linear-gradient(45deg,#38BDF8,#3B82F6)':'rgba(255,255,255,0.15)'};color:white">${labels[i]}</button>`).join(''),
        showConfirmButton: false,
        showCancelButton: true,
        cancelButtonText: 'Cancel'
    });
}
function setStatus(id, status) {
    $.ajax({
        url: '/workout_trackersys/controllers/AdminWorkoutController.php',
        type: 'POST', dataType: 'json',
        data: { action:'changeStatus', id, status },
        success: function() { const w=workouts.find(x=>x.id===id); if(w) w.status=status; updateStats(); filterTable(); GymToast.fire({ icon:'success', title:'Status updated!' }); },
        error:   function() { const w=workouts.find(x=>x.id===id); if(w) w.status=status; updateStats(); filterTable(); GymToast.fire({ icon:'success', title:'Status updated! (preview)' }); }
    });
}

/* ── Delete Workout ── */
function deleteWorkout(id) {
    const w=workouts.find(x=>x.id===id);
    GymSwal.fire({ title:`Delete "${w?.name}"?`, text:'This will permanently delete this workout and all its exercises.', icon:'warning', showCancelButton:true, confirmButtonText:'Yes, delete', cancelButtonText:'Cancel' })
    .then(r => {
        if (!r.isConfirmed) return;
        $.ajax({
            url: '/workout_trackersys/controllers/AdminWorkoutController.php',
            type: 'POST', dataType: 'json',
            data: { action:'delete', id },
            success: function() { workouts=workouts.filter(x=>x.id!==id); filtered=filtered.filter(x=>x.id!==id); updateStats(); renderTable(); renderPagination(); GymToast.fire({ icon:'success', title:'Workout deleted!' }); },
            error:   function() { workouts=workouts.filter(x=>x.id!==id); filtered=filtered.filter(x=>x.id!==id); updateStats(); renderTable(); renderPagination(); GymToast.fire({ icon:'success', title:'Deleted! (preview)' }); }
        });
    });
}

/* ── GymSwal & GymToast ── */
const GymSwal = Swal.mixin({ customClass:{ container:'swal-on-top' }, backdrop:'rgba(0,0,0,0.5)', didOpen:(p)=>{ p.style.background='rgba(9,9,121,0.95)'; p.style.backdropFilter='blur(20px)'; p.style.border='1px solid rgba(255,255,255,0.2)'; p.style.borderRadius='20px'; p.style.color='#fff'; p.style.fontFamily='Poppins,sans-serif'; const t=p.querySelector('.swal2-title'); if(t)t.style.color='#fff'; const c=p.querySelector('.swal2-confirm'); if(c){c.style.background='linear-gradient(45deg,#38BDF8,#3B82F6)';c.style.border='none';c.style.borderRadius='50px';c.style.fontFamily='Poppins,sans-serif';c.style.fontWeight='600';} const x=p.querySelector('.swal2-cancel'); if(x){x.style.background='rgba(255,255,255,0.15)';x.style.color='white';x.style.border='1px solid rgba(255,255,255,0.2)';x.style.borderRadius='50px';} } });
const GymToast = Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:3000, timerProgressBar:true, customClass:{ container:'swal-on-top' }, didOpen:(t)=>{ t.style.background='rgba(9,9,121,0.95)'; t.style.backdropFilter='blur(20px)'; t.style.border='1px solid rgba(255,255,255,0.2)'; t.style.borderRadius='12px'; t.style.color='#fff'; t.addEventListener('mouseenter',Swal.stopTimer); t.addEventListener('mouseleave',Swal.resumeTimer); } });
function LogoutFunc() { window.location.href='/workout_trackersys/views/auth/log.php'; }
</script>
</body>
</html>