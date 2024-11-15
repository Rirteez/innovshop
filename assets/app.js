import './bootstrap.js';
import './styles/app.scss';


document.addEventListener('DOMContentLoaded', () => {
    const carouselContainer = document.querySelector('.carousel-container');
    const items = document.querySelectorAll('.carousel-item');
    const prevButton = document.querySelector('.carousel-button.prev');
    const nextButton = document.querySelector('.carousel-button.next');

    let currentIndex = 0; // Index actuel
    const totalItems = items.length; // Nombre total d'items

    function updateCarousel() {
        const offset = -currentIndex * 100; // Décalage en % par rapport à l'index
        carouselContainer.style.transform = `translateX(${offset}%)`;
    }

    prevButton.addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + totalItems) % totalItems; // Boucle au dernier élément
        updateCarousel();
    });

    nextButton.addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % totalItems; // Boucle au premier élément
        updateCarousel();
    });

    // Initialisation pour assurer que le premier item est bien affiché
    updateCarousel();
});
