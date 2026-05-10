
document.addEventListener("DOMContentLoaded", function () {
    new DataTable('#usersTable', {
        pageLength: 5,
        paging: true,
        searching: true,
        info: true,
        language: {
            paginate:{
                previous: '<i class="material-icons" style="font-size:18px; vertical-align:middle;">chevron_left</i>',
                next: '<i class="material-icons" style="font-size:18px; vertical-align:middle;">chevron_right</i>'
            }
        }
    });
});
    function ToggleSidebar() {
    document.getElementById("sidebar").classList.toggle("active");
  }
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
function LogoutFunc() {
    GymSwal.fire({
        title: 'Leaving so soon?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, log out',
        cancelButtonText: 'Stay'
    }).then(r => {
        if (r.isConfirmed) {
            $.ajax({
                url: '/workout_trackersys/controllers/logout.php',
                type: 'POST',
                success: () => window.location.href = '?page=login'
            });
        }
    });
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

const modal = document.getElementById("AddModal");

// open modal
function openAddModal() {
    modal.classList.add("active");
}

// close modal
function closeAddModal() {
    modal.classList.remove("active");
}

// close when clicking outside
window.onclick = function(e) {
    if (e.target === modal) {
        closeAddModal();
    }
}


function addUserFunc() {
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
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'User Added',
                    confirmButtonText:"Okay",
                    showConfirmButton:true
                }).then((result) =>{
                   location.reload();
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


// Open Edit Modal
function openEditModal(userData) {
    const editModal = document.getElementById('EditModal');
    window.currentUserData =userData;
    
    // Fill the fields
    document.getElementById('edit_userID').value = userData.userID;
    document.getElementById('edit_username').value = userData.username;
    document.getElementById('edit_email').value = userData.email;
    document.getElementById('edit_gender').value = userData.gender;
    document.getElementById('edit_doBirth').value = userData.day_of_birth;

    // Show it
    editModal.classList.add('active');
}

// Close any modal by ID
function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

// Universal close when clicking outside ANY modal
window.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('active');
    }
});
function updateUserFunc(){
    const originalData =window.currentUserData;
    // 1. Get the values
    let userID   = document.getElementById('edit_userID').value;
    let username = document.getElementById('edit_username').value.trim();
    let email    = document.getElementById('edit_email').value.trim();
    let password =document.getElementById('edit_password').value.trim();
    let doBirth  = document.getElementById('edit_doBirth').value.trim();
    let gender   = document.getElementById('edit_gender').value;
    let role     = document.getElementById('edit_role').value;

    // check if empty field then it will use the original data
    username = username || originalData.username;
    email    = email    || originalData.email;
    doBirth  = doBirth  || originalData.day_of_birth;
    gender   = gender   || originalData.gender;
    role     = role     || originalData.role;
    // 2. Validation
    if(email!==originalData.email && !validateEmail(email)){
         Swal.fire({ icon: 'error', title: 'Invalid Email', text: 'Please enter a valid email address.' });
        return;
    }
    $.ajax({
        url: "/workout_trackersys/controllers/UserController.php",
        type: "POST",
        data: { 
            action: "update",
            UpdateuserID: userID,       
            Updateusername: username, 
            Updateemail: email, 
            Updatepassword:password,
            UpdatedateofBirth: doBirth,  
            Updategender: gender,
            Updaterole:role
        },
        success: function(returnedData) {
            const data = returnedData.trim();
            if (data === "Success") {
                Swal.fire({
                    icon: 'success',
                    title: 'User Updated',
                    confirmButtonText: "Okay"
                }).then(() => {
                    location.reload(); 
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: data
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


/////////DELETE USER FUNCTION/////////
function deleteUser(userID){
    Swal.fire({
        title: 'Are you sure?, any actions are irreversible',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, keep it'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "/workout_trackersys/controllers/UserController.php",
                type: "POST",
                data: { userID: userID },
                success: function(returnedData) {
                    const data = returnedData.trim();
                    if (data === "Success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'User Deleted',
                            confirmButtonText: "Okay"
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Oops...',
                            text: data
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
    });
}
