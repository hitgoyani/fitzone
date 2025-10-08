document.addEventListener("DOMContentLoaded", function () {
    let sliderImages = document.querySelectorAll(".slide"),
        arrowLeft = document.querySelector("#arrow-left"),
        arrowRight = document.querySelector("#arrow-right"),
        current = 0;

    if (sliderImages.length > 0) {
        function reset() {
            sliderImages.forEach(img => (img.style.display = "none"));
        }

        function startSlide() {
            reset();
            sliderImages[0].style.display = "block";
        }

        function slideLeft() {
            reset();
            current = (current - 1 + sliderImages.length) % sliderImages.length;
            sliderImages[current].style.display = "block";
        }

        function slideRight() {
            reset();
            current = (current + 1) % sliderImages.length;
            sliderImages[current].style.display = "block";
        }

        arrowLeft?.addEventListener("click", slideLeft);
        arrowRight?.addEventListener("click", slideRight);

        startSlide();
    }

    // Popup
    const popup = document.getElementById('popup');
    const closeBtn = document.getElementById('closePopup');

    if (popup && closeBtn) {
        setTimeout(() => {
            popup.style.display = 'flex';
        }, 5000);

        closeBtn.addEventListener('click', () => {
            popup.style.display = 'none';
        });

        window.addEventListener('click', (e) => {
            if (e.target === popup) popup.style.display = 'none';
        });
    }

    // Dark/Light Mode Toggle
    const modeToggle = document.getElementById('modeToggle');
    const body = document.body;

    if (modeToggle) {
        modeToggle.addEventListener('click', () => {
            body.classList.toggle('light-mode');
        });
    }
});
