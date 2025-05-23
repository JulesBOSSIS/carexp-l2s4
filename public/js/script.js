
document.addEventListener('DOMContentLoaded', function () {
    const burgerMenu = document.querySelector('.burger-menu');
    const navList = document.querySelector('nav ul');

    burgerMenu.addEventListener('click', function () {
        navList.classList.toggle('active');
    });
});


// Détecter le défilement de la page
$(window).scroll(function() {
    // Si la position de défilement est supérieure à 100 pixels
    if ($(this).scrollTop() > 100) {
        // Ajouter une classe "shrink" au header
        $('header').addClass('shrink');
    } else {
        // Sinon, retirer la classe "shrink" du header
        $('header').removeClass('shrink');
    }
});
