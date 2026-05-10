<?php
require_once __DIR__ . '/../../BL/WorkoutManager.php';
require_once __DIR__ . '/../../BL/WorkoutExerciseManager.php';
$wm = new WorkoutManager();
$wem = new WorkoutExerciseManager();
$workoutDetails = $wm->getWorkoutDetails($_GET['id'] ?? 0);
$workoutExercises = $wem->getWorkoutExercisesData($_GET['id'] ?? 0); // url query parameter
$totalcards = $wem->getTotalCard($_GET['id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Workout Details</title>

    <link rel="stylesheet" href="/workout_trackersys/assets/workoutdetails.css">
    <link rel="stylesheet" href="/workout_trackersys/assets/workoutdetailspatch.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

<div class="main-content">

    <!-- BACK BUTTON -->
    <div class="workout-details-top">
        <a onclick="redirectUser(2)" class="btn-flat">
            <i class="material-icons">arrow_back</i> Back
        </a>
    </div>

    <!-- WORKOUT HEADER -->
    <div class="workout-header-card">

        <div class="header-info">
            <h2 id="workoutName"><?= $workoutDetails['name'] ?></h2>
            <p id="workoutDescription">
                <?= $workoutDetails['workout_description'] ?>
            </p>
        </div>

        <div class="header-actions">
            <button class="btn blue" onclick="openAddExerciseModal()">
                + Add Exercise
            </button>

            <button class="btn orange" onclick="EditWorkout(<?= $workoutDetails['workoutID'] ?>)">
                Edit Workout
            </button>

            <button class="btn red" onclick="DeleteWorkout(<?= $workoutDetails['workoutID'] ?>)">
                Delete
            </button>
            <button class="btn green" onclick="startWorkout()">
                <i class="material-icons">play_arrow</i> Start Workout
            </button>
        </div>

    </div>

    <!-- STATS -->
    <div class="workout-stats">

        <div class="stat-card">
            <h5 id="exerciseCount"><?= $totalcards[0]["total_exercises"] ?? 0 ?></h5>
            <span>Exercises</span>
        </div>

        <div class="stat-card">
            <h5 id="totalSets"><?= $totalcards[0]["total_Sets"] ?? 0 ?></h5>
            <span>Total Sets</span>
        </div>

        <div class="stat-card">
            <h5 id="duration"><?= $totalcards[0]["total_minutes"] ?? 0 ?> mins</h5>
            <span>Duration</span>
        </div>

    </div>

    <!-- EXERCISES -->
    <div class="exercise-section">

        <h4>Exercises</h4>

        <div id="exerciseList">

        <?php if(!empty($workoutExercises)):?>
            <?php foreach($workoutExercises as $workex):?>
    <div class="exercise-card" data-workout-exercise-id="<?= $workex['workout_exercises_ID'] ?? '' ?>">
        <div class="exercise-main">
            <h6 class="ex-name"><?= htmlspecialchars($workex['exerciseName']) ?></h6>
            <small class="ex-muscle"><?= htmlspecialchars($workex['muscle']) ?></small>
        </div>

        <div class="exercise-details" 
             data-type="<?= strtolower($workex['exerciseType'] ?? 'strength') ?>"
             data-sets="<?= $workex['sets'] ?? 0 ?>"
             data-reps="<?= $workex['reps'] ?? 0 ?>"
             data-weight="<?= $workex['weight'] ?? 0 ?>"
             data-duration="<?= $workex['durationInMinutes'] ?? 0 ?>"
             data-distance="<?= $workex['distance_km'] ?? 0 ?>"
             data-hold="<?= $workex['holdTimeSec'] ?? 0 ?>"
             data-rest="<?= $workex['rest'] ?? 60 ?>">
            
            <?php 
            $type = strtolower($workex['exerciseType'] ?? 'strength');
            if($type === 'strength'): ?>
                <span>Sets: <?= $workex['sets'] ?></span>
                <span>Reps: <?= $workex['reps'] ?></span>
                <span>Weight: <?= $workex['weight'] ?> kg</span>
            <?php elseif($type === 'cardio'): ?>
                <span>Duration: <?= $workex['durationInMinutes'] ?> min</span>
                <span>Distance: <?= $workex['distance_km'] ?> km</span>
            <?php elseif($type === 'bodyweight'): ?>
                <span>Sets: <?= $workex['sets'] ?></span>
                <span>Reps: <?= $workex['reps'] ?></span>
                <span>Rest: <?= $workex['rest'] ?? 60 ?>s</span>
            <?php elseif($type === 'flexibility'): ?>
                <span>Duration: <?= $workex['durationInMinutes'] ?> min</span>
                <span>Hold: <?= $workex['holdTimeSec'] ?>s</span>
            <?php else: ?>
                <span>Sets: <?= $workex['sets'] ?></span>
                <span>Reps: <?= $workex['reps'] ?></span>
                <span>Weight: <?= $workex['weight'] ?> kg</span>
            <?php endif; ?>
        </div>

        <div class="exercise-actions">
            <button class="btn-flat" onclick="openEditExerciseModal(this, <?= $workex['workout_exercises_ID'] ?>)">
                <i class="material-icons">edit</i>
            </button>
            <button class="btn-flat red-text" onclick="deleteExercise(<?= $workex['workout_exercises_ID'] ?>)">
                <i class="material-icons">delete</i>
            </button>
        </div>
    </div>
<?php endforeach; ?>

        <?php elseif(empty($workoutExercises)):?>
                 <div id="emptyState" class="empty-state">
            No exercises yet. Start building your workout 💪
        </div>
        <?php endif; ?>
        </div>

        <!-- EMPTY STATE -->
       

    </div>
            <!--- mag add pa ako modals for edits, delete and add workout--->
</div>

<div class="editmodal" id="EditWorkoutModal">
    <div class="modal-content">
        <h4>Edit Workout</h4>
        <span class="close-modal" id="closeAddModal" onclick="closeModalEdit()">&times;</span>
        <form id="editWorkoutForm">
            <div class="input-field">
                <input type="text" id="editWorkoutName" value="<?= $workoutDetails['name'] ?>" minlength="3" maxlength="100">
                <label for="editWorkoutName" class="active">Workout Name</label>
            </div>

            <div class="input-field">
                <textarea id="editWorkoutDescription" class="materialize-textarea"><?= $workoutDetails['workout_description'] ?></textarea>
                <label for="editWorkoutDescription" class="active">Description</label>
            </div>

            <button type="button" class="btn blue" onclick="SaveEdit(<?= $workoutDetails['workoutID'] ?>)">
                Save Changes
            </button>
        </form>
    </div>
</div>
<!-- ══ ADD EXERCISE MODAL ══ -->
<div class="editmodal" id="AddExerciseModal">
    <div class="modal-content ae-modal-content">

        <h4>Add Exercise</h4>
        <span class="close-modal" onclick="closeAddExerciseModal()">&times;</span>

        <!-- Tabs -->
        <div class="ae-tabs">
            <button class="ae-tab active" onclick="switchAETab('library', this)">
                📚 From Library
            </button>
            <button class="ae-tab" onclick="switchAETab('manual', this)">
                ✏️ Manual Entry
            </button>
        </div>

        <!-- ── LIBRARY TAB ── -->
        <div class="ae-section" id="ae-library">
            <div class="ae-search-wrap">
                <span class="material-icons ae-search-icon">search</span>
                <input
                    type="text"
                    id="aeSearchInput"
                    class="ae-search-input"
                    placeholder="Search by name or muscle..."
                    oninput="filterAELibrary()"
                >
            </div>

            <div class="ae-filter-row">
                <button class="ae-pill active" onclick="setAEFilter('all', this)">All</button>
                <button class="ae-pill" onclick="setAEFilter('chest', this)">Chest</button>
                <button class="ae-pill" onclick="setAEFilter('back', this)">Back</button>
                <button class="ae-pill" onclick="setAEFilter('shoulders', this)">Shoulders</button>
                <button class="ae-pill" onclick="setAEFilter('legs', this)">Legs</button>
                <button class="ae-pill" onclick="setAEFilter('arms', this)">Arms</button>
                <button class="ae-pill" onclick="setAEFilter('core', this)">Core</button>
                <button class="ae-pill" onclick="setAEFilter('cardio', this)">Cardio</button>
            </div>

            <div class="ae-library-list" id="aeLibraryList"></div>

            <!-- Selected preview -->
            <div class="ae-selected-preview" id="aeSelectedPreview" style="display:none">
                <div class="ae-preview-name"   id="aePreviewName">—</div>
                <div class="ae-preview-muscle" id="aePreviewMuscle">—</div>
            </div>
        </div>

        <!-- ── MANUAL TAB ── -->
        <div class="ae-section" id="ae-manual" style="display:none">
            <div class="ae-manual-grid">
                <div class="ae-manual-field full">
                    <label class="ae-label">Exercise Name</label>
                    <input type="text" id="manualExerciseName" class="ae-input"
                           placeholder="e.g. Incline Dumbbell Press" minlength="3" maxlength="100">
                </div>
                <div class="ae-manual-field">
                    <label class="ae-label">Muscle Group</label>
                    <select id="manualMuscleGroup" class="ae-input browser-default">
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
                <div class="ae-manual-field">
                    <label class="ae-label">Exercise Type</label>
                    <select id="manualExerciseType" class="ae-input browser-default" onchange="onManualTypeChange(this.value)">
                        <option value="strength">Strength</option>
                        <option value="bodyweight">Bodyweight</option>
                        <option value="cardio">Cardio</option>
                        <option value="flexibility">Flexibility</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- ══ SET DETAILS (shared, changes per exercise type) ══ -->
        <div class="ae-srw">
            <div class="ae-srw-header">
                <div class="ae-srw-title">Set Details</div>
                <span class="ae-type-label" id="aeTypeLabel">Strength</span>
            </div>

            <!-- STRENGTH -->
            <div class="ae-type-section" id="strengthFields">
                <div class="ae-fields-grid ae-fields-4">
                    <div class="ae-field-card">
                        <div class="ae-field-icon">🔢</div>
                        <label>Sets</label>
                        <input type="number" id="aeSets" placeholder="3" min="1" max="20">
                    </div>
                    <div class="ae-field-card">
                        <div class="ae-field-icon">🔁</div>
                        <label>Reps</label>
                        <input type="number" id="aeReps" placeholder="10" min="1" max="100">
                    </div>
                    <div class="ae-field-card">
                        <div class="ae-field-icon">⚖️</div>
                        <label>Weight (kg)</label>
                        <input type="number" id="aeWeight" placeholder="0" min="0" max="500">
                    </div>
                    <div class="ae-field-card">
                        <div class="ae-field-icon">⏱️</div>
                        <label>Rest (sec)</label>
                        <input type="number" id="aeSeconds" placeholder="60" min="0" max="600">
                    </div>
                </div>
            </div>

            <!-- CARDIO -->
            <div class="ae-type-section" id="cardioFields" style="display:none">
                <div class="ae-fields-grid ae-fields-2">
                    <div class="ae-field-card">
                        <div class="ae-field-icon">⏱️</div>
                        <label>Duration (min)</label>
                        <input type="number" id="aeDuration" placeholder="30" min="1" max="600">
                    </div>
                    <div class="ae-field-card">
                        <div class="ae-field-icon">📏</div>
                        <label>Distance (km)</label>
                        <input type="number" id="aeDistance" placeholder="5" step="0.1" min="0" max="1000">
                    </div>
                </div>
            </div>

            <!-- BODYWEIGHT -->
            <div class="ae-type-section" id="bodyweightFields" style="display:none">
                <div class="ae-fields-grid ae-fields-3">
                    <div class="ae-field-card">
                        <div class="ae-field-icon">🔢</div>
                        <label>Sets</label>
                        <input type="number" id="aeBWsets" placeholder="3" min="1" max="20">
                    </div>
                    <div class="ae-field-card">
                        <div class="ae-field-icon">🔁</div>
                        <label>Reps</label>
                        <input type="number" id="aeBWreps" placeholder="12" min="1" max="100">
                    </div>
                    <div class="ae-field-card">
                        <div class="ae-field-icon">⏱️</div>
                        <label>Rest (sec)</label>
                        <input type="number" id="aeBWrest" placeholder="60" min="0" max="600">
                    </div>
                </div>
            </div>

            <!-- FLEXIBILITY -->
            <div class="ae-type-section" id="flexibilityFields" style="display:none">
                <div class="ae-fields-grid ae-fields-2">
                    <div class="ae-field-card">
                        <div class="ae-field-icon">⏱️</div>
                        <label>Duration (min)</label>
                        <input type="number" id="aeFlexDuration" placeholder="10" min="1" max="120">
                    </div>
                    <div class="ae-field-card">
                        <div class="ae-field-icon">🧘</div>
                        <label>Hold Time (sec)</label>
                        <input type="number" id="aeFlexHold" placeholder="30" min="1" max="300">
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer" style="margin-top:16px">
            <button class="btn-cancel" onclick="closeAddExerciseModal()">Cancel</button>
            <button class="btn-save" onclick="submitAddExercise()">
                <i class="material-icons" style="font-size:16px;vertical-align:middle">add_circle</i>
                Add Exercise
            </button>
        </div>
    </div>
</div>


<!-- ══ EDIT EXERCISE MODAL ══ -->
<div class="editmodal" id="EditExerciseModal">
    <div class="modal-content" style="max-width:520px">

        <h4>Edit Exercise</h4>
        <span class="close-modal" onclick="closeEditExerciseModal()">&times;</span>

        <!-- Exercise Info -->
        <div class="ee-info-wrap">
            <div>
                <div class="ee-info-name"   id="eeExerciseName">—</div>
                <div class="ee-info-muscle" id="eeExerciseMuscle">—</div>
            </div>
        </div>

        <div class="ae-srw">
    <div class="ae-srw-header">
        <div class="ae-srw-title">Update Exercise Details</div>
    </div>

    <!-- =========================
         STRENGTH
    ========================== -->
    <div id="eeStrengthFields" class="ae-fields-grid ae-fields-4">
        <div class="ae-field-card">
            <div class="ae-field-icon">🔢</div>
            <label>Sets</label>
            <input type="number" id="eeSets" placeholder="3" min="1" max="20">
        </div>

        <div class="ae-field-card">
            <div class="ae-field-icon">🔁</div>
            <label>Reps</label>
            <input type="number" id="eeReps" placeholder="10" min="1" max="100">
        </div>

        <div class="ae-field-card">
            <div class="ae-field-icon">⚖️</div>
            <label>Weight (kg)</label>
            <input type="number" id="eeWeight" placeholder="0" min="0" max="500">
        </div>

        <div class="ae-field-card">
            <div class="ae-field-icon">⏱️</div>
            <label>Rest (sec)</label>
            <input type="number" id="eeRest" placeholder="60" min="0" max="600">
        </div>
    </div>

    <!-- =========================
         CARDIO
    ========================== -->
    <div id="eeCardioFields" class="ae-fields-grid ae-fields-2" style="display:none;">

        <div class="ae-field-card">
            <div class="ae-field-icon">⏱️</div>
            <label>Duration (min)</label>
            <input type="number" id="eeDuration" placeholder="30" min="1">
        </div>

        <div class="ae-field-card">
            <div class="ae-field-icon">📏</div>
            <label>Distance (km)</label>
            <input type="number" id="eeDistance" placeholder="5">
        </div>

    </div>

    <!-- =========================
         BODYWEIGHT
    ========================== -->
    <div id="eeBodyweightFields" class="ae-fields-grid ae-fields-3" style="display:none;">

        <div class="ae-field-card">
            <div class="ae-field-icon">🔢</div>
            <label>Sets</label>
            <input type="number" id="eeBWsets" placeholder="3">
        </div>

        <div class="ae-field-card">
            <div class="ae-field-icon">🔁</div>
            <label>Reps</label>
            <input type="number" id="eeBWreps" placeholder="15">
        </div>

        <div class="ae-field-card">
            <div class="ae-field-icon">⏱️</div>
            <label>Rest (sec)</label>
            <input type="number" id="eeBWrest" placeholder="45">
        </div>

    </div>

    <!-- =========================
         FLEXIBILITY
    ========================== -->
    <div id="eeFlexibilityFields" class="ae-fields-grid ae-fields-2" style="display:none;">

        <div class="ae-field-card">
            <div class="ae-field-icon">⏱️</div>
            <label>Duration (min)</label>
            <input type="number" id="eeFlexDuration" placeholder="10">
        </div>

        <div class="ae-field-card">
            <div class="ae-field-icon">🧘</div>
            <label>Hold (sec)</label>
            <input type="number" id="eeHold" placeholder="30">
        </div>

    </div>
</div>

        <div class="modal-footer" style="margin-top:16px">
            <button class="btn-cancel" onclick="closeEditExerciseModal()">Cancel</button>
            <button class="btn-save" onclick="saveEditExercise()">
                <i class="material-icons" style="font-size:16px;vertical-align:middle">save</i>
                Save Changes
            </button>
        </div>
    </div>
</div>


<!-- ══ START WORKOUT MODAL ══ -->
<div class="editmodal" id="StartWorkoutModal">
    <div class="modal-content" style="max-width:660px">

        <!-- Header -->
        <div class="sw-header">
            <div>
                <h4 id="swWorkoutTitle">Workout</h4>
                <span class="sw-status-badge">🟢 In Progress</span>
            </div>
            <div class="sw-timer-wrap">
                <span class="material-icons" style="color:var(--accent);font-size:22px">timer</span>
                <span class="sw-timer" id="swTimer">00:00</span>
            </div>
        </div>

        <!-- Controls -->
        <div class="sw-controls">
            <button class="sw-btn pause" id="swPauseBtn" onclick="togglePause()">
                <span class="material-icons">pause</span> Pause
            </button>
            <button class="sw-btn finish" onclick="finishWorkout()">
                <span class="material-icons">check_circle</span> Finish
            </button>
            <button class="sw-btn quit" onclick="quitWorkout()">
                <span class="material-icons">cancel</span> Quit
            </button>
        </div>

        <!-- Progress -->
        <div class="sw-progress-wrap">
            <div class="sw-progress-label">
                <span><span id="swDoneCount">0</span> / <span id="swTotalSets">0</span> sets done</span>
                <span id="swProgressPct">0%</span>
            </div>
            <div class="sw-progress-bg">
                <div class="sw-progress-fill" id="swProgressFill" style="width:0%"></div>
            </div>
        </div>

        <!-- Rest Timer Banner (hidden by default) -->
        <div class="sw-rest-banner" id="swRestBanner" style="display:none">
            <span class="material-icons">hourglass_bottom</span>
            <span>Rest: <strong id="swRestCountdown">60</strong>s</span>
            <button class="sw-rest-skip" onclick="skipRest()">Skip →</button>
        </div>

        <!-- Checklist -->
        <div class="sw-checklist" id="swChecklist"></div>
        <div class="sw-empty" id="swEmpty" style="display:none">
            No exercises added yet. Add some first 💪
        </div>

    </div>
</div>
    </div>
</div>

</body>
 <script src="/workout_trackersys/scripts/redirect.js"></script>
 <script src="/workout_trackersys/scripts/workoutdetails.js"></script>
</html>