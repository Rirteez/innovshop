import './bootstrap.js';
import './styles/app.scss';
import 'select2';
import 'select2/dist/css/select2.css';


document.addEventListener('DOMContentLoaded', () => {

    /////////////////////////////////////// GESTION DES CAROUSELS DE LA PAGE D'ACCUEIL /////////////////////////////////
    const carousels = document.querySelectorAll('.carousel');

    carousels.forEach((carousel) => {
        const carouselContainer = carousel.querySelector('.carousel-container');
        const items = carousel.querySelectorAll('.carousel-item');
        const prevButton = carousel.querySelector('.carousel-button.prev');
        const nextButton = carousel.querySelector('.carousel-button.next');

        let currentIndex = 0; // Index actuel

        function updateCarousel() {
            const offset = -currentIndex * 100; // Décalage en % par rapport à l'index
            carouselContainer.style.transform = `translateX(${offset}%)`;
        }

        prevButton.addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + items.length) % items.length; // Boucle au dernier élément
            updateCarousel();
        });

        nextButton.addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % items.length; // Boucle au premier élément
            updateCarousel();
        });

        // Initialisation pour assurer que le premier item est bien affiché
        updateCarousel();
    })

    /////////////////////////////////////// GESTION DES FILTRES ET TRIS DU CATALOGUE ///////////////////////////////////
    // Initialisation de Select2
    $('#filterBy').select2({
        placeholder: "Sélectionnez des catégories",
        allowClear: true,
        width: '100%' // Adapte la largeur au conteneur
    });


    //////////////////////////////////// GESTION DE LA PHOTO AFFICHEE SUR LA PAGE IMAGE ////////////////////////////////
    const thumbnails = document.querySelectorAll('.select-image'); // Sélectionne toutes les miniatures
    const mainImage = document.getElementById('mainImage'); // Sélectionne l'image principale

    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', () => {
            // Change la source de l'image principale
            mainImage.src = thumbnail.src;

            // Optionnel : ajoute une classe active pour marquer la miniature sélectionnée
            thumbnails.forEach(t => t.classList.remove('active-thumbnail'));
            thumbnail.classList.add('active-thumbnail');
        });
    });

    /////////////////////////////////////////// TITLE SCROLLING RANDOM ARTICLE  ////////////////////////////////////////
    let isCooldown = false;

    function scrollText() {
        if (!isCooldown) {
            scrollingText.scrollLeft += 1;


            if (scrollingText.scrollLeft >= scrollingText.scrollWidth - scrollingText.clientWidth) {
                isCooldown = true;
                setTimeout(() => {
                    scrollingText.scrollLeft = 0;
                    isCooldown = false;
                    scrollText();
                }, 1000);
            } else {
                // Continue à défiler tant qu'on est pas à la fin
                requestAnimationFrame(scrollText);
            }
        }
    }

// Initialise l'animation
    const scrollingText = document.getElementById('scrollingText');
    scrollText();
});