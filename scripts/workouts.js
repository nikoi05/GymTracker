    function ToggleSidebar() {
     const sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("active");
  // Save the state based on the current class list
  const isActive = sidebar.classList.contains('active');
  localStorage.setItem('sidebar-state', isActive ? 'active' : 'inactive');
  }

/* ============================================================
   GYMSWAL & GYMTOAST (shared) - MUST BE DECLARED FIRST
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

const themeToggle = document.getElementById('themeToggle');
const themeLabel = document.getElementById('theme-label');
const thumb = document.querySelector('.toggle-thumb i');
// APPLY SAVED THEME ON LOAD
window.addEventListener('DOMContentLoaded', () => {
    const savedStateToggle = localStorage.getItem('sidebar-state');
    const sidebar = document.getElementById("sidebar");

    if (savedStateToggle === 'active') {
        sidebar.classList.add('active');
    } else {
        sidebar.classList.remove('active');
    }

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

// When A card is clicked It will show the details of that card # NOTE NOT YET DONE
const container = document.querySelector('.workout-container')
container.addEventListener('click', (e)=>{
    const clickedDiv =e.target.closest('.workout-card');
    if(clickedDiv){
        const workoutId = clickedDiv.getAttribute('data-id');
        // Redirect to workout details page with the workout ID as a query parameter
        window.location.href = `index.php?page=getWorkoutDetails&id=${workoutId}`;
    }
});

 
// Function to open modal
function openModal() {
    const modal = document.getElementById('addWorkoutModal');
    modal.classList.add('active');
}

// Function to close modal
function closeModal() {
    const modal = document.getElementById('addWorkoutModal');
    modal.classList.remove('active');
}
//close modal for edit workout
function closeModalEdit(){
    const modal = document.getElementById('EditWorkoutModal');
    modal.classList.remove('active');
}

// Character Counter Logic
const descArea = document.getElementById('workoutDescription');
const charDisplay = document.getElementById('charCount');

descArea.addEventListener('input', () => {
    const length = descArea.value.length;
    charDisplay.textContent = `${length} / 500`;
    
    // Optional: Change color if getting close to limit
    if (length >= 450) {
        charDisplay.style.color = "crimson";
    } else {
        charDisplay.style.color = "var(--text-muted)";
    }
});

// Close modal when clicking outside the content box
window.onclick = function(event) {
    const modal = document.getElementById('addWorkoutModal');
    if (event.target == modal) {
        closeModal();
    }
}
//event listner for textarea

const textarea = document.querySelector("textarea[name='description']");
const counter = document.getElementById("charCount");
textarea.addEventListener("input", () => {
    counter.textContent = `${textarea.value.length} / 500`;
});

function addWorkout(){
    const workoutName = document.getElementById("workoutName").value.trim();
    let workoutDescription =document.getElementById("workoutDescription").value.trim();
    

if(!workoutName){
        GymToast.fire({
            icon: 'error',
            title: 'Please input workout name'
        });
        return; 
    }if(!workoutDescription){
        workoutDescription ="none";
    }
    $.ajax({
        url:"/workout_trackersys/controllers/Workoutcontroller.php",
        type:"POST",
        data:{
            action: "AddWorkout",
            workoutName: workoutName,
            workoutDescription: workoutDescription,
        },
success:function(result){
            const normalized = String(result).trim().toLowerCase();
            if (normalized === "success") {
                // Close modal first
                closeModal();
                GymToast.fire({
                    icon: 'success',
                    title: 'Workout added successfully'
                });
                // Delay reload to allow toast to display
                setTimeout(() => {
                    location.reload();
                }, 3000);
            } else {
                GymSwal.fire({
                    icon: 'error',
                    title: 'Add failed',
                    text: String(result)
                });
            }
        }
    })
}
function EditWorkout($WorkoutID){
    const Editmodal = document.getElementById('EditWorkoutModal');
    Editmodal.classList.add('active'); 
    }
function SaveEdit(WorkoutID){
    let workoutName = document.getElementById("editWorkoutName").value.trim();
    let workoutDescription = document.getElementById("editWorkoutDescription").value.trim();
    if(!workoutName){
        GymToast.fire({
            icon: 'error',
            title: 'Please input workout name'
        });
        return; 
    }
     $.ajax({
        url:"/workout_trackersys/controllers/Workoutcontroller.php",
        type:"POST",
        data:{
            action: "Edit",
            EditworkoutID: WorkoutID,
            EditworkoutName: workoutName,
            EditworkoutDescription: workoutDescription,
        },
success:function(result){
            if (String(result).trim().toLowerCase() === "success") {
                // Close modal first
                closeModalEdit();
                GymToast.fire({
                    icon: 'success',
                    title: 'Workout updated successfully'
                });
                // Delay reload to allow toast to display
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                GymSwal.fire({
                    icon: 'error',
                    title: 'Update failed',
                    text: String(result)
                });
            }
        }
    })

}
$(document).ready(function () {
    $('#workoutTable').DataTable({
        pageLength: 3,        // limit to 3
        lengthChange: false,
        searching: true,
        info: false,
        ordering: false
    });
});
function DeleteWorkout(WorkoutID) {
    // Show confirmation dialog using GymSwal
    GymSwal.fire({
        title: 'Delete Workout',
        text: 'Are you sure you want to delete this workout?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "/workout_trackersys/controllers/Workoutcontroller.php",
                type: "POST",
                data: {
                    action: "Delete",
                    workoutID: WorkoutID
                },
success: function(result) {
                    if (result === "success") {
                        GymToast.fire({
                            icon: 'success',
                            title: 'Workout deleted successfully'
                        });
                        // Delay reload to allow toast to display
                        setTimeout(() => {
                            window.location.href = "index.php?page=workouts";
                        }, 1500);
                    } else {
                        GymToast.fire({
                            icon: 'error',
                            title: 'Delete failed: ' + result
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    GymToast.fire({
                        icon: 'error',
                        title: 'Server error occurred'
                    });
                }
            });
        }
    });

}

document.addEventListener('DOMContentLoaded', function() {
    // Prevent Materialize from styling selects in modals
    if (document.querySelector('.modal')) {
        M.AutoInit(); // Still init other components
        // Don't call select init on modal selects
    }
});
//logout func

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
