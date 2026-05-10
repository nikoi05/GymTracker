/////////////////////// ONBOARDING FUNCTIONS //////////////////////

function onboardSubmitFunc(){
    const data = getOnboardingData();
    if(!data) return;

    $.ajax({
        url:"/workout_trackersys/controllers/onboardingController.php",
        type:"POST",
        data: data,
        success: function(result){
            if(result==="working"){
                Swal.fire({
                    icon: 'success',
                    title: 'Profile Setup Complete!',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = "?page=dashboard";
                });
            } else if(result==="update_failed"){
                Swal.fire({
                    icon: 'error',
                    title: 'Profile Level Update Failed'
                });
            } else if(result==="insert_failed"){
                Swal.fire({
                    icon: 'error',
                    title: 'Insertion Failed'
                });
            } else{
                Swal.fire({
                    icon: 'error',
                    title: 'Unknown Error'
                });
            }
        },
        error: function(xhr,status, error){
            Swal.fire({
                icon: 'error',
                title: 'Submission Error',
                text: error
            });
        }
    })
}

function getOnboardingData() {
    let onboardingErrors = [];

    const height = document.querySelector('input[name="height"]').value.trim();
    const weight = document.querySelector('input[name="weight"]').value.trim();
    const limitations = document.querySelector('textarea[name="limitations"]').value.trim();
    const fitnessLevel = document.querySelector('input[name="fitness_level"]:checked')?.value?.trim();
    const fitnessGoal = document.querySelector('select[name="FitnessGoal"]').value.trim();
    const workoutFrequency = document.querySelector('input[name="workout_frequency"]:checked')?.value?.trim();
    const workoutDuration = document.querySelector('input[name="workout_duration"]:checked')?.value?.trim();

    const preferredWorkouts = Array.from(document.querySelectorAll('input[name="preferred_workouts[]"]:checked')).map(el => el.value);
    const locations = Array.from(document.querySelectorAll('input[name="locations[]"]:checked')).map(el => el.value);
    const equipment = Array.from(document.querySelectorAll('input[name="equipment[]"]:checked')).map(el => el.value);
    const motivation = Array.from(document.querySelectorAll('input[name="motivation[]"]:checked')).map(el => el.value);

    // Validation
    if (!height) onboardingErrors.push("Please input your Height!");
    if (!weight) onboardingErrors.push("Please input your Weight!");
    if (!limitations) onboardingErrors.push("Please complete the limitations field!");
    if (!fitnessLevel) onboardingErrors.push("Please select your Fitness Level!");
    if (!fitnessGoal) onboardingErrors.push("Please select your Fitness Goal!");
    if (!workoutFrequency) onboardingErrors.push("Please select Workout Frequency!");
    if (!workoutDuration) onboardingErrors.push("Please select Workout Duration!");
    if (preferredWorkouts.length === 0) onboardingErrors.push("Please select at least one Preferred Workout!");
    if (locations.length === 0) onboardingErrors.push("Please select at least one Location!");
    if (equipment.length === 0) onboardingErrors.push("Please select at least one Equipment!");
    if (motivation.length === 0) onboardingErrors.push("Please select at least one Motivation!");

    if(onboardingErrors.length > 0){
        Swal.fire({
            icon: 'error',
            title: 'Please fix the following errors:',
            html: onboardingErrors.join("<br>")
        });
        return null;
    }

    return {
        height, weight, limitations, fitnessLevel, fitnessGoal, workoutFrequency, workoutDuration,
        preferredWorkouts, locations, equipment, motivation
    };
}

// UI UPDATE //
 /* Multi-step */
        const steps       = document.querySelectorAll('.step');
        const progressBar = document.getElementById('progressBar');
        const dots        = document.querySelectorAll('#stepDots span');
        let current       = 0;
        const total       = steps.length;

        function updateUI() {
            steps.forEach((s, i) => s.classList.toggle('hidden', i !== current));
            progressBar.style.width = ((current + 1) / total * 100) + '%';
            dots.forEach((d, i) => {
                d.classList.remove('active', 'done');
                if (i === current) d.classList.add('active');
                else if (i < current) d.classList.add('done');
            });
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        document.querySelectorAll('button.next').forEach(btn =>
            btn.addEventListener('click', () => { if (current < total - 1) { current++; updateUI(); } })
        );
        document.querySelectorAll('button.prev').forEach(btn =>
            btn.addEventListener('click', () => { if (current > 0) { current--; updateUI(); } })
        );

        updateUI();
