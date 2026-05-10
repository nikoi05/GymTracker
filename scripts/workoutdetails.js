let exerciseData = [];

//fetch data
async function getExerciseData(){
   try{
            const response = await fetch('api/get_exercise_data.php');
            if(!response.ok)throw new Error('Network Response unable to fetch exercise data');
             const data = await response.json();
            exerciseData =data.flat();
            return exerciseData;
        }catch(err){
            console.error('Error fetching exercise data:', err);
        }
}

async function initExerciseData(){
    await getExerciseData();
    console.log(exerciseData);
}
initExerciseData();

// get currentWorkouts
const params = new URLSearchParams(window.location.search);
const workoutID = params.get('id' ?? 0);
console.log(workoutID);

/* ============================================================
   ADD EXERCISE MODAL
============================================================ */
let aeActiveFilter    = 'all';
let aeSelectedLibrary = null;
let aeActiveTab       = 'library';

/* Open */
function openAddExerciseModal() {
    resetAEModal();
    document.getElementById('AddExerciseModal').classList.add('active');
    filterAELibrary();
}

/* Close */
function closeAddExerciseModal() {
    document.getElementById('AddExerciseModal').classList.remove('active');
    resetAEModal();
}

/* Reset */
function resetAEModal() {
    aeSelectedLibrary = null;
    aeActiveFilter    = 'all';
    aeActiveTab       = 'library';

    document.getElementById('aeSearchInput').value = '';

    // strength
    document.getElementById('aeSets').value    = '';
    document.getElementById('aeReps').value    = '';
    document.getElementById('aeWeight').value  = '';
    document.getElementById('aeSeconds').value = '';

    // cardio
    const aeDurationEl = document.getElementById('aeDuration');
    if (aeDurationEl) aeDurationEl.value = '';
    const aeDistanceEl = document.getElementById('aeDistance');
    if (aeDistanceEl) aeDistanceEl.value = '';

    // bodyweight
    const aeBWsetsEl = document.getElementById('aeBWsets');
    if (aeBWsetsEl) aeBWsetsEl.value = '';
    const aeBWrepsEl = document.getElementById('aeBWreps');
    if (aeBWrepsEl) aeBWrepsEl.value = '';
    const aeBWrestEl = document.getElementById('aeBWrest');
    if (aeBWrestEl) aeBWrestEl.value = '';

    // flexibility
    const aeFlexDurationEl = document.getElementById('aeFlexDuration');
    if (aeFlexDurationEl) aeFlexDurationEl.value = '';
    const aeFlexHoldEl = document.getElementById('aeFlexHold');
    if (aeFlexHoldEl) aeFlexHoldEl.value = '';

    document.getElementById('manualExerciseName').value = '';

    document.getElementById('aeSelectedPreview').style.display = 'none';

    // Reset tab UI
    document.querySelectorAll('.ae-tab').forEach((t, i) => {
        t.classList.toggle('active', i === 0);
    });
    document.getElementById('ae-library').style.display = 'block';
    document.getElementById('ae-manual').style.display  = 'none';

    // show strength by default
    hideAEModalTypeSections();
    showAEModalTypeSection('strength');

    // Reset filter pills
    document.querySelectorAll('.ae-pill').forEach((p, i) => {
        p.classList.toggle('active', i === 0);
    });
}

function hideAEModalTypeSections() {
    document.querySelectorAll('.ae-type-section').forEach(el => {
        el.style.display = 'none';
        el.classList.remove('active');
    });
}

function showAEModalTypeSection(type) {
    hideAEModalTypeSections();

    const map = {
        strength:    'strengthFields',
        cardio:      'cardioFields',
        bodyweight:  'bodyweightFields',
        flexibility: 'flexibilityFields',
    };

    const id = map[type] || 'strengthFields';
    const el = document.getElementById(id);
    if (el) { el.style.display = 'block'; el.classList.add('active'); }

    const labels = {
        strength:    'Strength',
        cardio:      'Cardio',
        bodyweight:  'Bodyweight',
        flexibility: 'Flexibility',
    };
    const badge = document.getElementById('aeTypeLabel');
    if (badge) badge.textContent = labels[type] || 'Strength';
}

/* Switch Tab */
function switchAETab(tab, btn) {
    aeActiveTab = tab;
    document.querySelectorAll('.ae-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('ae-library').style.display = tab === 'library' ? 'block' : 'none';
    document.getElementById('ae-manual').style.display  = tab === 'manual'  ? 'block' : 'none';
}

/* Filter Pills */
function setAEFilter(muscle, btn) {
    aeActiveFilter = muscle;
    document.querySelectorAll('.ae-pill').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    filterAELibrary();
}

/* Render Library List */
function filterAELibrary() {
    const q    = document.getElementById('aeSearchInput').value.toLowerCase();
    const list = document.getElementById('aeLibraryList');
    list.innerHTML = '';

    const filtered = exerciseData.filter(e =>
        (aeActiveFilter === 'all' || e.muscle === aeActiveFilter) &&
        (e.name.toLowerCase().includes(q) || e.muscle.toLowerCase().includes(q))
    );

    if (filtered.length === 0) {
        list.innerHTML = `<div class="ae-no-results">No exercises found. Try manual entry.</div>`;
        return;
    }

    filtered.forEach(e => {
        const item = document.createElement('div');
        item.className = 'ae-library-item';
        if (aeSelectedLibrary && aeSelectedLibrary.id === e.id) item.classList.add('selected');
        item.innerHTML = `
            <div class="ae-item-emoji"><img src='${e.icon}'></div>
            <div class="ae-item-info">
                <div class="ae-item-name">${e.name}</div>
                <div class="ae-item-muscle">${e.muscle}</div>
            </div>
            <div class="ae-item-check">✓</div>
        `;
        item.addEventListener('click', () => selectAELibraryItem(e, item));
        list.appendChild(item);
    });
}

currentExerciseID = null;

/* Select from Library */
function selectAELibraryItem(e, el) {
    aeSelectedLibrary = e;
    document.querySelectorAll('.ae-library-item').forEach(i => i.classList.remove('selected'));
    el.classList.add('selected');

    const preview = document.getElementById('aeSelectedPreview');
    document.getElementById('aePreviewName').innerHTML  = `✅ <img src="${e.icon}" class="ae-preview-icon" alt="${e.name}"> ${e.name}`;
    document.getElementById('aePreviewMuscle').textContent = e.muscle;
    preview.style.display = 'flex';
    currentExerciseID = e.exerciseID;

    showAEModalTypeSection(e.type || 'strength');
}

function calcExerciseDuration(sets, reps, rest, repstime = 3) {
    let workTime = sets * reps * repstime;
    let restime  = (sets - 1) * rest;
    return Math.round((workTime + restime) / 60);
}

function submitAddExercise() {
    if (!workoutID) {
        GymSwal.fire({ icon: 'error', title: 'Missing workout id' });
        return;
    }

    // =========================
    // LIBRARY MODE
    // =========================
    if (aeActiveTab === 'library') {

        if (!aeSelectedLibrary) {
            GymSwal.fire({ icon: 'warning', title: 'Pick an exercise from the library first!' });
            return;
        }

        const ExerciseID   = currentExerciseID;
        const exerciseType = aeSelectedLibrary.type || 'strength';

        const dataToSend = {
            action:     'add_exercise_to_workout',
            workoutId:  workoutID,
            exerciseId: ExerciseID
        };

        switch (exerciseType) {

            case 'strength': {
                const sets   = document.getElementById('aeSets').value.trim();
                const reps   = document.getElementById('aeReps').value.trim();
                const weight = document.getElementById('aeWeight').value.trim() || 0;
                const rest   = document.getElementById('aeSeconds').value.trim();
                const typeID = 1;

                if (!sets || !reps) {
                    return GymSwal.fire({ icon: 'warning', title: 'Please fill Sets and Reps.' });
                }

                const durationTime = calcExerciseDuration(parseInt(sets), parseInt(reps), parseInt(rest || 0));
                Object.assign(dataToSend, { typeID, sets, reps, weight, rest, duration: durationTime });
                break;
            }

            case 'cardio': {
                const duration = document.getElementById('aeDuration').value.trim();
                const distance = document.getElementById('aeDistance').value.trim();
                const typeID   = 2;

                if (!duration || !distance) {
                    return GymSwal.fire({ icon: 'warning', title: 'Please fill Duration and Distance.' });
                }

                Object.assign(dataToSend, { typeID, duration, distance });
                break;
            }

            case 'bodyweight': {
                const sets   = document.getElementById('aeBWsets').value.trim();
                const reps   = document.getElementById('aeBWreps').value.trim();
                const rest   = document.getElementById('aeBWrest').value.trim();
                const typeID = 3;

                if (!sets || !reps) {
                    return GymSwal.fire({ icon: 'warning', title: 'Please fill Sets and Reps.' });
                }

                const durationTime = calcExerciseDuration(parseInt(sets), parseInt(reps), parseInt(rest || 0));
                Object.assign(dataToSend, { typeID, sets, reps, rest, duration: durationTime, weight: 0 });
                break;
            }

            case 'flexibility': {
                const duration = document.getElementById('aeFlexDuration').value.trim();
                const hold     = document.getElementById('aeFlexHold').value.trim();
                const typeID   = 4;

                if (!duration || !hold) {
                    return GymSwal.fire({ icon: 'warning', title: 'Please fill Duration and Hold.' });
                }

                Object.assign(dataToSend, { typeID, duration, hold, sets: 1, reps: 1, weight: 0, rest: 0 });
                break;
            }

            default:
                return GymSwal.fire({ icon: 'error', title: 'Unknown exercise type' });
        }

        $.ajax({
            url:  '/workout_trackersys/controllers/WorkoutExercise.php',
            type: 'POST',
            data: dataToSend,
            success: function(response) {
                if (response.trim() === 'success') {
                    const name = aeSelectedLibrary.name;
                    closeAddExerciseModal();
                    GymSwal.fire({ icon: 'success', title: `${name} added!`, showConfirmButton: false });
                    setTimeout(() => { location.reload(); }, 2000);
                } else {
                    GymSwal.fire({ icon: 'error', title: 'Failed to add exercise.', text: response });
                }
            },
            error: function(xhr) {
                console.error('AJAX error:', xhr);
                GymSwal.fire({ icon: 'error', title: 'Server error. Please try again.' });
            }
        });

        return;
    }

    // =========================
    // MANUAL MODE
    // =========================
    const exerciseName       = document.getElementById('manualExerciseName')?.value.trim();
    const muscleGroup        = document.getElementById('manualMuscleGroup')?.value || '';
    const ManualexerciseType = document.getElementById('manualExerciseType')?.value || 'strength';

    const dataToSendCustom = {
        action:    'add_exercise_to_workout',
        workoutId: workoutID,
        name:      exerciseName,
        muscle:    muscleGroup,
    };

    switch (ManualexerciseType) {

        case 'strength': {
            const sets   = document.getElementById('aeSets').value.trim();
            const reps   = document.getElementById('aeReps').value.trim();
            const weight = document.getElementById('aeWeight').value.trim() || 0;
            const rest   = document.getElementById('aeSeconds').value.trim();
            const typeID = 1;

            if (!sets || !reps) {
                return GymSwal.fire({ icon: 'warning', title: 'Please fill Sets and Reps.' });
            }

            const durationTime = calcExerciseDuration(parseInt(sets), parseInt(reps), parseInt(rest || 0));
            Object.assign(dataToSendCustom, { typeID, sets, reps, weight, rest, duration: durationTime });
            break;
        }

        case 'cardio': {
            const duration = document.getElementById('aeDuration').value.trim();
            const distance = document.getElementById('aeDistance').value.trim();
            const typeID   = 2;

            if (!duration || !distance) {
                return GymSwal.fire({ icon: 'warning', title: 'Please fill Duration and Distance.' });
            }

            Object.assign(dataToSendCustom, { typeID, duration, distance });
            break;
        }

        case 'bodyweight': {
            const sets   = document.getElementById('aeBWsets').value.trim();
            const reps   = document.getElementById('aeBWreps').value.trim();
            const rest   = document.getElementById('aeBWrest').value.trim();
            const typeID = 3;

            if (!sets || !reps) {
                return GymSwal.fire({ icon: 'warning', title: 'Please fill Sets and Reps.' });
            }

            const durationTime = calcExerciseDuration(parseInt(sets), parseInt(reps), parseInt(rest || 0));
            Object.assign(dataToSendCustom, { typeID, sets, reps, rest, duration: durationTime, weight: 0 });
            break;
        }

        case 'flexibility': {
            const duration = document.getElementById('aeFlexDuration').value.trim();
            const hold     = document.getElementById('aeFlexHold').value.trim();
            const typeID   = 4;

            if (!duration || !hold) {
                return GymSwal.fire({ icon: 'warning', title: 'Please fill Duration and Hold.' });
            }

            Object.assign(dataToSendCustom, { typeID, duration, hold, sets: 1, reps: 1, weight: 0, rest: 0 });
            break;
        }

        default:
            return GymSwal.fire({ icon: 'error', title: 'Unknown exercise type' });
    }

    if (!exerciseName) {
        GymSwal.fire({ icon: 'warning', title: 'Please enter an exercise name.' });
        return;
    }

    console.log(dataToSendCustom);
    $.ajax({
        url:  '/workout_trackersys/controllers/WorkoutExercise.php',
        type: 'POST',
        data: dataToSendCustom,
        success: function(response) {
            if (response.trim() === 'success') {
                closeAddExerciseModal();
                GymSwal.fire({ icon: 'success', title: 'Exercise added!', showConfirmButton: false });
                setTimeout(() => { location.reload(); }, 2000);
            } else {
                GymSwal.fire({ icon: 'error', title: 'Failed to add exercise.', text: response });
            }
        },
        error: function(xhr) {
            console.error('AJAX error:', xhr);
            GymSwal.fire({ icon: 'error', title: 'Server error. Please try again.' });
        }
    });
}

/* Called when manual type dropdown changes */
function onManualTypeChange(type) {
    showAEModalTypeSection(type);
}

/* ============================================================
   START WORKOUT MODAL
============================================================ */
let swTimer       = null;
let swSeconds     = 0;
let swPaused      = false;
let swTotalSets   = 0;
let swDoneSets    = 0;
let swExercises   = [];
let swRestTimer   = null;
let swRestSeconds = 0;

function startWorkout() {
    swExercises = [];
    document.querySelectorAll('.exercise-card').forEach((card, idx) => {
        const name    = card.querySelector('.ex-name')?.textContent.trim() || 'Exercise';
        const muscle  = card.querySelector('.ex-muscle')?.textContent.trim() || '';
        const details = card.querySelector('.exercise-details');

        const type     = details?.dataset.type     || 'strength';
        const weight   = parseFloat(details?.dataset.weight) || 0;
        const rest     = parseInt(details?.dataset.rest)     || 60;
        const duration = details?.dataset.duration || '';
        const distance = details?.dataset.distance || '';
        const hold     = details?.dataset.hold     || '';
        const reps     = details?.dataset.reps     || '10';

        // Cardio & flexibility don't use sets — force 1 round so progress works
        let sets;
        if (type === 'cardio' || type === 'flexibility') {
            sets = 1;
        } else {
            sets = parseInt(details?.dataset.sets) || 1;
        }

        swExercises.push({ id: idx, name, muscle, type, sets, reps, weight, rest, duration, distance, hold, doneSets: 0 });
    });

    if (swExercises.length === 0) {
        GymSwal.fire({ icon: 'warning', title: 'No exercises yet!', text: 'Add some exercises before starting.' });
        return;
    }

    swSeconds   = 0;
    swPaused    = false;
    swDoneSets  = 0;
    swTotalSets = swExercises.reduce((sum, e) => sum + e.sets, 0);
    skipRest();

    const workoutTitle = document.getElementById('workoutName');
    if (workoutTitle) document.getElementById('swWorkoutTitle').textContent = workoutTitle.textContent || 'Workout';

    renderSWChecklist();
    updateSWProgress();
    updateSWProgressPct();
    updateSWTimer();

    clearInterval(swTimer);
    swTimer = setInterval(() => {
        if (!swPaused) { swSeconds++; updateSWTimer(); }
    }, 1000);

    document.getElementById('StartWorkoutModal').classList.add('active');

    const pauseBtn = document.getElementById('swPauseBtn');
    if (pauseBtn) {
        pauseBtn.innerHTML        = '<span class="material-icons">pause</span> Pause';
        pauseBtn.style.background = 'rgba(245,158,11,0.12)';
        pauseBtn.style.color      = '#F59E0B';
    }
}

function renderSWChecklist() {
    const list  = document.getElementById('swChecklist');
    const empty = document.getElementById('swEmpty');

    if (swExercises.length === 0) {
        if (list)  list.style.display  = 'none';
        if (empty) empty.style.display = 'block';
        return;
    }

    if (list)  list.style.display  = 'flex';
    if (empty) empty.style.display = 'none';
    if (list)  list.innerHTML = '';

    swExercises.forEach(ex => {
        const allDone = ex.doneSets >= ex.sets;
        const block   = document.createElement('div');
        block.className = `sw-exercise-block${allDone ? ' all-done' : ''}`;
        block.id = `sw-block-${ex.id}`;

        const typeBadgeColor = {
            strength:    '#38BDF8',
            cardio:      '#F59E0B',
            bodyweight:  '#22C55E',
            flexibility: '#A78BFA'
        }[ex.type] || '#38BDF8';

        // Cardio & flexibility use "Round", strength & bodyweight use "Set"
        const rowLabel = (ex.type === 'cardio' || ex.type === 'flexibility') ? 'Round' : 'Set';

        let setsHTML = '';
        for (let s = 1; s <= ex.sets; s++) {
            const done    = s <= ex.doneSets;
            const isLast  = s === ex.sets;
            const restSec = parseInt(ex.rest) || 60;

            let infoText = '';
            if (ex.type === 'cardio') {
                infoText = `${ex.duration} min${ex.distance ? ` · ${ex.distance} km` : ''}`;
            } else if (ex.type === 'flexibility') {
                infoText = `${ex.duration} min · Hold ${ex.hold}s`;
            } else if (ex.type === 'bodyweight') {
                infoText = `${ex.reps} reps <span style="font-size:11px;color:var(--text-muted)">(bodyweight)</span>`;
            } else {
                infoText = `${ex.reps} reps${ex.weight > 0
                    ? ` <span style="font-size:11px;color:var(--text-muted)">@ ${ex.weight}kg</span>`
                    : ' <span style="font-size:11px;color:var(--text-muted)">(bodyweight)</span>'}`;
            }

            setsHTML += `
                <div class="sw-set-row${done ? ' done' : ''}" id="sw-set-${ex.id}-${s}">
                    <div class="sw-set-num">${rowLabel} ${s}</div>
                    <div class="sw-set-info${done ? ' done-text' : ''}">
                        ${infoText}
                        ${!isLast && !done && (ex.type === 'strength' || ex.type === 'bodyweight')
                            ? `<span style="font-size:10px;color:var(--warning);margin-left:6px">Rest ${restSec}s after</span>`
                            : ''}
                    </div>
                    <div class="sw-set-check${done ? ' checked' : ''}"
                         onclick="toggleSet(${ex.id}, ${s})"
                         title="${done ? 'Mark undone' : 'Mark done'}">✓</div>
                </div>`;
        }

        block.innerHTML = `
            <div class="sw-exercise-header">
                <div>
                    <div class="sw-exercise-name">
                        ${escapeHtml(ex.name)}
                        <span style="font-size:10px;padding:2px 8px;border-radius:20px;background:${typeBadgeColor}22;color:${typeBadgeColor};margin-left:6px;text-transform:capitalize">${ex.type}</span>
                    </div>
                    <div class="sw-exercise-muscle">
                        ${escapeHtml(ex.muscle)}
                        ${ex.rest && (ex.type === 'strength' || ex.type === 'bodyweight') ? ` · ${ex.rest}s rest` : ''}
                    </div>
                </div>
                <span class="sw-sets-done" id="sw-sets-label-${ex.id}">${ex.doneSets}/${ex.sets} ${rowLabel.toLowerCase()}s</span>
            </div>
            <div class="sw-sets-list">${setsHTML}</div>`;

        if (list) list.appendChild(block);
    });

    const totalEl = document.getElementById('swTotalSets');
    if (totalEl) totalEl.textContent = swTotalSets;
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[m]));
}

function toggleSet(exId, setNum) {
    const ex = swExercises.find(e => e.id === exId);
    if (!ex) return;

    const row      = document.getElementById(`sw-set-${exId}-${setNum}`);
    const checkBtn = row?.querySelector('.sw-set-check');
    const infoEl   = row?.querySelector('.sw-set-info');
    const isDone   = checkBtn?.classList.contains('checked');

    if (isDone) {
        checkBtn?.classList.remove('checked');
        row?.classList.remove('done');
        infoEl?.classList.remove('done-text');
        ex.doneSets = Math.max(0, ex.doneSets - 1);
        swDoneSets  = Math.max(0, swDoneSets - 1);
    } else {
        checkBtn?.classList.add('checked');
        row?.classList.add('done');
        infoEl?.classList.add('done-text');
        ex.doneSets++;
        swDoneSets++;

        // Only trigger rest timer for strength & bodyweight
        if (ex.doneSets < ex.sets && (ex.type === 'strength' || ex.type === 'bodyweight')) {
            startRestTimer(parseInt(ex.rest) || 60);
        }
    }

    const setsLabel = document.getElementById(`sw-sets-label-${exId}`);
    const rowLabel  = (ex.type === 'cardio' || ex.type === 'flexibility') ? 'round' : 'set';
    if (setsLabel) setsLabel.textContent = `${ex.doneSets}/${ex.sets} ${rowLabel}s`;

    const block = document.getElementById(`sw-block-${exId}`);
    if (block) block.classList.toggle('all-done', ex.doneSets >= ex.sets);

    updateSWProgress();
    updateSWProgressPct();

    if (swDoneSets >= swTotalSets) {
        clearInterval(swTimer);
        setTimeout(() => {
            GymSwal.fire({
                icon:              'success',
                title:             '🎉 Workout Complete!',
                text:              `Great job! You finished in ${formatTime(swSeconds)}.`,
                confirmButtonText: 'Finish & Save'
            }).then(r => { if (r.isConfirmed) saveWorkoutCompletion(); });
        }, 400);
    }
}

function updateSWProgress() {
    const pct = swTotalSets > 0 ? Math.round((swDoneSets / swTotalSets) * 100) : 0;
    const fill = document.getElementById('swProgressFill');
    if (fill) fill.style.width = pct + '%';
    const doneCount = document.getElementById('swDoneCount');
    if (doneCount) doneCount.textContent = swDoneSets;
    const totalSets = document.getElementById('swTotalSets');
    if (totalSets) totalSets.textContent = swTotalSets;
}

function updateSWProgressPct() {
    const pct   = swTotalSets > 0 ? Math.round((swDoneSets / swTotalSets) * 100) : 0;
    const pctEl = document.getElementById('swProgressPct');
    if (pctEl) pctEl.textContent = pct + '%';
}

function updateSWTimer() {
    const timerEl = document.getElementById('swTimer');
    if (timerEl) timerEl.textContent = formatTime(swSeconds);
}

function formatTime(secs) {
    const m = Math.floor(secs / 60).toString().padStart(2, '0');
    const s = (secs % 60).toString().padStart(2, '0');
    return `${m}:${s}`;
}

function togglePause() {
    swPaused = !swPaused;
    const btn = document.getElementById('swPauseBtn');
    if (swPaused) {
        if (btn) {
            btn.innerHTML        = '<span class="material-icons">play_arrow</span> Resume';
            btn.style.background = 'rgba(34,197,94,0.12)';
            btn.style.color      = '#22C55E';
        }
    } else {
        if (btn) {
            btn.innerHTML        = '<span class="material-icons">pause</span> Pause';
            btn.style.background = 'rgba(245,158,11,0.12)';
            btn.style.color      = '#F59E0B';
        }
    }
}

function startRestTimer(seconds) {
    clearInterval(swRestTimer);
    swRestSeconds = seconds;

    const banner    = document.getElementById('swRestBanner');
    const countdown = document.getElementById('swRestCountdown');
    if (!banner || !countdown) return;

    countdown.textContent = swRestSeconds;
    banner.style.display  = 'flex';

    swRestTimer = setInterval(() => {
        swRestSeconds--;
        if (countdown) countdown.textContent = swRestSeconds;
        if (swRestSeconds <= 0) skipRest();
    }, 1000);
}

function skipRest() {
    clearInterval(swRestTimer);
    const banner = document.getElementById('swRestBanner');
    if (banner) banner.style.display = 'none';
    swRestSeconds = 0;
}

function finishWorkout() {
    clearInterval(swTimer);
    GymSwal.fire({
        icon:              'question',
        title:             'Finish Workout?',
        text:              `Time: ${formatTime(swSeconds)} | ${swDoneSets}/${swTotalSets} sets done`,
        showCancelButton:  true,
        confirmButtonText: 'Yes, finish!',
        cancelButtonText:  'Keep going'
    }).then(r => {
        if (r.isConfirmed) saveWorkoutCompletion();
        else swTimer = setInterval(() => { if (!swPaused) { swSeconds++; updateSWTimer(); } }, 1000);
    });
}

function saveWorkoutCompletion() {
    const currentWorkoutId = params.get('id') ?? '0';
    $.ajax({
        url:  '/workout_trackersys/controllers/Workoutcontroller.php',
        type: 'POST',
        data: {
            action:        'finishWorkout',
            workoutID:     currentWorkoutId,
            duration:      swSeconds,
            setsCompleted: swDoneSets
        },
        success: function(response) {
            if (response.trim() === 'success') {
                document.getElementById('StartWorkoutModal').classList.remove('active');
                GymToast.fire({ icon: 'success', title: 'Workout saved! Great job 💪' });
                const badge = document.getElementById('workoutStatus');
                if (badge) { badge.textContent = 'Completed'; badge.className = 'status completed'; }
            }
        },
        error: function(xhr) {
            GymSwal.fire({ icon: 'error', title: 'Error: ' + xhr.status, confirmButtonText: 'OK' });
        }
    });
}

function quitWorkout() {
    GymSwal.fire({
        icon:              'warning',
        title:             'Quit Workout?',
        text:              'Your progress will not be saved.',
        showCancelButton:  true,
        confirmButtonText: 'Yes, quit',
        cancelButtonText:  'Keep going'
    }).then(r => {
        if (r.isConfirmed) {
            clearInterval(swTimer);
            clearInterval(swRestTimer);
            document.getElementById('StartWorkoutModal').classList.remove('active');
        }
    });
}

/* ============================================================
   EDIT EXERCISE MODAL
============================================================ */
let currentEditExerciseId = null;
let currentExerciseCard   = null;

function openEditExerciseModal(button, workoutExerciseId) {
    const card = button.closest('.exercise-card');
    currentExerciseCard   = card;
    currentEditExerciseId = workoutExerciseId || button.dataset.target || null;

    const name   = card.querySelector('.ex-name')?.textContent.trim()   || '';
    const muscle = card.querySelector('.ex-muscle')?.textContent.trim() || '';
    const details = card.querySelector('.exercise-details');

    const sets     = details?.dataset.sets     || '';
    const reps     = details?.dataset.reps     || '';
    const weight   = details?.dataset.weight   || '';
    const duration = details?.dataset.duration || '';
    const distance = details?.dataset.distance || '';
    const hold     = details?.dataset.hold     || '';
    const rest     = details?.dataset.rest     || '';

    const exerciseType = details?.dataset.type || 'strength';

    document.getElementById('EditExerciseModal').dataset.exerciseType = exerciseType;
    document.getElementById('eeExerciseName').textContent   = name;
    document.getElementById('eeExerciseMuscle').textContent = muscle;

    hideEditSections();

    if (exerciseType === 'strength') {
        document.getElementById('eeStrengthFields').style.display = 'grid';
        document.getElementById('eeSets').value   = sets;
        document.getElementById('eeReps').value   = reps;
        document.getElementById('eeWeight').value = weight;
        document.getElementById('eeRest').value   = rest;
    } else if (exerciseType === 'cardio') {
        document.getElementById('eeCardioFields').style.display = 'grid';
        document.getElementById('eeDuration').value = duration;
        document.getElementById('eeDistance').value = distance;
    } else if (exerciseType === 'bodyweight') {
        document.getElementById('eeBodyweightFields').style.display = 'grid';
        document.getElementById('eeBWsets').value = sets;
        document.getElementById('eeBWreps').value = reps;
        document.getElementById('eeBWrest').value = rest;
    } else if (exerciseType === 'flexibility') {
        document.getElementById('eeFlexibilityFields').style.display = 'grid';
        document.getElementById('eeFlexDuration').value = duration;
        document.getElementById('eeHold').value         = hold;
    }

    document.getElementById('EditExerciseModal').classList.add('active');
}

function hideEditSections() {
    ['eeStrengthFields','eeCardioFields','eeBodyweightFields','eeFlexibilityFields'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'none';
    });
}

function closeEditExerciseModal() {
    document.getElementById('EditExerciseModal').classList.remove('active');
    currentEditExerciseId = null;
    currentExerciseCard   = null;
}

function saveEditExercise() {
    const exerciseType = document.getElementById('EditExerciseModal').dataset.exerciseType || 'strength';

    let payload = {
        action:            'updateExercise',
        workoutExerciseId: currentEditExerciseId,
        exerciseType
    };

    if (exerciseType === 'strength') {
        const sets   = document.getElementById('eeSets').value.trim();
        const reps   = document.getElementById('eeReps').value.trim();
        const weight = document.getElementById('eeWeight').value.trim() || 0;
        const rest   = document.getElementById('eeRest').value.trim()   || 0;

        if (!sets || !reps) return GymSwall.fire({ icon: 'warning', title: 'Please fill Sets and Reps.' });
        payload = { ...payload, sets, reps, weight, rest };

    } else if (exerciseType === 'cardio') {
        const duration = document.getElementById('eeDuration').value.trim();
        const distance = document.getElementById('eeDistance').value.trim();

        if (!duration || !distance) return GymSwall.fire({ icon: 'warning', title: 'Please fill Duration and Distance.' });
        payload = { ...payload, duration, distance };

    } else if (exerciseType === 'bodyweight') {
        const sets = document.getElementById('eeBWsets').value.trim();
        const reps = document.getElementById('eeBWreps').value.trim();
        const rest = document.getElementById('eeBWrest').value.trim();

        if (!sets || !reps) return GymSwall.fire({ icon: 'warning', title: 'Please fill Sets and Reps.' });
        payload = { ...payload, sets, reps, rest };

    } else if (exerciseType === 'flexibility') {
        const duration = document.getElementById('eeFlexDuration').value.trim();
        const hold     = document.getElementById('eeHold').value.trim();

        if (!duration || !hold) return GymSwall.fire({ icon: 'warning', title: 'Please fill Duration and Hold.' });
        payload = { ...payload, duration, hold };
    }

    $.ajax({
        url:  '/workout_trackersys/controllers/WorkoutExercise.php',
        type: 'POST',
        data: payload,
        success: function(response) {
            if (response.trim() === 'success') {
                closeEditExerciseModal();
                GymSwall.fire({ icon: 'success', title: 'Exercise updated!' });
                setTimeout(() => { location.reload(); }, 1000);
            } else {
                GymSwall.fire({ icon: 'error', title: 'Failed to update exercise', text: response });
            }
        },
        error: function(xhr) {
            console.error(xhr);
            GymSwall.fire({ icon: 'error', title: 'Server Error' });
        }
    });
}
/* ============================================================
   GYMSWAL & GYMTOAST (shared) - MUST BE DECLARED FIRST
============================================================ */
const GymSwal = Swal.mixin({
    customClass: { container: 'swal-on-top' },
    backdrop: 'rgba(0,0,0,0.5)',
    didOpen: (popup) => {
        popup.style.background     = 'rgba(9,9,121,0.95)';
        popup.style.backdropFilter = 'blur(20px)';
        popup.style.border         = '1px solid rgba(255,255,255,0.2)';
        popup.style.borderRadius   = '20px';
        popup.style.color          = '#ffffff';
        popup.style.fontFamily     = 'Poppins, sans-serif';
        const title = popup.querySelector('.swal2-title');
        if (title) { title.style.color = '#fff'; title.style.fontFamily = 'Poppins, sans-serif'; }
        const text = popup.querySelector('.swal2-html-container');
        if (text) { text.style.color = 'rgba(255,255,255,0.8)'; }
        const confirm = popup.querySelector('.swal2-confirm');
        if (confirm) {
            confirm.style.background   = 'linear-gradient(45deg,#38BDF8,#3B82F6)';
            confirm.style.border       = 'none';
            confirm.style.borderRadius = '50px';
            confirm.style.fontFamily   = 'Poppins,sans-serif';
            confirm.style.fontWeight   = '600';
        }
        const cancel = popup.querySelector('.swal2-cancel');
        if (cancel) {
            cancel.style.background   = 'rgba(255,255,255,0.15)';
            cancel.style.color        = 'white';
            cancel.style.border       = '1px solid rgba(255,255,255,0.2)';
            cancel.style.borderRadius = '50px';
            cancel.style.fontFamily   = 'Poppins,sans-serif';
            cancel.style.fontWeight   = '600';
        }
    }
});
 
const GymToast = Swal.mixin({
    toast:              true,
    position:           'top-end',
    showConfirmButton:  false,
    timer:              3000,
    timerProgressBar:   true,
    customClass: { container: 'swal-on-top' },
    didOpen: (toast) => {
        toast.style.background     = 'rgba(9,9,121,0.95)';
        toast.style.backdropFilter = 'blur(20px)';
        toast.style.border         = '1px solid rgba(255,255,255,0.2)';
        toast.style.borderRadius   = '12px';
        toast.style.color          = '#ffffff';
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

const themeToggle = document.getElementById('themeToggle');
const themeLabel = document.getElementById('theme-label');
const thumb = document.querySelector('.toggle-thumb i');
// APPLY SAVED THEME ON LOAD
window.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme');

    if (savedTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        themeToggle.checked = true;
        themeLabel.textContent = 'Dark';
        thumb.textContent = 'brightness_2';
    } else {
        document.documentElement.setAttribute('data-theme', 'light');
        themeToggle.checked = false;
        themeLabel.textContent = 'Light';
        thumb.textContent = 'brightness_5';
    }
});

// TOGGLE CHANGE EVENT
themeToggle.addEventListener('change', () => {
    if (themeToggle.checked) {
        document.documentElement.setAttribute('data-theme', 'dark');
        themeLabel.textContent = 'Dark';
        thumb.textContent = 'brightness_2';
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.setAttribute('data-theme', 'light');
        themeLabel.textContent = 'Light';
        thumb.textContent = 'brightness_5';
        localStorage.setItem('theme', 'light');
    }
});