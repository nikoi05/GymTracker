///////////////////////////////// User Authentication ////////////////////////////

// Registration function
function registerUserFunc() {
    const username = document.getElementById("username").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const password_confirm = document.getElementById("confirm_password").value.trim();
    const doBirth = document.getElementById("doBirth").value.trim();

    let errors = [];

    // Username
    if (!username) {
        errors.push("Username is required.");
        setErrorBorder(document.getElementById("username"));
    } else clearErrorBorder(document.getElementById("username"));

    // Email
    if (!email) {
        errors.push("Email is required.");
        setErrorBorder(document.getElementById("email"));
    } else clearErrorBorder(document.getElementById("email"));

    // Password
    const passwordValidation = validatePasswordRules(password);
    if (!password) {
        errors.push("Password is required.");
        setErrorBorder(document.getElementById("password"));
    } else if (!passwordValidation.isValid) {
        errors.push("Password must be at least 8 characters and include uppercase, lowercase, number, and special character.");
        setErrorBorder(document.getElementById("password"));
    } else clearErrorBorder(document.getElementById("password"));

    // Confirm Password
    if (!password_confirm) {
        errors.push("Confirm Password is required.");
        setErrorBorder(document.getElementById("confirm_password"));
    } else clearErrorBorder(document.getElementById("confirm_password"));

    // Date of Birth
    const birthValidation = validateBirthDate(doBirth);
    if (!doBirth) {
        errors.push("Date of Birth is required.");
        setErrorBorder(document.getElementById("doBirth"));
    } else if (!birthValidation.isValid) {
        errors.push(birthValidation.message);
        setErrorBorder(document.getElementById("doBirth"));
    } else clearErrorBorder(document.getElementById("doBirth"));

    // Gender
    const gender = getotherGender() !== "" ? getotherGender() : document.getElementById("gender").value;
    if (!gender) {
        errors.push("Gender is required.");
        setErrorBorder(document.getElementById("gender"));
    } else clearErrorBorder(document.getElementById("gender"));

    // Password match verification
    verifypassFUNC(errors);

    // Email format validation
    if (email && !validateEmail(email)) {
        errors.push("Invalid email format.");
        setErrorBorder(document.getElementById("email"));
    }

    // Show errors if any
    if (errors.length > 0) {
        (window.GymSwal || Swal).fire({
            icon: 'error',
            title: 'Please check the following:',
            html: errors.join("<br>")
        });
        return;
    }

    // AJAX registration
    $.ajax({
        url: "/workout_trackersys/controllers/UserController.php",
        type: "POST",
        data: { username, email, password, dateofBirth: doBirth, gender },
        success: function(returnedData) {
            const data = returnedData.trim();
            if (data.includes("Email already exists") || data.includes("Username already exists")) {
                (window.GymSwal || Swal).fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: data
                });
            } else if(data.includes("Success")) {
                (window.GymSwal || Swal).fire({
                    icon: 'success',
                    title: "Registration Complete!",
                    confirmButtonText:"Okay",
                    showConfirmButton:true
                }).then((result) =>{
                   window.location.href ='index.php?page=login';
                });
            }
        },
        error: function(xhr) {
            (window.GymSwal || Swal).fire({
                icon: 'error',
                title: 'Error',
                text: xhr.status + ": " + xhr.responseText
            });
        }
    });
}

// Login function
function loginUserFunc(){
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("Logpassword").value.trim();
    let errors = [];

    if(!email){
        errors.push("Email is required.");
        setErrorBorder(document.getElementById("email"));
    } else clearErrorBorder(document.getElementById("email"));

    if(!password){
        errors.push("Password is required.");
        setErrorBorder(document.getElementById("Logpassword"));
    } else clearErrorBorder(document.getElementById("Logpassword"));

    if(errors.length > 0){
        (window.GymSwal || Swal).fire({
            icon: 'error',
            title: 'Please check the following:',
            html: errors.join("<br>")
        });
        return;
    }

    $.ajax({
        url: "/workout_trackersys/controllers/UserController.php",
        type: "POST",
        data: { email:email, Logpassword: password },
        success: function(returnedData) {
            const level = returnedData.trim().split("_");
            const data = returnedData.trim();
            if(data ==="locked"){
                GymSwal.fire({
                    icon:'warning',
                    title:'Account Locked! Too many attempts',
                    timer:2000,
                    showConfirmButton:false
                });
                return;
            }

            if(level[0]==="Success"){
                const ProfileLevel = level[1];
                GymSwal.fire({
                    icon: 'success',
                    title: 'Login Successful!',
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    if(ProfileLevel === "0") {
                        window.location.href = "?page=onboarding";
                    } else if(ProfileLevel === "1") {
                        window.location.href = "?page=dashboard";
                    } else if(ProfileLevel === "2"){
                        window.location.href = "?page=admindash";
                    }else {
                        (window.GymSwal || Swal).fire({
                            icon: 'error',
                            title: 'Unexpected Profile Level'
                        });
                    }
                });
            } else if(level[0]==="failed"){
                GymSwal.fire({
                    icon: 'error',
                    title: 'Invalid Credentials'
                });
            } else {
                GymSwal.fire({
                    icon: 'error',
                    title: 'Invalid Credentials'
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

// Logout function
window.LogoutFunc = window.LogoutFunc || function() {
    (window.GymSwal || Swal).fire({
        title: 'Leaving so soon?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, log out',
        cancelButtonText: 'Stay'
    })
    .then(r => {
        if (r.isConfirmed) {
            $.ajax({
                url: '/workout_trackersys/controllers/logout.php',
                type: 'POST',
                success: () => window.location.href = '?page=login'
            });
        }
    });
};



/////////////////////// Verifying and field errors checking //////////////////////

// Password verification
function verifypassFUNC(errors) {
    const password = document.getElementById("password");
    const confirm_password = document.getElementById("confirm_password");

    if (password.value != confirm_password.value) {
        errors.push("Passwords do not match.");
        setErrorBorder(password);
        setErrorBorder(confirm_password);
        return false;
    } else {
        clearErrorBorder(password);
        clearErrorBorder(confirm_password);
        return true;

    }
}

// Gender helper
function getotherGender() {
    return document.getElementById('othergender').value.trim();
}

// Email format validation
function validateEmail(email) {
    const emailregex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailregex.test(email);
}

function validatePasswordRules(password) {
    const checks = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /\d/.test(password),
        special: /[^A-Za-z0-9]/.test(password)
    };

    return {
        checks: checks,
        isValid: Object.keys(checks).every(function(key) { return checks[key]; })
    };
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
//passwordRules
function updatePasswordRulesUI() {
    const password = document.getElementById("password");
    const rules = document.getElementById("passwordRules");
    if (!password || !rules) return;

    const validation = validatePasswordRules(password.value);
    Object.keys(validation.checks).forEach(function(rule) {
        const item = rules.querySelector('[data-rule="' + rule + '"]');
        if (!item) return;
        const passed = validation.checks[rule];
        item.classList.toggle("valid", passed);
        item.classList.toggle("invalid", !passed && password.value.length > 0);
    });
}

function updatePasswordMatchUI() {
    const password = document.getElementById("password");
    const confirm = document.getElementById("confirm_password");
    const hint = document.getElementById("passwordMatchHint");
    if (!password || !confirm || !hint) return;

    if (!confirm.value) {
        hint.textContent = "Passwords must match.";
        hint.className = "field-hint";
        return;
    }

    const matched = password.value === confirm.value;
    hint.textContent = matched ? "Passwords match." : "Passwords do not match.";
    hint.className = "field-hint " + (matched ? "valid" : "invalid");
}

function updateBirthDateUI() {
    const dob = document.getElementById("doBirth");
    const hint = document.getElementById("dobHint");
    if (!dob || !hint) return;

    if (!dob.value) {
        hint.textContent = "You must be 18 years old or older. Future dates are not allowed.";
        hint.className = "field-hint";
        return;
    }

    const validation = validateBirthDate(dob.value);
    hint.textContent = validation.message;
    hint.className = "field-hint " + (validation.isValid ? "valid" : "invalid");
}

function initRegisterValidation() {
    const password = document.getElementById("password");
    const confirm = document.getElementById("confirm_password");
    const dob = document.getElementById("doBirth");

    if (dob) {
        const today = new Date();
        dob.setAttribute("max", today.toISOString().split("T")[0]);
    }

    if (password) {
        password.addEventListener("input", function() {
            updatePasswordRulesUI();
            updatePasswordMatchUI();
        });
    }
    // checks the inputs and updates it real time
    if (confirm) confirm.addEventListener("input", updatePasswordMatchUI);
    if (dob) dob.addEventListener("input", updateBirthDateUI);

    updatePasswordRulesUI();
    updatePasswordMatchUI();
    updateBirthDateUI();
}
// Live email validation
const emailField = document.getElementById("email");
if (emailField) emailField.addEventListener("input", function() {
    if (!validateEmail(this.value.trim())) {
        setErrorBorder(this);
    } else clearErrorBorder(this);
});

initRegisterValidation();

// Border helpers
function setErrorBorder(el) {
    el.style.borderColor = "red";
    el.style.borderWidth = "2px";
}

function clearErrorBorder(el) {
    el.style.borderColor = "";
    el.style.borderWidth = "";
}


/* ============================================================
   GYMSWAL & GYMTOAST
============================================================ */
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
