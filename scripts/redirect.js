//THIS SCRIPT FOCUCES ONLY ON REDIRECTING

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
        default:
            window.location.href = "?page=admindash";
}
}
