import './bootstrap.js';
import './styles/app.scss';


document.addEventListener('DOMContentLoaded', () => {
    let currentSlide = 0;
    const slides = document.querySelectorAll('.item-carousel');
    const totalSlides = slides.length;
    const carouselImages = document.querySelector('.carousel-images');

    function updateSlidePosition() {
        const offset = -currentSlide * 100;
        carouselImages.style.transform = `translateX(${offset}%)`;
    }

    function changeSlide(direction) {
        currentSlide = (currentSlide + direction + totalSlides) % totalSlides;
        updateSlidePosition();
    }

    // Ajoute les écouteurs d'événements pour les boutons
    const prevButton = document.querySelector('.carousel-button.prev');
    const nextButton = document.querySelector('.carousel-button.next');

    prevButton.addEventListener('click', () => changeSlide(-1));
    nextButton.addEventListener('click', () => changeSlide(1));
});


