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
    if (!password) {
        errors.push("Password is required.");
        setErrorBorder(document.getElementById("password"));
    } else clearErrorBorder(document.getElementById("password"));

    // Confirm Password
    if (!password_confirm) {
        errors.push("Confirm Password is required.");
        setErrorBorder(document.getElementById("confirm_password"));
    } else clearErrorBorder(document.getElementById("confirm_password"));

    // Date of Birth
    if (!doBirth) {
        errors.push("Date of Birth is required.");
        setErrorBorder(document.getElementById("doBirth"));
    } else clearErrorBorder(document.getElementById("doBirth"));

    // Gender
    const gender = getotherGender() !== "" ? getotherGender() : document.getElementById("gender").value;

    // Password match verification
    verifypassFUNC(errors);

    // Email format validation
    if (email && !validateEmail(email)) {
        errors.push("Invalid email format.");
        setErrorBorder(document.getElementById("email"));
    }

    // Show errors if any
    if (errors.length > 0) {
        Swal.fire({
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
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: data
                });
            } else if(data.includes("Success")) {
                Swal.fire({
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
            Swal.fire({
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
        Swal.fire({
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
                        Swal.fire({
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
function LogoutFunc() {
    const fireFn = (typeof GymSwal !== 'undefined' && GymSwal && typeof GymSwal.fire === 'function')
        ? GymSwal.fire.bind(GymSwal)
        : Swal.fire.bind(Swal);

    fireFn({
        title: 'Leaving so soon?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, log out',
        cancelButtonText: 'Stay'
    })
    .then(r => {
        if (!r.isConfirmed) return;

        $.ajax({
            url: '/workout_trackersys/controllers/logout.php',
            type: 'POST',
            success: () => window.location.href = '?page=login'
        });
    });
}



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

// Live email validation
document.getElementById("email").addEventListener("input", function() {
    if (!validateEmail(this.value.trim())) {
        setErrorBorder(this);
    } else clearErrorBorder(this);
});

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
const GymSwal = Swal.mixin({
    customClass:{ container:'swal-on-top' }, backdrop:'rgba(0,0,0,0.5)',
    didOpen:(p)=>{
        p.style.background='rgba(9,9,121,0.95)'; p.style.backdropFilter='blur(20px)';
        p.style.border='1px solid rgba(255,255,255,0.2)'; p.style.borderRadius='20px';
        p.style.color='#fff'; p.style.fontFamily='Poppins,sans-serif';
        const t=p.querySelector('.swal2-title'); if(t)t.style.color='#fff';
        const tx=p.querySelector('.swal2-html-container'); if(tx)tx.style.color='rgba(255,255,255,0.8)';
        const c=p.querySelector('.swal2-confirm'); if(c){c.style.background='linear-gradient(45deg,#38BDF8,#3B82F6)';c.style.border='none';c.style.borderRadius='50px';c.style.fontFamily='Poppins,sans-serif';c.style.fontWeight='600';}
        const x=p.querySelector('.swal2-cancel'); if(x){x.style.background='rgba(255,255,255,0.15)';x.style.color='white';x.style.border='1px solid rgba(255,255,255,0.2)';x.style.borderRadius='50px';x.style.fontFamily='Poppins,sans-serif';x.style.fontWeight='600';}
    }
});

const GymToast = Swal.mixin({
    toast:true, position:'top-end', showConfirmButton:false, timer:3000, timerProgressBar:true,
    customClass:{ container:'swal-on-top' },
    didOpen:(t)=>{ t.style.background='rgba(9,9,121,0.95)'; t.style.backdropFilter='blur(20px)'; t.style.border='1px solid rgba(255,255,255,0.2)'; t.style.borderRadius='12px'; t.style.color='#fff'; t.addEventListener('mouseenter',Swal.stopTimer); t.addEventListener('mouseleave',Swal.resumeTimer); }
});
