<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymTracker Admin — Exercises</title>
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

        /* ── Sidebar ── */
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

        /* ── Toggle ── */
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

        /* ── Add Button ── */
        .btn-add-ex { display:inline-flex; align-items:center; gap:8px; padding:11px 22px; border-radius:25px; border:none; background:linear-gradient(45deg,#38BDF8,#3B82F6); color:white; font-family:'Poppins',sans-serif; font-size:14px; font-weight:600; cursor:pointer; transition:all 0.3s ease; box-shadow:0 6px 20px rgba(56,189,248,0.35); white-space:nowrap; }
        .btn-add-ex:hover { transform:translateY(-2px); box-shadow:0 12px 28px rgba(56,189,248,0.5); }

        /* ── Table Card ── */
        .table-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; animation:fadeUp 0.55s ease; }
        .table-wrap { overflow-x:auto; }
        table { width:100%; border-collapse:collapse; }
        thead { background:var(--surface-2); border-bottom:1px solid var(--border); }
        th { padding:14px 18px; text-align:left; font-size:11px; font-weight:600; letter-spacing:1px; text-transform:uppercase; color:var(--text-muted); white-space:nowrap; }
        td { padding:14px 18px; font-size:14px; color:var(--text); border-bottom:1px solid var(--border); vertical-align:middle; }
        tr:last-child td { border-bottom:none; }
        tbody tr { transition:background var(--transition); }
        tbody tr:hover { background:var(--surface-2); }

        /* ── Muscle Badge ── */
        .muscle-badge { display:inline-block; font-size:11px; font-weight:700; text-transform:capitalize; letter-spacing:0.5px; padding:4px 10px; border-radius:99px; border:1px solid transparent; }
        .muscle-chest     { background:rgba(239,68,68,0.10);   color:#EF4444;   border-color:rgba(239,68,68,0.25); }
        .muscle-back      { background:rgba(59,130,246,0.10);  color:#3B82F6;   border-color:rgba(59,130,246,0.25); }
        .muscle-shoulders { background:rgba(245,158,11,0.10);  color:#F59E0B;   border-color:rgba(245,158,11,0.25); }
        .muscle-legs      { background:rgba(34,197,94,0.10);   color:#22C55E;   border-color:rgba(34,197,94,0.25); }
        .muscle-arms      { background:rgba(168,85,247,0.10);  color:#A855F7;   border-color:rgba(168,85,247,0.25); }
        .muscle-core      { background:rgba(56,189,248,0.10);  color:#38BDF8;   border-color:rgba(56,189,248,0.25); }
        .muscle-cardio    { background:rgba(249,115,22,0.10);  color:#F97316;   border-color:rgba(249,115,22,0.25); }
        .muscle-full-body { background:rgba(99,102,241,0.10);  color:#6366F1;   border-color:rgba(99,102,241,0.25); }

        /* ── Difficulty Badge ── */
        .diff-badge { display:inline-block; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; padding:4px 10px; border-radius:99px; }
        .diff-beginner     { background:rgba(34,197,94,0.10);  color:#22C55E; }
        .diff-intermediate { background:rgba(56,189,248,0.10); color:#38BDF8; }
        .diff-advanced     { background:rgba(239,68,68,0.10);  color:#EF4444; }

        /* ── Action Buttons ── */
        .action-btns { display:flex; gap:6px; align-items:center; }
        .action-btn { width:32px; height:32px; border-radius:8px; border:1px solid var(--border); background:var(--surface-2); color:var(--text-muted); cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all 0.3s ease; }
        .action-btn .material-icons { font-size:17px; }
        .action-btn:hover { transform:translateY(-2px); }
        .action-btn.edit:hover   { background:var(--accent-light); color:var(--accent); border-color:var(--accent); }
        .action-btn.delete:hover { background:rgba(239,68,68,0.10); color:var(--danger); border-color:var(--danger); }

        /* ── Pagination ── */
        .pagination { display:flex; justify-content:space-between; align-items:center; padding:16px 20px; border-top:1px solid var(--border); background:var(--surface-2); }
        .pagination-info { font-size:13px; color:var(--text-muted); }
        .pagination-btns { display:flex; gap:6px; }
        .page-btn { width:34px; height:34px; border-radius:8px; border:1px solid var(--border); background:var(--surface); color:var(--text-muted); cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:600; transition:all 0.3s ease; font-family:'Poppins',sans-serif; }
        .page-btn:hover { border-color:var(--accent); color:var(--accent); }
        .page-btn.active { background:linear-gradient(45deg,#38BDF8,#3B82F6); color:white; border-color:transparent; box-shadow:0 4px 14px rgba(56,189,248,0.4); }

        /* ── Modal ── */
        .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); backdrop-filter:blur(6px); z-index:9999; justify-content:center; align-items:center; padding:16px; }
        .modal-overlay.visible { display:flex; }
        .modal-box { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:32px; width:100%; max-width:560px; box-shadow:0 24px 60px rgba(0,0,0,0.3); position:relative; animation:modalFade 0.3s ease; max-height:90vh; overflow-y:auto; }
        .modal-close-btn { position:absolute; top:16px; right:16px; width:32px; height:32px; border-radius:8px; border:1px solid var(--border); background:var(--surface-2); color:var(--text-muted); cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:18px; transition:var(--transition); }
        .modal-close-btn:hover { background:rgba(239,68,68,0.10); color:var(--danger); border-color:var(--danger); }
        .modal-title { font-size:20px; font-weight:700; color:var(--text); margin-bottom:6px; padding-right:40px; }
        .modal-subtitle { font-size:13px; color:var(--text-muted); margin-bottom:22px; }
        .form-group { display:flex; flex-direction:column; gap:6px; margin-bottom:16px; }
        .form-label { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:var(--text-muted); }
        .form-input,.form-select,.form-textarea { padding:11px 14px; border:1px solid var(--border); border-radius:var(--radius-sm); background:var(--surface-2); color:var(--text); font-family:'Poppins',sans-serif; font-size:14px; outline:none; transition:var(--transition); width:100%; }
        .form-input:focus,.form-select:focus,.form-textarea:focus { border-color:var(--accent); box-shadow:0 0 0 3px var(--accent-light); background:var(--surface); }
        .form-input::placeholder,.form-textarea::placeholder { color:var(--text-muted); }
        .form-select { appearance:none; cursor:pointer; background-image:url("data:image/svg+xml,%3Csvg fill='%235B7BAF' height='20' viewBox='0 0 24 24' width='20' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 12px center; background-size:16px; padding-right:36px; }
        .form-select option { background:#090979; color:white; }
        .form-textarea { resize:vertical; min-height:90px; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
        .modal-footer { display:flex; justify-content:flex-end; gap:10px; margin-top:8px; padding-top:16px; border-top:1px solid var(--border); }
        .btn-cancel { padding:11px 22px; border-radius:25px; border:1px solid var(--border); background:var(--surface-2); color:var(--text-muted); font-family:'Poppins',sans-serif; font-size:14px; font-weight:600; cursor:pointer; transition:var(--transition); }
        .btn-cancel:hover { background:var(--border); color:var(--text); }
        .btn-save { padding:11px 26px; border-radius:25px; border:none; background:linear-gradient(45deg,#38BDF8,#3B82F6); color:white; font-family:'Poppins',sans-serif; font-size:14px; font-weight:600; cursor:pointer; transition:all 0.3s ease; box-shadow:0 6px 20px rgba(56,189,248,0.35); }
        .btn-save:hover { transform:translateY(-2px); box-shadow:0 12px 28px rgba(56,189,248,0.5); }

        /* ── Step tags input ── */
        .steps-list { display:flex; flex-direction:column; gap:8px; margin-bottom:8px; }
        .step-row { display:flex; gap:8px; align-items:center; }
        .step-num-badge { width:24px; height:24px; border-radius:50%; background:var(--accent-grad); color:white; font-size:11px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .step-input { flex:1; padding:9px 12px; border:1px solid var(--border); border-radius:var(--radius-sm); background:var(--surface-2); color:var(--text); font-family:'Poppins',sans-serif; font-size:13px; outline:none; transition:var(--transition); }
        .step-input:focus { border-color:var(--accent); box-shadow:0 0 0 2px var(--accent-light); }
        .step-del-btn { width:28px; height:28px; border-radius:6px; border:1px solid var(--border); background:var(--surface-2); color:var(--text-muted); cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:14px; transition:var(--transition); flex-shrink:0; }
        .step-del-btn:hover { background:rgba(239,68,68,0.10); color:var(--danger); border-color:var(--danger); }
        .add-step-btn { display:inline-flex; align-items:center; gap:6px; padding:7px 14px; border-radius:25px; border:1px solid var(--border); background:var(--surface-2); color:var(--text-muted); font-family:'Poppins',sans-serif; font-size:12px; font-weight:600; cursor:pointer; transition:var(--transition); }
        .add-step-btn:hover { border-color:var(--accent); color:var(--accent); background:var(--accent-light); }

        .swal-on-top { z-index:99999 !important; }
        @media(max-width:1100px){.stats-strip{grid-template-columns:repeat(2,1fr);}}
        @media(max-width:768px){.sidebar{display:none;} .main{padding:20px 16px;} .form-row{grid-template-columns:1fr;}}
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
        <a class="sidebar-link active"><span class="material-icons">directions_run</span> Exercises</a>
        <a class="sidebar-link" href="admin_workouts.php"><span class="material-icons">fitness_center</span> Workouts</a>
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
                <h1>Exercise Library</h1>
                <p>Add, edit, and delete exercises available to all users</p>
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
            <div class="strip-card"><div class="strip-icon">📚</div><div><div class="strip-val" id="totalEx">—</div><div class="strip-label">Total Exercises</div></div></div>
            <div class="strip-card"><div class="strip-icon">💪</div><div><div class="strip-val" id="totalMuscles">8</div><div class="strip-label">Muscle Groups</div></div></div>
            <div class="strip-card"><div class="strip-icon">🏠</div><div><div class="strip-val" id="totalHome">—</div><div class="strip-label">Home Friendly</div></div></div>
            <div class="strip-card"><div class="strip-icon">🔥</div><div><div class="strip-val" id="totalAdv">—</div><div class="strip-label">Advanced Level</div></div></div>
        </div>

        <!-- TOOLBAR -->
        <div class="toolbar">
            <div class="search-wrap">
                <span class="material-icons">search</span>
                <input class="search-input" id="searchInput" placeholder="Search by name, muscle, or equipment..." oninput="filterTable()">
            </div>
            <select class="filter-select" id="muscleFilter" onchange="filterTable()">
                <option value="">All Muscles</option>
                <option value="chest">Chest</option>
                <option value="back">Back</option>
                <option value="shoulders">Shoulders</option>
                <option value="legs">Legs</option>
                <option value="arms">Arms</option>
                <option value="core">Core</option>
                <option value="cardio">Cardio</option>
                <option value="full-body">Full Body</option>
            </select>
            <select class="filter-select" id="diffFilter" onchange="filterTable()">
                <option value="">All Levels</option>
                <option value="Beginner">Beginner</option>
                <option value="Intermediate">Intermediate</option>
                <option value="Advanced">Advanced</option>
            </select>
            <select class="filter-select" id="locFilter" onchange="filterTable()">
                <option value="">All Locations</option>
                <option value="Gym">Gym</option>
                <option value="Home">Home</option>
                <option value="Outdoors">Outdoors</option>
            </select>
            <button class="btn-add-ex" onclick="openModal()">
                <span class="material-icons" style="font-size:18px">add</span> Add Exercise
            </button>
        </div>

        <!-- TABLE -->
        <div class="table-card">
            <div class="table-wrap">
                <table id="exerciseTable">
                    <thead>
                        <tr>
                            <th>Exercise</th>
                            <th>Muscle Group</th>
                            <th>Difficulty</th>
                            <th>Equipment</th>
                            <th>Location</th>
                            <th>Steps</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="exerciseTableBody"></tbody>
                </table>
            </div>
            <div class="pagination">
                <div class="pagination-info" id="paginationInfo">—</div>
                <div class="pagination-btns" id="paginationBtns"></div>
            </div>
        </div>
    </main>
</div>

<!-- ADD / EDIT MODAL -->
<div class="modal-overlay" id="modalOverlay" onclick="closeModalOutside(event)">
    <div class="modal-box">
        <button class="modal-close-btn" onclick="closeModal()">✕</button>
        <div class="modal-title" id="modalTitle">Add Exercise</div>
        <div class="modal-subtitle" id="modalSubtitle">Fill in the details to add a new exercise to the library.</div>

        <input type="hidden" id="editId">

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Exercise Name</label>
                <input type="text" class="form-input" id="exName" placeholder="e.g. Bench Press">
            </div>
            <div class="form-group">
                <label class="form-label">Emoji / Icon</label>
                <input type="text" class="form-input" id="exEmoji" placeholder="e.g. 🏋️" maxlength="4">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Muscle Group</label>
                <select class="form-select" id="exMuscle">
                    <option value="chest">Chest</option>
                    <option value="back">Back</option>
                    <option value="shoulders">Shoulders</option>
                    <option value="legs">Legs</option>
                    <option value="arms">Arms</option>
                    <option value="core">Core</option>
                    <option value="cardio">Cardio</option>
                    <option value="full-body">Full Body</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Difficulty</label>
                <select class="form-select" id="exDiff">
                    <option value="Beginner">Beginner</option>
                    <option value="Intermediate">Intermediate</option>
                    <option value="Advanced">Advanced</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Equipment</label>
                <input type="text" class="form-input" id="exEquip" placeholder="e.g. Barbell, Dumbbells, None">
            </div>
            <div class="form-group">
                <label class="form-label">Location</label>
                <select class="form-select" id="exLoc">
                    <option value="Gym">Gym</option>
                    <option value="Home">Home</option>
                    <option value="Outdoors">Outdoors</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea class="form-textarea" id="exDesc" placeholder="Brief description of the exercise..."></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Tags <span style="font-weight:400;text-transform:none;letter-spacing:0">(comma separated)</span></label>
            <input type="text" class="form-input" id="exTags" placeholder="e.g. Compound, Strength, Push">
        </div>

        <div class="form-group">
            <label class="form-label">How to Perform (Steps)</label>
            <div class="steps-list" id="stepsList"></div>
            <button class="add-step-btn" onclick="addStep()">
                <span class="material-icons" style="font-size:16px">add</span> Add Step
            </button>
        </div>

        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal()">Cancel</button>
            <button class="btn-save" onclick="saveExercise()">
                <span class="material-icons" style="font-size:16px;vertical-align:middle">save</span> Save Exercise
            </button>
        </div>
    </div>
</div>

<script>
/* ── Theme ── */
const themeToggle = document.getElementById('themeToggle');
const themeLabel  = document.getElementById('theme-label');
const thumbEl     = document.querySelector('.toggle-thumb');
window.addEventListener('DOMContentLoaded', () => { applyTheme(localStorage.getItem('theme')||'light'); loadExercises(); });
themeToggle.addEventListener('change', () => { const t=themeToggle.checked?'dark':'light'; applyTheme(t); localStorage.setItem('theme',t); });
function applyTheme(t) { document.documentElement.setAttribute('data-theme',t); themeToggle.checked=t==='dark'; themeLabel.textContent=t==='dark'?'Dark':'Light'; thumbEl.textContent=t==='dark'?'🌙':'☀️'; }

/* ── Data ── */
let exercises = [], filtered = [], currentPage = 1;
const perPage = 8;

function loadExercises() {
    $.ajax({
        url: '/workout_trackersys/controllers/AdminExerciseController.php',
        type: 'POST', dataType: 'json',
        data: { action: 'getAll' },
        success: function(d) { exercises = d; updateStats(); filterTable(); },
        error: function() {
            // Fallback sample data
            exercises = [
                { id:1,  name:'Bench Press',       emoji:'🏋️', muscle:'chest',     difficulty:'Intermediate', equipment:'Barbell',       location:'Gym',     desc:'A compound upper body exercise targeting chest, shoulders and triceps.', steps:['Lie on bench','Grip bar shoulder-width','Lower to chest','Press up'], tags:'Compound,Strength,Push' },
                { id:2,  name:'Push-Up',            emoji:'💪', muscle:'chest',     difficulty:'Beginner',     equipment:'None',           location:'Home',    desc:'Fundamental bodyweight chest exercise.',                              steps:['High plank position','Lower chest to floor','Push back up'],           tags:'Bodyweight,Beginner,Home' },
                { id:3,  name:'Pull-Up',            emoji:'🔝', muscle:'back',      difficulty:'Intermediate', equipment:'Pull-up bar',    location:'Home',    desc:'Classic back and bicep builder.',                                      steps:['Hang from bar','Pull chin over bar','Lower slowly'],                   tags:'Bodyweight,Back,Strength' },
                { id:4,  name:'Deadlift',           emoji:'🏋️', muscle:'back',      difficulty:'Advanced',     equipment:'Barbell',        location:'Gym',     desc:'The king of compound lifts.',                                          steps:['Stand with bar over feet','Hinge and grip bar','Drive through floor','Lock out'], tags:'Compound,Advanced,Strength' },
                { id:5,  name:'Overhead Press',     emoji:'🏋️', muscle:'shoulders', difficulty:'Intermediate', equipment:'Barbell',        location:'Gym',     desc:'Primary shoulder strength builder.',                                   steps:['Bar at collarbone','Press overhead','Lower with control'],             tags:'Compound,Shoulders,Push' },
                { id:6,  name:'Squat',              emoji:'🦵', muscle:'legs',      difficulty:'Beginner',     equipment:'Barbell',        location:'Gym',     desc:'The fundamental lower body exercise.',                                 steps:['Bar on traps','Hinge hips down','Thighs parallel','Drive up'],         tags:'Compound,Legs,Strength' },
                { id:7,  name:'Bicep Curl',         emoji:'💪', muscle:'arms',      difficulty:'Beginner',     equipment:'Dumbbells',      location:'Gym',     desc:'Classic isolation exercise for bicep peak.',                           steps:['Hold dumbbells','Curl to shoulder','Squeeze','Lower slowly'],          tags:'Isolation,Biceps,Arms' },
                { id:8,  name:'Plank',              emoji:'🔥', muscle:'core',      difficulty:'Beginner',     equipment:'None',           location:'Home',    desc:'Foundational isometric core exercise.',                                steps:['Forearms on floor','Straight body line','Engage core','Hold'],         tags:'Core,Isometric,Home' },
                { id:9,  name:'Running',            emoji:'🏃', muscle:'cardio',    difficulty:'Beginner',     equipment:'None',           location:'Outdoors',desc:'Natural endurance and calorie-burning cardio.',                        steps:['Warm up walk','Run at comfortable pace','Cool down'],                  tags:'Cardio,Endurance,Outdoors' },
                { id:10, name:'Burpee',             emoji:'⚡', muscle:'full-body', difficulty:'Intermediate', equipment:'None',           location:'Home',    desc:'Full body HIIT movement for maximum calorie burn.',                   steps:['Squat down','Jump feet back','Push-up','Jump feet forward','Jump up'], tags:'HIIT,Full Body,Cardio' },
                { id:11, name:'Lateral Raise',      emoji:'💥', muscle:'shoulders', difficulty:'Beginner',     equipment:'Dumbbells',      location:'Gym',     desc:'Isolation for medial deltoid width.',                                  steps:['Hold dumbbells at sides','Raise to shoulder height','Lower slowly'],   tags:'Isolation,Shoulders,Dumbbells' },
                { id:12, name:'Romanian Deadlift',  emoji:'🏃', muscle:'legs',      difficulty:'Intermediate', equipment:'Barbell',        location:'Gym',     desc:'Targets hamstrings and glutes with hip hinge.',                        steps:['Hold bar at hips','Push hips back','Feel stretch','Drive hips forward'],tags:'Compound,Hamstrings,Hip Hinge' },
            ];
            updateStats(); filterTable();
        }
    });
}

function updateStats() {
    document.getElementById('totalEx').textContent    = exercises.length;
    document.getElementById('totalHome').textContent  = exercises.filter(e=>e.location==='Home').length;
    document.getElementById('totalAdv').textContent   = exercises.filter(e=>e.difficulty==='Advanced').length;
}

/* ── Filter ── */
function filterTable() {
    const q    = document.getElementById('searchInput').value.toLowerCase();
    const mus  = document.getElementById('muscleFilter').value;
    const diff = document.getElementById('diffFilter').value;
    const loc  = document.getElementById('locFilter').value;
    filtered = exercises.filter(e =>
        (e.name.toLowerCase().includes(q) || e.muscle.includes(q) || (e.equipment||'').toLowerCase().includes(q)) &&
        (!mus  || e.muscle     === mus) &&
        (!diff || e.difficulty === diff) &&
        (!loc  || e.location   === loc)
    );
    currentPage = 1; renderTable(); renderPagination();
}

/* ── Render Table ── */
function renderTable() {
    const tbody = document.getElementById('exerciseTableBody');
    const start = (currentPage-1)*perPage;
    const paged = filtered.slice(start, start+perPage);
    if (!paged.length) { tbody.innerHTML=`<tr><td colspan="7" style="text-align:center;padding:32px;color:var(--text-muted)">No exercises found.</td></tr>`; updatePaginationInfo(0,0,0); return; }
    tbody.innerHTML = paged.map(e => `
        <tr>
            <td><div style="display:flex;align-items:center;gap:10px">
                <div style="width:36px;height:36px;border-radius:10px;background:var(--accent-light);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">${e.emoji||'🏋️'}</div>
                <div><div style="font-weight:600;color:var(--text)">${e.name}</div><div style="font-size:12px;color:var(--text-muted)">${(e.tags||'').split(',')[0]||''}</div></div>
            </div></td>
            <td><span class="muscle-badge muscle-${e.muscle}">${e.muscle.replace('-',' ')}</span></td>
            <td><span class="diff-badge diff-${e.difficulty.toLowerCase()}">${e.difficulty}</span></td>
            <td style="font-size:13px;color:var(--text-muted)">${e.equipment||'—'}</td>
            <td style="font-size:13px;color:var(--text-muted)">${e.location||'—'}</td>
            <td style="font-size:13px;color:var(--text-muted)">${Array.isArray(e.steps)?e.steps.length:e.steps.split(',').length} steps</td>
            <td><div class="action-btns">
                <button class="action-btn edit"   title="Edit"   onclick="editExercise(${e.id})"><span class="material-icons">edit</span></button>
                <button class="action-btn delete" title="Delete" onclick="deleteExercise(${e.id})"><span class="material-icons">delete</span></button>
            </div></td>
        </tr>`).join('');
    const end = Math.min(start+perPage, filtered.length);
    updatePaginationInfo(start+1, end, filtered.length);
}

/* ── Pagination ── */
function updatePaginationInfo(s,e,t) { document.getElementById('paginationInfo').textContent = t ? `Showing ${s}–${e} of ${t} exercises` : 'No results'; }
function renderPagination() {
    const totalPages = Math.ceil(filtered.length/perPage);
    const btns = document.getElementById('paginationBtns');
    btns.innerHTML = '';
    const prev = document.createElement('button'); prev.className='page-btn'; prev.innerHTML='<span class="material-icons" style="font-size:16px">chevron_left</span>'; prev.onclick=()=>goPage(currentPage-1); btns.appendChild(prev);
    for (let i=1;i<=Math.min(totalPages,5);i++) {
        const b = document.createElement('button'); b.className=`page-btn${i===currentPage?' active':''}`; b.textContent=i; b.onclick=()=>goPage(i); btns.appendChild(b);
    }
    const next = document.createElement('button'); next.className='page-btn'; next.innerHTML='<span class="material-icons" style="font-size:16px">chevron_right</span>'; next.onclick=()=>goPage(currentPage+1); btns.appendChild(next);
}
function goPage(p) { const max=Math.ceil(filtered.length/perPage); if(p<1||p>max) return; currentPage=p; renderTable(); renderPagination(); }

/* ── Steps builder ── */
function renderSteps(steps=[]) {
    const list = document.getElementById('stepsList'); list.innerHTML='';
    steps.forEach((s,i) => addStepRow(s,i+1));
}
function addStep() { const list=document.getElementById('stepsList'); addStepRow('', list.children.length+1); }
function addStepRow(val,num) {
    const list=document.getElementById('stepsList');
    const row=document.createElement('div'); row.className='step-row';
    row.innerHTML=`<div class="step-num-badge">${num}</div><input type="text" class="step-input" placeholder="Describe step ${num}..." value="${val}"><button class="step-del-btn" onclick="removeStep(this)" title="Remove">✕</button>`;
    list.appendChild(row);
}
function removeStep(btn) {
    btn.closest('.step-row').remove();
    document.querySelectorAll('#stepsList .step-num-badge').forEach((b,i) => b.textContent=i+1);
}
function getSteps() { return [...document.querySelectorAll('#stepsList .step-input')].map(i=>i.value.trim()).filter(Boolean); }

/* ── Modal open/close ── */
let editingId = null;
function openModal(exercise=null) {
    editingId = exercise ? exercise.id : null;
    document.getElementById('modalTitle').textContent    = exercise ? 'Edit Exercise' : 'Add Exercise';
    document.getElementById('modalSubtitle').textContent = exercise ? 'Update the exercise details below.' : 'Fill in the details to add a new exercise.';
    document.getElementById('exName').value   = exercise?.name   || '';
    document.getElementById('exEmoji').value  = exercise?.emoji  || '';
    document.getElementById('exMuscle').value = exercise?.muscle || 'chest';
    document.getElementById('exDiff').value   = exercise?.difficulty || 'Beginner';
    document.getElementById('exEquip').value  = exercise?.equipment || '';
    document.getElementById('exLoc').value    = exercise?.location || 'Gym';
    document.getElementById('exDesc').value   = exercise?.desc || '';
    document.getElementById('exTags').value   = Array.isArray(exercise?.tags) ? exercise.tags.join(',') : (exercise?.tags||'');
    const steps = Array.isArray(exercise?.steps) ? exercise.steps : (exercise?.steps||'').split(',').filter(Boolean);
    renderSteps(steps.length ? steps : ['']);
    document.getElementById('modalOverlay').classList.add('visible');
}
function closeModal() { document.getElementById('modalOverlay').classList.remove('visible'); editingId=null; }
function closeModalOutside(e) { if(e.target===document.getElementById('modalOverlay')) closeModal(); }
function editExercise(id) { const ex=exercises.find(e=>e.id===id); if(ex) openModal(ex); }

/* ── Save Exercise ── */
function saveExercise() {
    const name  = document.getElementById('exName').value.trim();
    const emoji = document.getElementById('exEmoji').value.trim();
    const muscle= document.getElementById('exMuscle').value;
    const diff  = document.getElementById('exDiff').value;
    const equip = document.getElementById('exEquip').value.trim();
    const loc   = document.getElementById('exLoc').value;
    const desc  = document.getElementById('exDesc').value.trim();
    const tags  = document.getElementById('exTags').value.trim();
    const steps = getSteps();
    if (!name) { GymSwal.fire({ icon:'warning', title:'Please enter an exercise name.' }); return; }
    if (!steps.length) { GymSwal.fire({ icon:'warning', title:'Please add at least one step.' }); return; }
    $.ajax({
        url: '/workout_trackersys/controllers/AdminExerciseController.php',
        type: 'POST', dataType: 'json',
        data: { action: editingId ? 'edit' : 'add', id:editingId, name, emoji, muscle, difficulty:diff, equipment:equip, location:loc, desc, tags, steps:JSON.stringify(steps) },
        success: function(d) {
            closeModal();
            GymToast.fire({ icon:'success', title: editingId ? 'Exercise updated!' : 'Exercise added!' });
            loadExercises();
        },
        error: function() {
            // Frontend preview: update local array
            if (editingId) {
                const idx = exercises.findIndex(e=>e.id===editingId);
                if (idx>-1) exercises[idx] = { ...exercises[idx], name, emoji, muscle, difficulty:diff, equipment:equip, location:loc, desc, tags, steps };
            } else {
                exercises.push({ id: Date.now(), name, emoji, muscle, difficulty:diff, equipment:equip, location:loc, desc, tags, steps });
            }
            closeModal(); updateStats(); filterTable();
            GymToast.fire({ icon:'success', title: editingId ? 'Exercise updated! (preview)' : 'Exercise added! (preview)' });
        }
    });
}

/* ── Delete Exercise ── */
function deleteExercise(id) {
    const ex = exercises.find(e=>e.id===id);
    GymSwal.fire({ title:`Delete "${ex?.name}"?`, text:'This will remove the exercise from the library permanently.', icon:'warning', showCancelButton:true, confirmButtonText:'Yes, delete', cancelButtonText:'Cancel' })
    .then(r => {
        if (!r.isConfirmed) return;
        $.ajax({
            url: '/workout_trackersys/controllers/AdminExerciseController.php',
            type: 'POST', dataType: 'json',
            data: { action:'delete', id },
            success: function() { GymToast.fire({ icon:'success', title:'Exercise deleted!' }); loadExercises(); },
            error: function() {
                exercises = exercises.filter(e=>e.id!==id); updateStats(); filterTable();
                GymToast.fire({ icon:'success', title:'Deleted! (preview)' });
            }
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