
let usersTableDT;

document.addEventListener("DOMContentLoaded", function () {
    usersTableDT = new DataTable('#usersTable', {
        pageLength: 5,
        paging: true,
        searching: true,
        info: true,
        language: {
            paginate:{
                previous: '<i class="material-icons" style="font-size:18px; vertical-align:middle;">chevron_left</i>',
                next: '<i class="material-icons" style="font-size:18px; vertical-align:middle;">chevron_right</i>'
            }
        },
        // keep DataTables internal search enabled; we just add extra controls below
    });

    // Quick search input -> DataTables global search
    const quickSearch = document.getElementById('quickSearch');
    if (quickSearch) {
        quickSearch.addEventListener('input', () => {
            if (!usersTableDT) return;
            usersTableDT.search(quickSearch.value.trim()).draw();
        });
    }

    // Role filter -> DataTables column search (Role column index = 5)
    const roleFilter = document.getElementById('roleFilter');
    if (roleFilter) {
        roleFilter.addEventListener('change', () => {
            if (!usersTableDT) return;
            const selected = roleFilter.value;

            // Map roleID -> role_description text by reading option text
            const selectedText = selected ? roleFilter.options[roleFilter.selectedIndex].text.trim() : '';
            usersTableDT.column(5).search(selectedText).draw();
        });
    }
});
    
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
window.LogoutFunc = window.LogoutFunc || function() {
    (window.GymSwal || Swal).fire({
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
};
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
    const usernameEl = document.getElementById("username");
    const emailEl = document.getElementById("email");
    const passwordEl = document.getElementById("password");
    const passwordConfirmEl = document.getElementById("confirm_password");
    const doBirthEl = document.getElementById("doBirth");
    const genderSelectEl = document.getElementById("gender");
    const otherGenderEl = document.getElementById("othergender");

    const username = usernameEl?.value.trim() || "";
    const email = emailEl?.value.trim() || "";
    const password = passwordEl?.value || "";
    const password_confirm = passwordConfirmEl?.value || "";
    const doBirth = doBirthEl?.value.trim() || "";

    // Prefer othergender if it has a value, otherwise use the select value
    let gender = (otherGenderEl?.value || "").trim();
    if (!gender) gender = (genderSelectEl?.value || "");

    const errors = [];

    const setErr = (el, msg) => {
        if (!el) return;
        el.style.border = '1px solid rgba(239,68,68,0.9)';
        el.style.boxShadow = '0 0 0 3px rgba(239,68,68,0.15)';
        if (msg) errors.push(msg);
    };
    const clearErr = (el) => {
        if (!el) return;
        el.style.border = '';
        el.style.boxShadow = '';
    };

    // Username
    const uOk = /^[A-Za-z0-9._-]+$/.test(username) && username.length >= 3 && username.length <= 50;
    if (!username) {
        errors.push("Username is required.");
        setErr(usernameEl);
    } else if (!uOk) {
        errors.push("Username must be 3-50 chars and contain only letters, numbers, dot, underscore, dash.");
        setErr(usernameEl);
    } else {
        clearErr(usernameEl);
    }

    // Email
    if (!email) {
        errors.push("Email is required.");
        setErr(emailEl);
    } else if (!validateEmail(email)) {
        errors.push("Invalid email format.");
        setErr(emailEl);
    } else {
        clearErr(emailEl);
    }

    // Password
    if (!password) {
        errors.push("Password is required.");
        setErr(passwordEl);
    } else {
        clearErr(passwordEl);
        if (password.length < 8) {
            errors.push("Password must be at least 8 characters.");
            setErr(passwordEl);
        }
    }

    // Confirm password
    if (!password_confirm) {
        errors.push("Confirm Password is required.");
        setErr(passwordConfirmEl);
    } else {
        clearErr(passwordConfirmEl);
    }

    if (password && password_confirm && password !== password_confirm) {
        errors.push("Passwords do not match.");
        setErr(passwordEl);
        setErr(passwordConfirmEl);
    }

    // DOB
    if (!doBirth) {
        errors.push("Date of Birth is required.");
        setErr(doBirthEl);
    } else {
        const date = new Date(doBirth + 'T00:00:00');
        const now = new Date();
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        const hundredYearsAgo = new Date(now.getFullYear() - 100, now.getMonth(), now.getDate());

        if (Number.isNaN(date.getTime())) {
            errors.push("Invalid Date of Birth.");
            setErr(doBirthEl);
        } else if (date > today) {
            errors.push("Date of Birth cannot be in the future.");
            setErr(doBirthEl);
        } else if (date < hundredYearsAgo) {
            errors.push("Date of Birth looks too far in the past.");
            setErr(doBirthEl);
        } else {
            clearErr(doBirthEl);
        }
    }

    // Gender
    if (!gender) {
        errors.push("Gender is required.");
        setErr(genderSelectEl);
    } else if (!Number.isInteger(Number(gender))) {
        errors.push("Invalid gender selection.");
        setErr(genderSelectEl);
    } else {
        clearErr(genderSelectEl);
    }

    if (errors.length) {
        GymSwal.fire({
            icon: 'error',
            title: 'Please check the following:',
            html: errors.join("<br>")
        });
        return;
    }

    $.ajax({
        url: "/workout_trackersys/controllers/UserController.php",
        type: "POST",
        data: { username, email, password, dateofBirth: doBirth, gender },
        success: function(returnedData) {
            const data = String(returnedData).trim();
            if (data.includes("Email already exists") || data.includes("Username already exists")) {
                GymSwal.fire({ icon: 'warning', title: 'Oops...', text: data });
            } else {
                (window.GymSwal || Swal).fire({
                    icon: 'success',
                    title: 'User Added',
                    confirmButtonText:"Okay",
                    showConfirmButton:true
                }).then(() => location.reload());
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



// Open Edit Modal
function openEditModal(userData) {
    const editModal = document.getElementById('EditModal');
    window.currentUserData =userData;
    console.log(userData);
    
    // Fill the fields
    document.getElementById('edit_userID').value = userData.userID;
    document.getElementById('edit_username').value = userData.username;
    document.getElementById('edit_email').value = userData.email;
    document.getElementById('edit_gender').value = userData.genderID;
    document.getElementById('edit_doBirth').value = userData.day_of_birth;
    document.getElementById('edit_role').value = userData.profileLevel;

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
function unhidepassFunc(button){
    const targetId = button.getAttribute('data-target');
    const targetInput = document.getElementById(targetId);

    if(!targetInput) return;

    if(targetInput.type === "password"){
        targetInput.type = "text";
        button.textContent = "visibility_off";
    }else{
        targetInput.type = "password";
        button.textContent = "visibility";
    }
}


function updateUserFunc(){
    const originalData = window.currentUserData;

    const userIDEl = document.getElementById('edit_userID');
    const usernameEl = document.getElementById('edit_username');
    const emailEl = document.getElementById('edit_email');
    const passwordEl = document.getElementById('edit_password');
    const doBirthEl = document.getElementById('edit_doBirth');
    const genderEl = document.getElementById('edit_gender');
    const roleEl = document.getElementById('edit_role');

    const userID = userIDEl?.value;

    let username = (usernameEl?.value || '').trim();
    let email = (emailEl?.value || '').trim();
    let password = (passwordEl?.value || '').trim();
    let doBirth = (doBirthEl?.value || '').trim();
    let gender = (genderEl?.value || '');
    let role = (roleEl?.value || '');

    // If optional fields empty, use original
    username = username || originalData.username;
    email = email || originalData.email;
    doBirth = doBirth || originalData.day_of_birth;
    gender = gender || originalData.gender;
    role = role || originalData.profileLevel;

    const errors = [];

    const setErr = (el, msg) => {
        if (!el) return;
        el.style.border = '1px solid rgba(239,68,68,0.9)';
        el.style.boxShadow = '0 0 0 3px rgba(239,68,68,0.15)';
        if (msg) errors.push(msg);
    };
    const clearErr = (el) => {
        if (!el) return;
        el.style.border = '';
        el.style.boxShadow = '';
    };

    // Username
    const uOk = /^[A-Za-z0-9._-]+$/.test(username) && username.length >= 3 && username.length <= 50;
    if (!uOk) {
        setErr(usernameEl, 'Username must be 3-50 chars and contain only letters, numbers, dot, underscore, dash.');
    } else {
        clearErr(usernameEl);
    }

    // Email
    if (!email) {
        setErr(emailEl, 'Email is required.');
    } else if (!validateEmail(email)) {
        setErr(emailEl, 'Invalid email format.');
    } else {
        clearErr(emailEl);
    }

    // DOB
    if (!doBirth) {
        setErr(doBirthEl, 'Date of Birth is required.');
    } else {
        const date = new Date(doBirth + 'T00:00:00');
        const now = new Date();
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        const hundredYearsAgo = new Date(now.getFullYear() - 100, now.getMonth(), now.getDate());

        if (Number.isNaN(date.getTime())) {
            setErr(doBirthEl, 'Invalid Date of Birth.');
        } else if (date > today) {
            setErr(doBirthEl, 'Date of Birth cannot be in the future.');
        } else if (date < hundredYearsAgo) {
            setErr(doBirthEl, 'Date of Birth looks too far in the past.');
        } else {
            clearErr(doBirthEl);
        }
    }

    // Gender
    if (!gender || !Number.isInteger(Number(gender))) {
        setErr(genderEl, 'Invalid gender selection.');
    } else {
        clearErr(genderEl);
    }

    // Role
    if (role === null || role === undefined || String(role).trim() === '' || !Number.isInteger(Number(role))) {
        setErr(roleEl, 'Role is required.');
    } else {
        clearErr(roleEl);
    }

    // Password (optional)
    if (password) {
        if (password.length < 8) {
            setErr(passwordEl, 'Password must be at least 8 characters.');
        } else {
            clearErr(passwordEl);
        }
    } else {
        clearErr(passwordEl);
    }

    if (errors.length) {
        GymSwal.fire({
            icon: 'error',
            title: 'Please check the following:',
            html: errors.join('<br>')
        });
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
            Updatepassword: password,
            UpdatedateofBirth: doBirth,
            Updategender: gender,
            Updaterole: role
        },
        success: function(returnedData) {
            const data = String(returnedData).trim();
            if (data === "Success") {
                GymSwal.fire({
                    icon: 'success',
                    title: 'User Updated',
                    confirmButtonText: "Okay"
                }).then(() => location.reload());
            } else {
                GymSwal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: data
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

// Email format validation
function validateEmail(email) {
    const emailregex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailregex.test(email);
}

// ── Live password rules helper ──
const passRuleChecks = {
    len:     p => p.length >= 8,
    max:     p => p.length <= 100,
    upper:   p => /[A-Z]/.test(p),
    num:     p => /\d/.test(p),
    special: p => /[^A-Za-z0-9]/.test(p)
};

function updatePassRules(prefix, val) {
    Object.entries(passRuleChecks).forEach(([key, fn]) => {
        const el = document.getElementById(`${prefix}-rule-${key}`);
        if (el) el.style.color = fn(val) ? 'var(--accent,#38BDF8)' : 'var(--text-muted)';
    });
}

// Add modal — password rules + match hint
const addPassEl = document.getElementById('password');
const addConfEl = document.getElementById('confirm_password');
const addMatchHint = document.getElementById('add-matchHint');

if (addPassEl) {
    addPassEl.addEventListener('input', function () {
        updatePassRules('add', this.value);
        updateAddMatch();
    });
}
if (addConfEl) {
    addConfEl.addEventListener('input', updateAddMatch);
}
function updateAddMatch() {
    if (!addMatchHint || !addPassEl || !addConfEl) return;
    const cp = addConfEl.value;
    if (!cp) { addMatchHint.textContent = ''; return; }
    if (addPassEl.value === cp) {
        addMatchHint.textContent = '✓ Passwords match';
        addMatchHint.style.color = 'var(--accent,#38BDF8)';
    } else {
        addMatchHint.textContent = '✗ Passwords do not match';
        addMatchHint.style.color = '#ef4444';
    }
}

// Add modal — DOB live hint
const addDobEl = document.getElementById('doBirth');
const addDobHint = document.getElementById('add-dobHint');
if (addDobEl) {
    addDobEl.addEventListener('input', function () {
        if (!addDobHint) return;
        const d = new Date(this.value + 'T00:00:00');
        const today = new Date(); today.setHours(0,0,0,0);
        const age = today.getFullYear() - d.getFullYear() - (today < new Date(today.getFullYear(), d.getMonth(), d.getDate()) ? 1 : 0);
        if (!this.value) { addDobHint.textContent = 'Must be 18+ years old.'; addDobHint.style.color = 'var(--text-muted)'; return; }
        if (d > today) { addDobHint.textContent = '✗ Date cannot be in the future.'; addDobHint.style.color = '#ef4444'; return; }
        if (age < 18) { addDobHint.textContent = `✗ Must be at least 18 (currently ${age}).`; addDobHint.style.color = '#ef4444'; return; }
        addDobHint.textContent = `✓ Age: ${age}`; addDobHint.style.color = 'var(--accent,#38BDF8)';
    });
}

// Edit modal — password rules (show list only when typing)
const editPassEl = document.getElementById('edit_password');
const editRulesList = document.getElementById('edit-passRules');
if (editPassEl) {
    editPassEl.addEventListener('input', function () {
        if (editRulesList) editRulesList.style.display = this.value ? 'block' : 'none';
        updatePassRules('edit', this.value);
    });
}

// Edit modal — DOB live hint
const editDobEl = document.getElementById('edit_doBirth');
const editDobHint = document.getElementById('edit-dobHint');
if (editDobEl) {
    editDobEl.addEventListener('input', function () {
        if (!editDobHint) return;
        const d = new Date(this.value + 'T00:00:00');
        const today = new Date(); today.setHours(0,0,0,0);
        const age = today.getFullYear() - d.getFullYear() - (today < new Date(today.getFullYear(), d.getMonth(), d.getDate()) ? 1 : 0);
        if (!this.value) { editDobHint.textContent = 'Must be 18+ years old.'; editDobHint.style.color = 'var(--text-muted)'; return; }
        if (d > today) { editDobHint.textContent = '✗ Date cannot be in the future.'; editDobHint.style.color = '#ef4444'; return; }
        if (age < 18) { editDobHint.textContent = `✗ Must be at least 18 (currently ${age}).`; editDobHint.style.color = '#ef4444'; return; }
        editDobHint.textContent = `✓ Age: ${age}`; editDobHint.style.color = 'var(--accent,#38BDF8)';
    });
}

// Live email validation (AddModal)
const emailInput = document.getElementById("email");
if (emailInput) {
    emailInput.addEventListener("input", function() {
        if (!validateEmail(this.value.trim())) {
            this.style.border = '1px solid rgba(239,68,68,0.9)';
            this.style.boxShadow = '0 0 0 3px rgba(239,68,68,0.15)';
        } else {
            this.style.border = '';
            this.style.boxShadow = '';
        }
    });
}

// Live email validation (EditModal)
const editEmailInput = document.getElementById("edit_email");
if (editEmailInput) {
    editEmailInput.addEventListener("input", function() {
        if (!validateEmail(this.value.trim())) {
            this.style.border = '1px solid rgba(239,68,68,0.9)';
            this.style.boxShadow = '0 0 0 3px rgba(239,68,68,0.15)';
        } else {
            this.style.border = '';
            this.style.boxShadow = '';
        }
    });
}

/////////DELETE USER FUNCTION/////////

function deleteUser(userID){
    GymSwal.fire({
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
                       GymSwal.fire({
                            icon: 'success',
                            title: 'User Deleted',
                            confirmButtonText: "Okay"
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        GymSwal.fire({
                            icon: 'warning',
                            title: 'Oops...',
                            text: data
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
    });
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
