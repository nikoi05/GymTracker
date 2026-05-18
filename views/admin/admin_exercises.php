<?php
require_once __DIR__ . '/../../BL/exerciseManage.php';
$em = new ExerciseManage();
$exercises = $em->getAllExercise();
$types = $em->getExerciseType();
$muscles = $em->getMuscleGroups();
$difficulty = $em->getDifficulty();
$location =  $em->getLocation();
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymTracker Admin - Exercises</title>

    <!-- Shared admin styles (sidebar, top-bar, hero, stats, cards, footer, theme) -->
    <link rel="stylesheet" href="/workout_trackersys/assets/admin-managers.css">
    <!-- AdminUsers style for table + modal - we reuse the same design language -->
    <link rel="stylesheet" href="/workout_trackersys/assets/AdminUsers.css">

        <link rel='stylesheet' href='/workout_trackersys/assets/admin-exercises.css'>
    <link href='https://fonts.googleapis.com/icon?family=Material+Icons' rel='stylesheet'>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/workout_trackersys/assets/footer.css">
</head>
<body>

<!-- SIDEBAR (same markup as all admin pages) -->
<div class="Sidebar" id="sidebar">
    <div class="NavLinks">
        <button class="toggle-btn" onclick="ToggleSidebar()">
            <i class="material-icons">menu</i>
        </button>
        <div class="sidebar-brand"><span class="sidebar-logo" aria-hidden="true">&#128170;</span><span class="text"><span class="brand-gym">Gym</span>Tracker Admin</span></div>
        <ul>
            <li onclick="redirectAdmin(1)"><i class="material-icons icon">dashboard</i><span class="text">Dashboard</span></li>
            <li onclick="redirectAdmin(2)"><i class="material-icons icon">people</i><span class="text">Users</span></li>
            <li class="active" onclick="redirectAdmin(3)"><i class="material-icons icon">fitness_center</i><span class="text">Exercises</span></li>
            <li onclick="redirectAdmin(6)"><i class="material-icons icon">self_improvement</i><span class="text">Workouts</span></li>
            <li onclick="redirectAdmin(4)"><i class="material-icons icon">bar_chart</i><span class="text">Reports</span></li>
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
            <i class="material-icons">logout</i><span class="text">Logout</span>
        </button>
    </div>
</div>

<!-- MAIN CONTENT -->
<main class="main-content">

    <!-- Top Bar -->
    <div class="top-bar">
        <div class="greeting">
            <h1>Exercise Manager</h1>
            <p>Exercise library, usage coverage and classification health.</p>
        </div>
        <div class="toggle-thumb"><i class="material-icons">brightness_5</i></div>
            </label>
        </div>
    </div>

    <!-- Hero -->
    <section class="admin-hero">
        <div>
            <span class="section-kicker">Exercise Library</span>
            <h2>Manage exercises from your database</h2>
            <p>Review exercise types, muscles, difficulty levels, steps, tags and available equipment.</p>
        </div>
        <div class="hero-icon"><i class="material-icons">fitness_center</i></div>
    </section>

    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon"><i class="material-icons">inventory_2</i></div>
            <div><div class="stat-value">---</div><div class="stat-label">Total Exercises</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="material-icons">home</i></div>
            <div><div class="stat-value">----</div><div class="stat-label">Home Friendly</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="material-icons">local_fire_department</i></div>
            <div><div class="stat-value">----</div><div class="stat-label">Advanced</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="material-icons">category</i></div>
            <div><div class="stat-value">----</div><div class="stat-label">Muscle Groups</div></div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid-main">
        <section class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Exercises by Muscle</div>
                    <div class="card-subtitle">Library coverage grouped by primary muscle.</div>
                </div>
            </div>
            <div class="chart-wrap"><canvas id="muscleChart"></canvas></div>
        </section>
        <section class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Difficulty Split</div>
                    <div class="card-subtitle">How hard the current library trends.</div>
                </div>
            </div>
            <div class="chart-wrap"><canvas id="difficultyChart"></canvas></div>
        </section>
    </div>

    <!-- Exercise Directory Table -->
    <section class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Exercise Directory</div>
                <div class="card-subtitle"><?= count($exercises) ?> exercises loaded from tbl_exercises.</div>
            </div>
        </div>

        <!-- Toolbar (matches AdminUsers toolbar-row style) -->
        <div class="toolbar-row">
            <div class="toolbar-left">
                <div class="field">
                    <label>Search</label>
                    <input type="text" id="exSearch" class="ex-search-input" placeholder="Search name, muscle, equipment..."
                        oninput="filterTable()">
                </div>
                <div class="field">
                    <label>Muscle</label>
                    <select id="exMuscleFilter" class="ex-filter-select" onchange="filterTable()">
                         <option value="">All Muscles</option>
                        <?php foreach($muscles as $m): ?>
                        <option value="<?= $m['muscle'] ?>"><?= $m['muscle'] ?></option>
                        <?php endforeach;?>
                    </select>
                </div>
                <div class="field">
                    <label>Difficulty</label>
                    <select id="exDiffFilter" class="ex-filter-select" onchange="filterTable()">
                        <option value="">All Levels</option>
                        <?php foreach($difficulty as $d):?>
                        <option value="<?= $d['difficulty'] ?>"><?= $d['difficulty'] ?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="toolbar-right">
                <button class="addbt" onclick="openAddExModal()">
                    <i class="material-icons add-icon">add</i> Add Exercise
                </button>
            </div>
        </div>

        <!-- Table (same class pattern as AdminUsers) -->
        <div class="table-wrap">
            <table class="display ex-table" id="exerciseTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Exercise</th>
                        <th>Muscle</th>
                        <th>Type</th>
                        <th>Difficulty</th>
                        <th>Location</th>
                        <th>Steps</th>
                        <th>Tags</th>
                        <th>Created</th>
                        <th class="actions-col">Actions</th>
                    </tr>
                </thead>
                <tbody id="exerciseTableBody">
                <?php foreach ($exercises as $ex): 
                    $tagArr = !empty($ex['tags']) ? explode(',', (string)$ex['tags']) : [];
                    $exJson = htmlspecialchars(json_encode($ex), ENT_QUOTES, 'UTF-8');
                ?>
                <tr data-name="<?= $ex['name'] ?>"
                    data-muscle="<?= $ex['muscle'] ?? ''?>"
                    data-diff="<?=$ex['difficulty'] ?? ''?>"
                    data-equip="<?= $ex['equipment'] ?? '' ?>"
                    data-locat="<?= $ex['location'] ?? '' ?>">
                    <td class="col-id-muted"><?= (int)$ex['exerciseID'] ?></td>
                    <td>
                        <div class="entity-cell">
                            <div class="entity-icon">
                                <?php if (!empty($ex['icon'])): ?>
                                    <img src="<?= htmlspecialchars((string)$ex['icon']) ?>" alt="">
                                <?php else: ?>
                                    <i class="material-icons">fitness_center</i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="entity-name"><?= htmlspecialchars((string)$ex['name']) ?></div>
                                <div class="entity-sub"><?= htmlspecialchars((string)($ex['equipment'] ?: 'No equipment')) ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge"><?= $ex['muscle'] ?: '-' ?></span>
                    </td>
                    <td>
                        <span class="badge type"><?= $ex['type_name'] ?: '-' ?></span>
                    </td>
                    <td>
                        <span class="badge diff"><?= $ex['difficulty'] ?: '-' ?></span>
                    </td>
                    <td class="col-muted"><?= htmlspecialchars((string)($ex['location'] ?: '-')) ?></td>
                    <td>
                        <span class="badge"><?= (int)$ex['step_count'] ?> steps</span>
                    </td>
                    <td>
                        <div class="tag-list">
                            <?php foreach ($tagArr as $tag): ?>
                            <span class="badge gray"><?= htmlspecialchars((string)$tag) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </td>
                    <td class="col-date-muted"><?= htmlspecialchars((string)substr((string)$ex['created_At'], 0, 10)) ?></td>
                    <td class="actions-cell">
                        <div class="action-btns">
                            <button class="icon-btn blue" title="Edit"
                                onclick='openEditExModal(<?= $exJson ?>)'>
                                <i class="material-icons">edit</i>
                            </button>
                            <button class="icon-btn red" title="Delete"
                                onclick="deleteExercise(<?= (int)$ex['exerciseID'] ?>, '<?= htmlspecialchars(addslashes((string)$ex['name'])) ?>')">
                                <i class="material-icons">delete</i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

</main>


<footer>
    <div class="footer-container">
        <div class="footer-brand">&#128170;<span class="brand-gym">Gym</span>Tracker</div>
    </div>
    <div class="footer-copyright">&copy; 2026 Gym Tracker. Built with &hearts; for fitness lovers by Niko</div>
</footer>


<!-- ADD EXERCISE MODAL -->
<div class="modal" id="AddExModal">
    <div class="modal-content">
        <button class="modal-close-btn" type="button" aria-label="Close" onclick="closeModal('AddExModal')">
            <i class="material-icons">close</i>
        </button>
        <div class="modal-head">
            <div class="modal-icon"><i class="material-icons">library_add</i></div>
            <div>
                <h1 class="add-title">Add New Exercise</h1>
                <p>Create a new exercise for your library, including steps and tags.</p>
            </div>
        </div>

        <form class="add-form">
            <div class="form-row-2">
            <div class="input-group">
                <label>Exercise Name *</label>
                <input type="text" id="add_name" placeholder="e.g. Bench Press" maxlength="100">
            </div>
            <div class="input-group">
                <label>Icon / Image Path</label>
                <input type="text" id="add_icon" placeholder="assets/images/chest.png">
                <input type="file" id="add_icon_file"  class ="FileICON"accept="image/*">
            </div>
        </div>

        <div class="form-row-2">
            <div class="input-group">
                <label>Muscle Group *</label>
                <select id="add_muscle" class="browser-default">
                     <option value="">All Muscles</option>
                        <?php foreach($muscles as $m): ?>
                        <option value="<?= $m['muscle'] ?>"><?= $m['muscle'] ?></option>
                        <?php endforeach;?>
                </select>
            </div>
            <div class="input-group">
                <label>Exercise Type *</label>
                <select id="add_type" class="browser-default">
                    <?php foreach ($types as $t): ?>
                    <option value="<?= (int)$t['typeID'] ?>"><?= htmlspecialchars((string)$t['description']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row-2">
            <div class="input-group">
                <label>Difficulty *</label>
                <select id="add_difficulty" class="browser-default">
                    <?php foreach ($difficulty as $d): ?>
                    <option value="<?= $d['exerciselevelID'] ?>"><?= $d['difficulty'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-group">
                <label>Location</label>
                <select id="add_location" class="browser-default">
                    <?php foreach($location as $l): ?>
                    <option value="<?= $l['LocationID'] ?>"><?= $l['Location'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="input-group">
            <label>Equipment</label>
            <input type="text" id="add_equipment" placeholder="e.g. Barbell, Dumbbells, None">
        </div>

        <div class="input-group">
            <label>Description</label>
            <textarea id="add_desc" class="exercise-desc-textarea" rows="3" placeholder="Brief description of the exercise..."></textarea>
        </div>

        <!-- Steps (tbl_exercise_step) -->
        <div class="input-group">
            <label>How to Perform &mdash; Steps *</label>
            <div class="steps-list" id="addStepsList"></div>
            <button type="button" class="add-step-btn" onclick="addStep('addStepsList')">
                <i class="material-icons">add</i> Add Step
            </button>
        </div>

        <!-- Tags (tbl_exercise_tags) -->
        <div class="input-group">
            <label>Tags</label>
            <div class="tag-input-row">
                <input type="text" id="addTagInput" placeholder="e.g. Compound, Push..."
                    onkeydown="if(event.key==='Enter'){event.preventDefault();addTag('add');}">
                <button type="button" class="tag-add-btn" onclick="addTag('add')">Add</button>
            </div>
            <div class="tags-wrap" id="addTagsWrap"></div>
        </div>
        </form>

        <div class="modal-footer-btns">
            <button type="button" class="cancel-btn" onclick="closeModal('AddExModal')">Cancel</button>
            <button type="button" class="register-button" onclick="saveExercise('add')">
                <i class="material-icons save-icon">save</i>
                Add Exercise
            </button>
        </div>

    </div>
</div>

<!-- EDIT EXERCISE MODAL -->
<div class="modal" id="EditExModal">
    <div class="modal-content">
        <button class="modal-close-btn" type="button" aria-label="Close" onclick="closeModal('EditExModal')">
            <i class="material-icons">close</i>
        </button>
        <div class="modal-head">
            <div class="modal-icon"><i class="material-icons">edit_note</i></div>
            <div>
                <h1 class="add-title">Edit Exercise Details</h1>
                <p>Update exercise information, steps, and tags.</p>
            </div>
        </div>

        <form class="add-form">
            <input type="hidden" id="edit_exerciseID">

        <div class="form-row-2">
            <div class="input-group">
                <label>Exercise Name *</label>
                <input type="text" id="edit_name" maxlength="100">
            </div>
            <div class="input-group">
                <label>Icon / Image Path</label>
                <input type="text" id="edit_icon">
                <input type="file" id="edit_icon_file" accept="image/*" style="margin-top:8px;">
            </div>
        </div>

        <div class="form-row-2">
            <div class="input-group">
                <label>Muscle Group *</label>
                <select id="edit_muscle" class="browser-default">
                   <option value="">All Muscles</option>
                        <?php foreach($muscles as $m): ?>
                        <option value="<?= $m['muscle'] ?>"><?= $m['muscle'] ?></option>
                        <?php endforeach;?>
                </select>
            </div>
            <div class="input-group">
                <label>Exercise Type *</label>
                <select id="edit_type" class="browser-default">
                    <?php foreach ($types as $t): ?>
                    <option value="<?= (int)$t['typeID'] ?>"><?= htmlspecialchars((string)$t['description']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row-2">
            <div class="input-group">
                <label>Difficulty *</label>
                <select id="edit_difficulty" class="browser-default">

                   <?php foreach ($difficulty as $d): ?>
                    <option value="<?= $d['exerciselevelID'] ?>"><?= $d['difficulty'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-group">
                <label>Location</label>
                <select id="edit_location" class="browser-default">
                    <?php foreach($location as $l): ?>
                    <option value="<?= $l['LocationID'] ?>"><?= $l['Location'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="input-group">
            <label>Equipment</label>
            <input type="text" id="edit_equipment" placeholder="e.g. Barbell, Dumbbells, None">
        </div>

        <div class="input-group">
            <label>Description</label>
            <textarea id="edit_desc" class="exercise-desc-textarea" rows="3" placeholder="Brief description..."></textarea>
        </div>

        <!-- Steps -->
        <div class="input-group">
            <label>How to Perform &mdash; Steps *</label>
            <div class="steps-list" id="editStepsList"></div>
            <button type="button" class="add-step-btn" onclick="addStep('editStepsList')">
                <i class="material-icons">add</i> Add Step
            </button>
        </div>

        <!-- Tags -->
        <div class="input-group">
            <label>Tags</label>
            <div class="tag-input-row">
                <input type="text" id="editTagInput" placeholder="e.g. Compound, Push..."
                    onkeydown="if(event.key==='Enter'){event.preventDefault();addTag('edit');}">
                <button type="button" class="tag-add-btn" onclick="addTag('edit')">Add</button>
            </div>
            <div class="tags-wrap" id="editTagsWrap"></div>
        </div>
        </form>

        <div class="modal-footer-btns">
            <button type="button" class="cancel-btn" onclick="closeModal('EditExModal')">Cancel</button>
            <button type="button" class="register-button" onclick="saveExercise('edit')">
                <i class="material-icons save-icon">save</i>
                Update Exercise
            </button>
        </div>
    </div>
</div>

<script>
    // Pass the first available Type ID to the JS for defaults
    window.defaultTypeId = "<?= !empty($types) ? (int)$types[0]['typeID'] : '' ?>";
</script>

<!-- Scripts -->
<script src="/workout_trackersys/scripts/redirect.js"></script>
<script src="/workout_trackersys/scripts/magic.js"></script>
<script src="/workout_trackersys/scripts/AdminExercise.js"></script>
</body>
</html>
