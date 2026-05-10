$(document).ready(function() {
    // Profile form
    $('#profileForm').on('submit', function(e) {
        e.preventDefault();
        const formData = {
            action: 'update',
            UpdateuserID: $('#userID').val(),
            Updateusername: $('#username').val(),
            Updateemail: $('#email').val(),
            Updatepassword: '', 
            Updategender: $('#gender').val(),
            UpdatedateofBirth: $('#dob').val(),
            Updaterole: $('#role').val()
        };
        
        $.post('/workout_trackersys/controllers/UserController.php', formData, function(response) {
            if (response.trim() === 'Success') {
                Swal.fire('Success!', 'Profile updated!', 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error!', response, 'error');
            }
        }).fail(() => {
            Swal.fire('Error!', 'Server error', 'error');
        });
    });

    // Onboarding fetch and display
    loadOnboarding();

    $('#onboardingForm').on('submit', function(e) {
        e.preventDefault();
        const formData = {
            action: 'updateOnboarding',
            userID: $('#userID').val(),
            height: $('#height').val(),
            weight: $('#weight').val(),
            limitations: $('#limitations').val(),
            fitnessLevel: $('#fitnessLevel').val(),
            fitnessGoal: $('#fitnessGoal').val(),
            workoutFrequency: $('#workoutFrequency').val(),
            workoutDuration: $('#workoutDuration').val()
        };
        
        $.post('/workout_trackersys/controllers/UserController.php', formData, function(response) {
            if (response.trim() === 'Success') {
                Swal.fire('Success!', 'Fitness profile updated!', 'success').then(() => {
                    loadOnboarding();
                    toggleOnboardingEdit();
                });
            } else {
                Swal.fire('Error!', response, 'error');
            }
        }).fail(() => {
            Swal.fire('Error!', 'Server error', 'error');
        });
    });

    // Settings password change
    $('#passwordForm').on('submit', function(e) {
        e.preventDefault();
        if ($('#newPassword').val() !== $('#confirmPassword').val()) {
            Swal.fire('Error!', 'Passwords do not match', 'error');
            return;
        }
        const formData = {
            action: 'changePassword',
            currentPassword: $('#currentPassword').val(),
            newPassword: $('#newPassword').val()
        };
        
        $.post('/workout_trackersys/controllers/UserController.php', formData, function(response) {
            if (response.trim() === 'Success') {
                Swal.fire('Success!', 'Password updated!', 'success');
                $('#passwordForm')[0].reset();
            } else {
                Swal.fire('Error!', response, 'error');
            }
        }).fail(() => {
            Swal.fire('Error!', 'Server error', 'error');
        });
    });
});

function toggleEdit() {
    $('#viewMode, #editMode').toggle();
    const btn = $('#editBtn');
    btn.text(btn.text() === 'Edit Profile' ? 'Cancel' : 'Edit Profile');
}

function toggleOnboardingEdit() {
    $('.onboarding-view, .onboarding-edit').toggle();
    const btn = $('#onboardingEditBtn');
    btn.text(btn.text() === 'Edit Fitness Profile' ? 'Cancel Edit' : 'Edit Fitness Profile');
}

function loadOnboarding() {
    $.get('/workout_trackersys/controllers/UserController.php?action=getOnboarding&userID=' + $('#userID').val(), function(data) {
        const onboarding = JSON.parse(data);
        if (onboarding.profile) {
            $('#heightView').text(onboarding.profile.height || 'Not set');
            $('#weightView').text(onboarding.profile.weight || 'Not set');
            $('#levelView').text(onboarding.profile.fitnessLevel || 'Not set');
            $('#goalView').text(onboarding.profile.fitnessGoal || 'Not set');
            $('#frequencyView').text(onboarding.profile.workoutFrequency || 'Not set');
            $('#durationView').text(onboarding.profile.pref_duration || 'Not set');
            
            // Preferences
            $('#workoutsList').empty();
            if (onboarding.prefs.workouts) {
                onboarding.prefs.workouts.forEach(w => $('#workoutsList').append(`<span class="pref-tag">${w}</span>`));
            }
            
            $('#locationsList').empty();
            if (onboarding.prefs.locations) {
                onboarding.prefs.locations.forEach(l => $('#locationsList').append(`<span class="pref-tag">${l}</span>`));
            }
            
            $('#equipmentList').empty();
            if (onboarding.prefs.equipment) {
                onboarding.prefs.equipment.forEach(e => $('#equipmentList').append(`<span class="pref-tag">${e}</span>`));
            }
            
            $('#motivationList').empty();
            if (onboarding.prefs.motivation) {
                onboarding.prefs.motivation.forEach(m => $('#motivationList').append(`<span class="pref-tag">${m}</span>`));
            }
        } else {
            $('.onboarding-section').html('<p class="no-data">Complete onboarding to see your fitness profile</p>');
        }
    }).fail(function() {
        $('.onboarding-section').html('<p class="no-data">Error loading fitness data</p>');
    });
}

function ToggleSidebar() {
    $('#sidebar').toggleClass('active');
}

// Theme toggle
const themeToggle = document.getElementById('themeToggle');
if (themeToggle) {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        themeToggle.checked = true;
    }
    themeToggle.addEventListener('change', function() {
        const theme = this.checked ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
    });
}

