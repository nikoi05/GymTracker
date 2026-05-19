//THIS SCRIPT FOCUCES ONLY ON REDIRECTING

if (typeof window !== 'undefined' && window.Swal) {
    if (!document.getElementById('gym-swal-theme')) {
        const style = document.createElement('style');
        style.id = 'gym-swal-theme';
        style.textContent = ".swal-on-top{z-index:100000!important}.gym-swal-popup,.gym-toast-popup{background:color-mix(in srgb,var(--surface,#ffffff) 92%,transparent)!important;border:1px solid var(--border,#dbeafe)!important;color:var(--text,#0f172a)!important;font-family:'Poppins',sans-serif!important;box-shadow:0 20px 48px rgba(2,6,23,.28)!important;backdrop-filter:blur(8px)}.gym-swal-popup{border-radius:18px!important}.gym-toast-popup{border-radius:12px!important}.gym-swal-title,.gym-toast-title{color:var(--text,#0f172a)!important;font-weight:700!important}.gym-swal-text,.gym-toast-text{color:var(--text-muted,#475569)!important}.gym-swal-confirm,.swal2-styled.gym-swal-confirm{border:none!important;border-radius:999px!important;background:linear-gradient(45deg,var(--accent-sky,#38BDF8),var(--accent,#3B82F6))!important;color:#fff!important;padding:10px 20px!important;font-weight:600!important;box-shadow:0 8px 20px rgba(56,189,248,.34)!important;transition:all .2s ease-in-out!important}.gym-swal-confirm:hover{filter:brightness(1.08)!important;transform:translateY(-1px)!important;box-shadow:0 12px 28px rgba(56,189,248,.45)!important}.gym-swal-cancel,.swal2-styled.gym-swal-cancel{border-radius:999px!important;border:1px solid var(--border,#dbeafe)!important;background:var(--surface-2,#e8f0fe)!important;color:var(--text,#0f172a)!important;padding:10px 20px!important;font-weight:600!important;transition:all .2s ease-in-out!important}.gym-swal-cancel:hover{background:var(--border,#dbeafe)!important;transform:translateY(-1px)!important}.swal2-popup .swal2-actions{gap:10px}.swal2-timer-progress-bar{background:linear-gradient(90deg,var(--accent-sky,#38BDF8),var(--accent,#3B82F6))!important}";
        document.head.appendChild(style);
    }

    window.GymSwal = window.GymSwal || Swal.mixin({
        customClass: {
            container: 'swal-on-top',
            popup: 'gym-swal-popup',
            title: 'gym-swal-title',
            htmlContainer: 'gym-swal-text',
            confirmButton: 'gym-swal-confirm',
            cancelButton: 'gym-swal-cancel'
        },
        buttonsStyling: false,
        confirmButtonText: 'Okay'
    });

    window.GymToast = window.GymToast || Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2400,
        timerProgressBar: true,
        customClass: { 
            container: 'swal-on-top',
            popup: 'gym-toast-popup' 
        }
    });
}

function redirectFUNC(redirectID){
        if(redirectID == 1){
            window.location.href ="?page=login";
        }else if(redirectID ==2){
            window.location.href = "?page=register";
        }else if(redirectID ==3){
            window.location.href = "?page=home";
        }
    }

// DASHBOARD REDIRECTIONS 

function redirectUser(redirectID){
    switch(redirectID){
        case 1:
            window.location.href = "?page=dashboard";
            break;
        case 2:
            window.location.href = "?page=workouts";
            break;
        case 3:
            window.location.href = "?page=exercises";
            break;
        case 4:
            window.location.href = "?page=progress";
            break;
        case 5:
            window.location.href = "?page=goals";

            break;

        case 7:
            window.location.href = "?page=settings";
            break;
        case 8:
            window.location.href = "?page=profile";
            break;
        case 9:
            window.location.href = "?page=calendar";
            break;
        default:
            window.location.href = "?page=dashboard";

        
    }
}

// admin redirects
function redirectAdmin(redirectID){
    switch(redirectID){
        case 1:
            window.location.href = "?page=admindash";
            break;
        case 2:
            window.location.href = "?page=admin_users";
            break;
        case 3:
            window.location.href = "?page=admin_exercises";
            break;
        case 4:
            window.location.href = "?page=admin_reports";
            break;
        case 5:
            window.location.href = "?page=admin_activity";
            break;
        case 6:
            window.location.href = "?page=admin_workouts";
            break;
        default:
            window.location.href = "?page=admindash";
}
}

/* ============================================================
   LOGOUT FUNCTION
============================================================ */
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
