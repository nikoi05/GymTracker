
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Onboarding</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="/workout_trackersys/assets/onboarding.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
 <!-- PARTICLES -->
    <div class="particles" id="particles"></div>

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="nav-brand">💪 Gym<span>Tracker</span></div>
        <div class="nav-setup-label">Profile Setup</div>
    </nav>

    <!-- STEP DOTS -->
    <div class="step-dots-bar" id="stepDots">
        <span class="active"></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
    </div>

    <!-- MAIN -->
    <main class="onboard-main">
        <div class="card">

            <div class="progress-container">
                <div class="progress-bar" id="progressBar"></div>
            </div>

            <form id="onboardingForm">

                <!-- STEP 1 -->
                <div class="step">
                    <div class="step-hint">
                        <span class="hint-icon">📏</span>
                        <p>These measurements help us calculate your BMI and tailor workout intensity. If you have injuries, let us know so we can keep things safe.</p>
                    </div>
                    <div class="step-label">Step 1 of 6</div>
                    <h3>Physical Attributes</h3>
                    <div class="field-group">
                        <label class="field-label">Height (cm)</label>
                        <input type="number" name="height" id="height" placeholder="e.g. 175">
                    </div>
                    <div class="field-group">
                        <label class="field-label">Weight (kg)</label>
                        <input type="number" name="weight" id="weight" placeholder="e.g. 70">
                    </div>
                    <div class="field-group">
                        <label class="field-label">Physical limitations or injuries &nbsp;<span style="opacity:.45; font-weight:400; font-size:.8rem">(optional)</span></label>
                        <textarea name="limitations" id="limitations" placeholder="e.g. lower back pain, bad knees — or leave blank if none"></textarea>
                    </div>
                    <div class="btn-row">
                        <button type="button" class="next">Next <span class="material-icons" style="font-size:18px">arrow_forward</span></button>
                    </div>
                </div>

                <!-- STEP 2 -->
                <div class="step hidden">
                    <div class="step-hint">
                        <span class="hint-icon">📊</span>
                        <p>Be honest — this helps us set the right difficulty. You can always update this as you improve over time.</p>
                    </div>
                    <div class="step-label">Step 2 of 6</div>
                    <h3>Fitness Level</h3>
                    <div class="choice-grid" style="grid-template-columns:repeat(3,1fr)">
                        <label class="custom-radio"><input type="radio" name="fitness_level" value="Beginner"> 🌱 Beginner</label>
                        <label class="custom-radio"><input type="radio" name="fitness_level" value="Intermediate"> 📈 Intermediate</label>
                        <label class="custom-radio"><input type="radio" name="fitness_level" value="Advanced"> 🔥 Advanced</label>
                    </div>
                    <div class="btn-row">
                        <button type="button" class="prev"><span class="material-icons" style="font-size:18px">arrow_back</span> Back</button>
                        <button type="button" class="next">Next <span class="material-icons" style="font-size:18px">arrow_forward</span></button>
                    </div>
                </div>

                <!-- STEP 3 -->
                <div class="step hidden">
                    <div class="step-hint">
                        <span class="hint-icon">🎯</span>
                        <p>Your goal shapes everything — from workout types to how we track progress. Pick the one that matters most right now.</p>
                    </div>
                    <div class="step-label">Step 3 of 6</div>
                    <h3>Fitness Goal</h3>
                    <div class="field-group">
                        <label class="field-label">What's your primary goal?</label>
                        <select name="FitnessGoal">
                            <option value="">Select a goal</option>
                            <option value="weight_loss">🔥 Weight Loss</option>
                            <option value="muscle_gain">💪 Build Muscle</option>
                            <option value="Improve endurance">🏃 Improve Endurance</option>
                            <option value="general_fitness">🧘 General Fitness</option>
                        </select>
                    </div>
                    <div class="btn-row">
                        <button type="button" class="prev"><span class="material-icons" style="font-size:18px">arrow_back</span> Back</button>
                        <button type="button" class="next">Next <span class="material-icons" style="font-size:18px">arrow_forward</span></button>
                    </div>
                </div>

                <!-- STEP 4 -->
                <div class="step hidden">
                    <div class="step-hint">
                        <span class="hint-icon">📅</span>
                        <p>Be realistic — consistency beats intensity. 3 solid days beats 6 burned-out ones. Pick what you can genuinely commit to.</p>
                    </div>
                    <div class="step-label">Step 4 of 6</div>
                    <h3>Duration & Frequency</h3>
                    <h2>How often will you train?</h2>
                    <div class="choice-grid">
                        <label class="custom-radio"><input type="radio" name="workout_frequency" value="1-2/week"> 1–2× / week</label>
                        <label class="custom-radio"><input type="radio" name="workout_frequency" value="3-4/week"> 3–4× / week</label>
                        <label class="custom-radio"><input type="radio" name="workout_frequency" value="5-6/week"> 5–6× / week</label>
                        <label class="custom-radio"><input type="radio" name="workout_frequency" value="daily"> Daily 🔥</label>
                    </div>
                    <h2>How long per session?</h2>
                    <div class="choice-grid">
                        <label class="custom-radio"><input type="radio" name="workout_duration" value="20-30mins"> 10–20 min</label>
                        <label class="custom-radio"><input type="radio" name="workout_duration" value="1hr-1hr30min"> 30–45 min</label>
                        <label class="custom-radio"><input type="radio" name="workout_duration" value="2hours+"> 60+ min</label>
                    </div>
                    <div class="btn-row">
                        <button type="button" class="prev"><span class="material-icons" style="font-size:18px">arrow_back</span> Back</button>
                        <button type="button" class="next">Next <span class="material-icons" style="font-size:18px">arrow_forward</span></button>
                    </div>
                </div>

                <!-- STEP 5 -->
                <div class="step hidden">
                    <div class="step-hint">
                        <span class="hint-icon">🏋️</span>
                        <p>Select everything that applies. More detail = better personalisation. Pick all that match your situation.</p>
                    </div>
                    <div class="step-label">Step 5 of 6</div>
                    <h3>Preferences & Location</h3>
                    <h2>Preferred Workouts</h2>
                    <div class="choice-grid">
                        <label class="custom-checkbox"><input type="checkbox" name="preferred_workouts[]" value="Cardio"> 🏃 Cardio</label>
                        <label class="custom-checkbox"><input type="checkbox" name="preferred_workouts[]" value="Strength"> 🏋️ Strength</label>
                        <label class="custom-checkbox"><input type="checkbox" name="preferred_workouts[]" value="HIIT"> ⚡ HIIT</label>
                        <label class="custom-checkbox"><input type="checkbox" name="preferred_workouts[]" value="Yoga"> 🧘 Yoga</label>
                    </div>
                    <h2>Where Do You Train?</h2>
                    <div class="choice-grid">
                        <label class="custom-checkbox"><input type="checkbox" name="locations[]" value="Home"> 🏠 Home</label>
                        <label class="custom-checkbox"><input type="checkbox" name="locations[]" value="Commercial_Gym"> 🏢 Gym</label>
                        <label class="custom-checkbox"><input type="checkbox" name="locations[]" value="Park"> 🌳 Park</label>
                        <label class="custom-checkbox"><input type="checkbox" name="locations[]" value="others"> 📍 Other</label>
                    </div>
                    <h2>Equipment Available</h2>
                    <div class="choice-grid">
                        <label class="custom-checkbox"><input type="checkbox" name="equipment[]" value="dumbbells"> 🏋️ Dumbbells</label>
                        <label class="custom-checkbox"><input type="checkbox" name="equipment[]" value="machines"> ⚙️ Machines</label>
                        <label class="custom-checkbox"><input type="checkbox" name="equipment[]" value="Resistance bands"> 🟡 Bands</label>
                        <label class="custom-checkbox"><input type="checkbox" name="equipment[]" value="Treadmills"> 🏃 Treadmill</label>
                        <label class="custom-checkbox"><input type="checkbox" name="equipment[]" value="none"> 🤸 None</label>
                    </div>
                    <div class="btn-row">
                        <button type="button" class="prev"><span class="material-icons" style="font-size:18px">arrow_back</span> Back</button>
                        <button type="button" class="next">Next <span class="material-icons" style="font-size:18px">arrow_forward</span></button>
                    </div>
                </div>

                <!-- STEP 6 -->
                <div class="step hidden">
                    <div class="step-hint">
                        <span class="hint-icon">🔥</span>
                        <p>Knowing what drives you helps us keep you motivated. Pick as many as apply — no wrong answers here!</p>
                    </div>
                    <div class="step-label">Step 6 of 6</div>
                    <h3>Lifestyle & Motivation</h3>
                    <h2>What Motivates You?</h2>
                    <div class="choice-grid">
                        <label class="custom-checkbox"><input type="checkbox" name="motivation[]" value="Health"> ❤️ Health</label>
                        <label class="custom-checkbox"><input type="checkbox" name="motivation[]" value="Appearance"> ✨ Appearance</label>
                        <label class="custom-checkbox"><input type="checkbox" name="motivation[]" value="Stress Relief"> 🧠 Stress Relief</label>
                        <label class="custom-checkbox"><input type="checkbox" name="motivation[]" value="Social/Community"> 👥 Community</label>
                        <label class="custom-checkbox"><input type="checkbox" name="motivation[]" value="Heartbreak"> 💔 Heartbreak</label>
                    </div>
                    <div class="btn-row">
                        <button type="button" class="prev"><span class="material-icons" style="font-size:18px">arrow_back</span> Back</button>
                        <button type="button" class="submit-btn" onclick="onboardSubmitFunc()">🚀 Let's Go!</button>
                    </div>
                </div>

            </form>
        </div>
    </main>

    <!-- FOOTER -->
    <footer>
        <div class="footer-container">
            <div class="footer-brand">💪 GymTracker</div>
        </div>
        <div class="footer-copyright">© 2026 Gym Tracker. Built with ❤️ for fitness lovers by Niko</div>
    </footer>
</body>


<script src="/workout_trackersys/scripts/redirect.js"></script>
<script src="/workout_trackersys/scripts/onboardingservices.js"></script>
<script src="/workout_trackersys/scripts/magic.js"></script>
<script src="/workout_trackersys/scripts/authservices.js"></script>

</body>
</html>