 // THIS SCRIPT IS MORE ON THE FRONTEND SIDE OF THE PROGRAM 

function unhidepassFUNC(element){
    const targetId = element.getAttribute('data-target');
    const targetInput = document.getElementById(targetId);
    
    if(targetInput.type === "password"){
        targetInput.type = "text";
        element.textContent ="visibility_off";
        element.classList.add("toggled");
    }else{
        targetInput.type = "password";
        element.textContent="visibility";
        element.classList.remove("toggled");

    }
}



function toggleselectgender(select) {
    const otherInput = document.getElementById('othergender');
    if (select.value === 'other') {
        otherInput.style.display = 'inline-block'; // show input
        otherInput.required = true; // make it required
    } else {
        otherInput.style.display = 'none'; // hide input
        otherInput.value = ''; // clear input
        otherInput.required = false; // remove required
    }
}
function getotherGender(){
    var genderoutput =document.getElementById('othergender');
    return genderoutput.value;
}
// intro loader timer

window.addEventListener("load", () => {
    const name = document.querySelector(".siteName");
    const intro = document.querySelector(".intro-loader");

    if(!name || !intro) return;

    setTimeout(() => {
        name.style.opacity = '1';
        name.style.transform = "translateY(0)";
    }, 300);

    setTimeout(() => {
        intro.style.top = "-100%";
   }, 2000);
});
// Mobile menu toggle
document.querySelector('.hamburger').addEventListener('click', function() {
    document.querySelector('.nav-links').classList.toggle('active');
});


 /* Particles */
        const container = document.getElementById('particles');
        for (let i = 0; i < 20; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            p.style.cssText = `
                left: ${Math.random() * 100}%;
                animation-delay: ${Math.random() * 20}s;
                animation-duration: ${15 + Math.random() * 15}s;
                width: ${2 + Math.random() * 4}px;
                height: ${2 + Math.random() * 4}px;
                opacity: ${0.3 + Math.random() * 0.5};
            `;
            container.appendChild(p);
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