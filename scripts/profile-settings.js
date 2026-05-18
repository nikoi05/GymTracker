    function ToggleSidebar() {
     const sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("active");
  // Save the state based on the current class list
  const isActive = sidebar.classList.contains('active');
  localStorage.setItem('sidebar-state', isActive ? 'active' : 'inactive');
  }
const themeToggle = document.getElementById('themeToggle');
const themeLabel = document.getElementById('theme-label');
const thumb = document.querySelector('.toggle-thumb i');

// APPLY SAVED THEME ON LOAD
window.addEventListener('DOMContentLoaded', () => {
const savedStateToggle = localStorage.getItem('sidebar-state');
  const sidebar = document.getElementById("sidebar");

  // Only add the class if it was saved as 'active'
  if (savedStateToggle === 'active') {
      sidebar.classList.add('active');
  } else {
      sidebar.classList.remove('active');
  }


    // theme 
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

const GymSwal = window.GymSwal || Swal.mixin({
    customClass: {
        container: 'swal-on-top',
        popup: 'gym-swal-popup',
        title: 'gym-swal-title',
        htmlContainer: 'gym-swal-text',
        confirmButton: 'gym-swal-confirm',
        cancelButton: 'gym-swal-cancel'
    },
    background: 'var(--surface)',
    color: 'var(--text)',
    backdrop: 'rgba(2, 6, 23, 0.62)',
    buttonsStyling: false,
    didOpen: (p) => {
        p.style.borderRadius = '18px';
        p.style.border = '1px solid var(--border)';
        p.style.boxShadow = '0 20px 48px rgba(2,6,23,.28)';
        const ok = p.querySelector('.swal2-confirm');
        if (ok) { ok.style.background = 'linear-gradient(45deg,#38BDF8,#3B82F6)'; ok.style.color = '#fff'; ok.style.border = 'none'; ok.style.borderRadius = '999px'; ok.style.padding = '10px 20px'; ok.style.fontWeight = '600'; }
        const no = p.querySelector('.swal2-cancel');
        if (no) { no.style.background = 'var(--surface-2)'; no.style.color = 'var(--text)'; no.style.border = '1px solid var(--border)'; no.style.borderRadius = '999px'; no.style.padding = '10px 20px'; no.style.fontWeight = '600'; }
    }
});

const GymToast = window.GymToast || Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    customClass: {
        container: 'swal-on-top',
        popup: 'gym-toast-popup',
        title: 'gym-toast-title',
        htmlContainer: 'gym-toast-text'
    },
    background: 'var(--surface)',
    color: 'var(--text)',
    didOpen: (t) => {
        t.style.borderRadius = '12px';
        t.style.border = '1px solid var(--border)';
        t.style.boxShadow = '0 20px 48px rgba(2,6,23,.28)';
        t.addEventListener('mouseenter', Swal.stopTimer);
        t.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

window.GymSwal = window.GymSwal || GymSwal;
window.GymToast = window.GymToast || GymToast;


let originalData = {};

function toggleEditiz(){

    const viewMode = document.getElementById('editMode');
    const editMode = document.getElementById('viewMode');
    const editbt = document.getElementById('editBtn');

    if(viewMode.style.display === 'none'){

        viewMode.style.display = 'flex';
        editMode.style.display = 'none';
        editbt.style.display = 'none';

    }else{

        viewMode.style.display = 'none';
        editMode.style.display = 'grid';
        editbt.style.display='block';
    }

    // STORE ORIGINAL VALUES
    originalData = {
        userID: document.getElementById('userID').value,
        username: document.getElementById('username').value,
        email: document.getElementById('email').value,
        gender: document.getElementById('gender').value,
        dob: document.getElementById('dob').value,
    };

    console.log(originalData);
}


// SAVE CHANGES
function saveChangeFuncs(){

    let username = document.getElementById('username').value;
    let email = document.getElementById('email').value;
    let gender = document.getElementById('gender').value;
    let dob = document.getElementById('dob').value;

    // EMAIL VALIDATION
    const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;

    if(!email.match(emailPattern)){

        GymSwal.fire({
            icon:'warning',
            title:'Invalid Email Format',
            showConfirmButton:true
        });

        return;
    }
   
    // CHECK BIRTHDATE
    const validatBirth = validateBirthDate(dob);

    if(!validatBirth.isValid){

        GymSwal.fire({
            icon:'warning',
            title: validatBirth.message,
            showConfirmButton:true
        });
        return;
    }
     username = username || originalData.username;
    email    = email    || originalData.email;
    dob  = dob  || originalData.dob;
    gender   = gender   || originalData.gender; 
     $.ajax({
        url: "/workout_trackersys/controllers/UserController.php",
        type: "POST",
        data: {
            action: "UpdateProfile",
             username: username, 
             email:email, 
             dateofBirth: dob, 
             gender:gender },
        success: function(returnedData) {
            const data = returnedData.trim();
           if(data.includes("Success")) {
                GymSwal.fire({
                    icon: 'success',
                    title: "Profile Update Complete!",
                    confirmButtonText:"Okay",
                    showConfirmButton:true
                }).then((result) =>{
                 location.reload();
                });
            }
        },
        error: function(xhr) {
            GymSwal.fire({
                icon: 'error',
                title: 'Error',
                text: xhr.status + ": " + xhr.responseText
            });
        }
    });
}

function getAgeFromBirthDate(value) {
    const birthDate = new Date(value + "T00:00:00");
    const today = new Date();
    // get the age by its year
    let age = today.getFullYear() - birthDate.getFullYear();
    //get the month difference between two months birthmonth and today
    const monthDiff = today.getMonth() - birthDate.getMonth();
    // first comparison is checking if the month is less than zero, to check if the user is already birthday, then it decreases its age to the right one
    //second comparison to check if the birthday is in the month and if in month birthday not happened it will decrease the age.
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }

    return age;
}
//validating birth
function validateBirthDate(value) {
    if (!value) return { isValid: false, message: "Date of Birth is required." };

    const birthDate = new Date(value + "T00:00:00");
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    //checks if its a number
    if (Number.isNaN(birthDate.getTime())) {
        return { isValid: false, message: "Please enter a valid Date of Birth." };
    }
    //compares if birthday is in future
    if (birthDate > today) {
        return { isValid: false, message: "Date of Birth cannot be in the future." };
    }
    // checks if birth is correct and age requirements met
    if (getAgeFromBirthDate(value) < 18) {
        return { isValid: false, message: "You must be 18 years old or older to register." };
    }

    return { isValid: true, message: "Age requirement met." };
}

function toggleOnboardingEdit() {
    const view = document.querySelector('.onboarding-view');
    const edit = document.querySelector('.onboarding-edit');
    const btn = document.getElementById('onboardingEditBtn');
    if (!view || !edit || !btn) return;

    const editing = edit.style.display === 'block';
    if (editing) {
        edit.style.display = 'none';
        view.style.display = 'grid';
        btn.style.display = 'inline-flex';
    } else {
        view.style.display = 'none';
        edit.style.display = 'block';
        btn.style.display = 'none';
    }
}
let p ={};
function loadFitnessProfileData() {
    $.ajax({
        url: '/workout_trackersys/controllers/onboardingController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'getProfileData' },
        success: function (res) {
            if (!res || !res.ok) return;
            const p = res.profile || {};

            $('#heightView').text((p.height ?? 'Not set') + ((p.height ? ' cm' : '')));
            $('#weightView').text((p.weight ?? 'Not set') + ((p.weight ? ' kg' : '')));
            $('#levelView').text(p.fitnessLevelLabel || 'Not set');
            $('#goalView').text(p.fitnessGoalLabel || 'Not set');
            $('#frequencyView').text(p.workoutFrequencyLabel || 'Not set');
            $('#durationView').text(p.pref_durationLabel || 'Not set');
            $('#limitationsView').text(p.Physical_limitations || 'None');

            $('#height').val(p.height ?? '');
            $('#weight').val(p.weight ?? '');
            $('#fitnessLevel').val(p.fitnessLevel ?? '1');
            $('#fitnessGoal').val(p.fitnessGoal ?? '1');
            $('#workoutFrequency').val(p.workoutFrequency ?? '2');
            $('#workoutDuration').val(p.pref_duration ?? '2');
            $('#limitations').val(p.Physical_limitations ?? '');
            console.log(p);
        },
        error: function () {
            GymSwal.fire({ icon: 'error', title: 'Server error' });
        }
    });
}


$(document).on('submit', '#onboardingForm', function (e) {
    e.preventDefault();
    const payload = {
        action: 'updateProfileData',
        height: $('#height').val(),
        weight: $('#weight').val(),
        limitations: $('#limitations').val(),
        fitnessLevel: $('#fitnessLevel').val(),
        fitnessGoal: $('#fitnessGoal').val(),
        workoutFrequency: $('#workoutFrequency').val(),
        workoutDuration: $('#workoutDuration').val()
    };
    console.log(payload);
    $.ajax({
        url: '/workout_trackersys/controllers/onboardingController.php',
        type: 'POST',
        dataType: 'json',
        data: payload,
        success: function (res) {
            if (res && res.ok) {
                GymSwal.fire({ icon: 'success', title: 'Fitness profile updated!' });
                toggleOnboardingEdit();
                loadFitnessProfileData();
            } else {
                GymSwal.fire({ icon: 'error', title: 'Update failed' });
            }
        },
        error: function () {
            GymSwal.fire({ icon: 'error', title: 'Server error' });
        }
    });
});


window.addEventListener('DOMContentLoaded', loadFitnessProfileData);
