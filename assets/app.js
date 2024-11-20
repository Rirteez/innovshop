import './bootstrap.js';
import './styles/app.scss';


document.addEventListener('DOMContentLoaded', () => {
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

    const selects = document.querySelectorAll('form.filter-sort select');const catalog = document.querySelector('.catalog');

    selects.forEach(select => {
        select.addEventListener('change', () => {
            const form = select.closest('form');
            const formData = new FormData(form);

            const params = new URLSearchParams();
            for (const [key, value] of formData.entries()) {
                params.append(key, value);
            }
            window.location.href = `${form.action}?${params.toString()}`;
        })
    })
});
