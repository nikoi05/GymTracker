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
    /* ── Sidebar Toggle ── */
    function ToggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
    }
        /*- FETCH EXERCISE DATA FROM API -*/
    async function fetchExerciseData(){
        try{
            const response = await fetch('api/get_exercise_data.php');
            if(!response.ok)throw new Error('Network Response unable to fetch exercise data');
             const data = await response.json();
            exerciseData =data.flat(); // Flatten the array if needed  so hindi nested loop makukuha
            return exerciseData;
        }catch(err){
            console.error('Error fetching exercise data:', err);
        }
    }
        /* we use init para makuha natin yun data at para if ever na network problem mahihintay before marun yung filters and stuff*/
     async function init(){
        exerciseData = await fetchExerciseData();
        // in init we will call the functions that requires the data to be loaded para marender yung data from the api
        Runnit();
        filterExercises();

    }

    function Runnit(){
        console.log(exerciseData);
    }

    init();

  let activeFilter = 'all';

function setFilter(muscle, btn) {
    activeFilter = muscle;
    document.querySelectorAll('.pill').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    filterExercises();
}

function filterExercises() {
    const query = (document.getElementById('searchInput').value || '').toLowerCase().trim();
    const grid = document.getElementById('exerciseGrid');
    grid.innerHTML = '';

    const filtered = exerciseData.filter(exercise => {
        if (!exercise) return false;

        const name = (exercise.name || '').toLowerCase();
        const muscle = (exercise.muscle || '').toLowerCase();
        const tags = (exercise.tag || '').toLowerCase().split(',').map(t => t.trim());
        const equipment = (exercise.equipment || '').toLowerCase();
        const location = (exercise.location || '').toLowerCase();
        const difficulty = (exercise.difficulty || '').toLowerCase();
        const type = (exercise.type || '').toLowerCase();

        const matchedFilter = activeFilter === 'all' || muscle === activeFilter;
        const matchedSearch = !query ||
            name.includes(query) ||
            muscle.includes(query) ||
            tags.some(t => t.includes(query)) ||
            equipment.includes(query) ||
            location.includes(query) ||
            difficulty.includes(query) ||
            type.includes(query);



        return matchedFilter && matchedSearch;
    });

    document.getElementById('totalCount').textContent = filtered.length;

    if (filtered.length === 0) {
        grid.innerHTML = `<p class="no-results">No exercises found.</p>`;
        return;
    }

    // FIXED: Use index parameter 'i'
    filtered.forEach((e, i) => {  // ← Added 'i' parameter here
        const card = document.createElement('div');
        card.className = 'exercise-card';
        card.style.animationDelay = (i * 0.04) + 's';  // ← Now 'i' works

        card.innerHTML = `
            <div class="exercise-card-top">
                <div class="exercise-emoji"><img src="${e.icon}" alt="${e.name}"></div>
                <span class="muscle-badge muscle-${e.muscle}">${e.muscle.replace('-', ' ')}</span>
            </div>
            <div class="exercise-name">${e.name}</div>
            <div class="exercise-desc">${e.desc}</div>
            <div class="exercise-meta">
                <span class="meta-tag"><span class="material-icons">fitness_center</span>${e.equipment}</span>
                <span class="meta-tag"><span class="material-icons">location_on</span>${e.location}</span>
                <span class="meta-tag"><span class="material-icons">signal_cellular_alt</span>${e.difficulty}</span>
                <span class="meta-tag"><span class="material-icons">fitness_center</span>${e.type}</span>

            <button class="exercise-card-btn">View Details →</button>
        `;

        // Card click handler
        card.addEventListener('click', (ev) => {
            if (!ev.target.classList.contains('exercise-card-btn')) {
                openModal(e.exerciseID); // use  the id of exercise
            }
        });

        // Button click handler
        const btn = card.querySelector('.exercise-card-btn');
        btn.addEventListener('click', (ev) => {
            ev.stopPropagation();
            openModal(e.exerciseID);
        });

        grid.appendChild(card);
    });
}

let exerciseStepsData = [];

// function to fetch exercise steps from API
async function fetchExerciseSteps(){
    try{
        const response = await fetch('api/get_exercixe_step.php');
        if(!response.ok)throw new Error('Network Response unable to fetch exercise steps');
         const data = await response.json();
        exerciseStepsData = data; // Store in global variable
    }catch(err){
        console.error('Error fetching exercise steps:', err);
    }
}

async function exercisesteps(){
    const stepsData = await fetchExerciseSteps();
        console.log('Exercise Steps Data:', exerciseStepsData);
}
exercisesteps();


// MODAL FUNCTIONS
function openModal(id) {
    id=parseInt(id); // ensure id is an integer
    const e = exerciseData.find(x => x.exerciseID === id);
    if (!e) return console.error('Exercise not found:', id);
    currentID = id ; // get the current id for the add to workout panel
    OpenformByType(e.type);

    document.getElementById('modalEmoji').innerHTML = `<img src="${e.icon}" alt="${e.name}">`;
    document.getElementById('modalName').textContent = e.name;
    
    const mb = document.getElementById('modalMuscle');
    mb.textContent = e.muscle.replace('-', ' ');
    mb.className = `muscle-badge muscle-${e.muscle}`;
    document.getElementById('modalDesc').textContent = e.desc;
    
    // to get the steps from the exerciseStepsData using the exerciseID
    const apiSteps = exerciseStepsData.filter(s=> s.exerciseID === id);

    const stepsEl = document.getElementById('modalSteps');
    stepsEl.innerHTML = '';
    const filteredsteps = apiSteps.sort((a,b)=> a.stepOrder - b.stepOrder);

    filteredsteps.forEach((s) => {
    stepsEl.innerHTML += `
        <div class="modal-step">
            <div class="step-num">${s.stepOrder}</div>
            <div>${s.step_desc}</div>
        </div>
    `;
});
    // FIXED: Handle tags as string (split by comma) or array
    const tagsEl = document.getElementById('modalTags');
    let tags = [];
    if (Array.isArray(e.tags)) {
        tags = e.tags;
    } else if (e.tag) {
        tags = e.tag.split(',').map(t => t.trim()).filter(t => t);
    }
    tagsEl.innerHTML = tags.map(t => `<span class="modal-tag">${t}</span>`).join('');

    document.getElementById('modalOverlay').classList.add('active');

     // Reset the add panel
        resetAddPanel();

        document.getElementById('modalOverlay').classList.add('active');
}

/* click outside modal */
function closeModal(e) {
    if (e.target.id === 'modalOverlay') {
        closeModalDirect();
    }
}

/* close button / x button */
function closeModalDirect() {
    const modal = document.getElementById('modalOverlay');

    modal.classList.remove('active');

    resetAddPanel();
}

    function closeModal(e) { if (e.target === document.getElementById('modalOverlay')) closeModalDirect(); }
    function closeModalDirect() {
        document.getElementById('modalOverlay').classList.remove('active');
        resetAddPanel();
    }
    // toggle add to workout panel
    function toggleAddPanel() {
        const panel = document.getElementById('addToWorkoutPanel');
        const isVisible = panel.classList.contains('active');

        if (isVisible) {
            resetAddPanel();
        } else {
            panel.classList.add('active');
            renderWorkoutList();
        }
    }
    // openform by type
    function OpenformByType(type) {
        document.querySelectorAll('.type-section').forEach( section =>{
            section.style.display = 'none';
        });
        switch(type){
            case 'strength':
                document.getElementById('strengthFields').style.display = 'block';
                break;
            case 'cardio':
                document.getElementById('cardioFields').style.display = 'block';
                break;
            case 'flexibility':
                document.getElementById('flexibilityFields').style.display = 'block';
                break;
            case 'bodyweight':
                document.getElementById('bodyweightFields').style.display = 'block';
                break;
            default:
                document.getElementById('strength-section').style.display = 'block';
                break;

        }
    }

    const workoutExercises=[];// global variable to store the exercises in the workout for the add to workout panel;
    let workouts=[];

    async function fetchWorkouts(){
        try{
            const response = await fetch('api/get_workout_data.php');
            if(!response.ok)throw new Error('Network Response unable to fetch workouts');
             const data = await response.json();
            workouts=data;
        }catch(err){
            console.error('Error fetching user workouts:', err);
        }
    }
    async function initWorkouts(){
     await fetchWorkouts(); // // wait for workouts to be fetched before rendering the workout list
     console.log('User Workouts:', workouts);
    }
    initWorkouts(); // loads the data of the workouts when the page loads


    // rendering the workout list  in the add Workout panel;
    function renderWorkoutList() {
        const list = document.getElementById('workoutSelectList');

        if (workouts.length === 0) {
            list.innerHTML = `<div class="no-workouts-msg">You have no workouts yet.<br>Create one first from the Workouts page.</div>`;
            return;
        }

        list.innerHTML = '';
            workouts.forEach(w => {
            const item = document.createElement('div');
            item.className = 'workout-select-item';
            item.dataset.id = w.workoutID; // Use actual workout ID from API data
            item.innerHTML = `
                <div class="item-icon">🏋️</div>
                <div class="item-info">
                    <div class="item-name">${w.name}</div>
                </div>
                <div class="item-check">✓</div>
            `;
            item.addEventListener('click', () => selectWorkout(w.workoutID, item));
            list.appendChild(item);
        });
    }

    // workout selection function
    
    function selectWorkout(id, el) {
        // Deselect all
        document.querySelectorAll('.workout-select-item').forEach(i => i.classList.remove('selected'));

        // Select clicked
        el.classList.add('selected');
        selectedWorkoutId = id;
        alert(selectedWorkoutId);

        // Show sets/reps/weight form
        document.getElementById('srwForm').classList.add('active');

        // Scroll modal to show the form
        document.querySelector('.ex-modal').scrollTo({ top: 9999, behavior: 'smooth' });
    }

    // reset panel
    
    function resetAddPanel() {
        document.getElementById('addToWorkoutPanel').classList.remove('active');
        document.getElementById('srwForm').classList.remove('active');
        document.getElementById('inputSets').value   = '';
        document.getElementById('inputReps').value   = '';
        document.getElementById('inputWeight').value = '';
        document.getElementById('inputRest').value = '';
        document.getElementById('inputDuration').value='';
        document.getElementById('inputDistance').value='';
        document.getElementById('bwSets').value='';
        document.getElementById('bwReps').value='';
        document.getElementById('bwRest').value='';
        document.getElementById('flexDuration').value='';
        document.getElementById('flexHold').value='';

        selectedWorkoutId = null;
    }
    // function for submitting the exercise

    // calculating the total duration of the exercise
    function calculateExerciseDuration(sets, reps, rest,repstime=3){
        let workTime = sets * reps * repstime; // we are only assuming 3 seconds per rep
        let restime = (sets-1) * rest; // rest time between sets
        return Math.round((workTime + restime) / 60); // return duration in minutes, rounded up
    }
   function getExerciseDuration(type) {
    switch (type) {
        case 'strength':
            const sets = parseInt(document.getElementById('inputSets').value || 0);
            const reps = parseInt(document.getElementById('inputReps').value || 0);
            const rest = parseInt(document.getElementById('inputRest').value || 0);

            return calculateExerciseDuration(sets, reps, rest); // your existing function

        case 'cardio':
            const cardioDuration = parseInt(document.getElementById('inputDuration').value || 0);
            return cardioDuration;

        case 'bodyweight':
            const bwSets = parseInt(document.getElementById('bwSets').value || 0);
            const bwReps = parseInt(document.getElementById('bwReps').value || 0);
            const bwRest = parseInt(document.getElementById('bwRest').value || 0);

            return calculateExerciseDuration(bwSets, bwReps, bwRest);

        case 'flexibility':
            return parseInt(document.getElementById('flexDuration').value || 0);

        default:
            return 0;
    }
}
function confirmAddToWorkout() {

    const workout = workouts.find(w => w.workoutID ===Number(selectedWorkoutId));
    const exercise = exerciseData.find(e => e.exerciseID === currentID);
    console.log(selectWorkout);
    console.log(workout);

    if (!selectedWorkoutId || !exercise) {
        GymSwal.fire({
            icon: 'warning',
            title: 'Invalid workout or exercise selected'
        });
        return;
    }

    let dataToSend = {
        action: 'add_exercise_to_workout',
        workoutId: selectedWorkoutId,
        exerciseId: currentID,
        durationTime: getExerciseDuration(exercise.type)
    };

    switch (exercise.type) {

        case 'strength': {
            const sets = document.getElementById('inputSets').value.trim();
            const reps = document.getElementById('inputReps').value.trim();
            const weight = document.getElementById('inputWeight').value.trim();
            const rest = document.getElementById('inputRest').value.trim();

            if (!sets || !reps || !weight) {
                return GymSwal.fire({
                    icon: 'warning',
                    title: 'Please fill all strength fields'
                });
            } Object.assign(dataToSend, { //this a shortcut to assign into datatosend same variable, and the const 
                sets,
                reps,
                weight,
                rest
            });

            break;
        }

        case 'bodyweight': {
            const sets = document.getElementById('bwSets').value.trim();
            const reps = document.getElementById('bwReps').value.trim();
            const rest = document.getElementById('bwRest').value.trim();

            if (!sets || !reps) {
                return GymSwal.fire({
                    icon: 'warning',
                    title: 'Please fill all bodyweight fields'
                });
            }

            Object.assign(dataToSend, {
                sets,
                reps,
                rest
            });

            break;
        }

        case 'cardio': {
            const distance = document.getElementById('inputDistance').value.trim();

            if (!distance) {
                return GymSwal.fire({
                    icon: 'warning',
                    title: 'Please enter distance'
                });
            }

            dataToSend.distance = distance;
            break;
        }

        case 'flexibility': {
            const hold = document.getElementById('flexHold').value.trim();

            if (!hold) {
                return GymSwal.fire({
                    icon: 'warning',
                    title: 'Please enter hold time'
                });
            }

            dataToSend.hold = hold;
            break;
        }

        default:
            return GymSwal.fire({
                icon: 'error',
                title: 'Unknown exercise type'
            });
    }

    $.ajax({
        url: "/workout_trackersys/controllers/WorkoutExercise.php",
        type: 'POST',
        data: dataToSend,

        success: function (response) {

            if (response.trim() === 'success') {
                GymToast.fire({
                    icon: 'success',
                    title: `${exercise.name} added to ${workout.name ?? 'workout'}!`
                });

                closeModalDirect();
            } else {
                GymSwal.fire({
                    icon: 'error',
                    title: 'Failed to add exercise'
                });
            }
        },

        error: function (xhr) {
            console.error(xhr);

            GymSwal.fire({
                icon: 'error',
                title: 'Server error'
            });
        }
    });
}
    /* ── GymSwal & GymToast ── */
    const GymSwal = Swal.mixin({
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
            const confirm = popup.querySelector('.swal2-confirm');
            if (confirm) { confirm.style.background = 'linear-gradient(45deg,#38BDF8,#3B82F6)'; confirm.style.border = 'none'; confirm.style.borderRadius = '50px'; confirm.style.fontFamily = 'Poppins,sans-serif'; confirm.style.fontWeight = '600'; }
        }
    });

    const GymToast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
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
//logout func
function LogoutFunc(){
    $.ajax({
        url:"/workout_trackersys/controllers/logout.php",
        type:"POST",
        success: function(result){
            if(result==="Success"){
                window.location.href = "?page=login";
            }
        },
        error: function(xhr,status, error){
            console.error("LOGOUT FAILED" + error);
        }
    })
}