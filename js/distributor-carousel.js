document.addEventListener('DOMContentLoaded', function () {
    const carousel = document.getElementById('distributor-carousel');
    const logos = carousel.querySelectorAll('img');
    const visibleCount = 7; // Number of logos visible at once (should be odd for a center)
    let currentIndex = 0;
    let total = logos.length;

    // Clone first N logos to the end for infinite effect
    for (let i = 0; i < visibleCount; i++) {
        const clone = logos[i].cloneNode(true);
        carousel.appendChild(clone);
    }

    function updateLogoClasses() {
        const allLogos = carousel.querySelectorAll('img');
        allLogos.forEach((logo, i) => {
            logo.classList.remove('center-logo', 'side-logo', 'far-logo', 'fade-logo');
            // Calculate position relative to center
            const center = currentIndex + Math.floor(visibleCount / 2);
            if (i === center) {
                logo.classList.add('center-logo');
            } else if (i === center - 1 || i === center + 1) {
                logo.classList.add('side-logo');
            } else if (i === center - 2 || i === center + 2) {
                logo.classList.add('far-logo');
            } else {
                logo.classList.add('fade-logo');
            }
        });
    }

    function slide() {
        currentIndex++;
        carousel.style.transition = 'transform 0.8s cubic-bezier(.7,0,.3,1)';
        carousel.style.transform = `translateX(-${currentIndex * (94)}px)`;
        updateLogoClasses();

        // Reset to start for infinite loop
        if (currentIndex >= total) {
            setTimeout(() => {
                carousel.style.transition = 'none';
                carousel.style.transform = 'translateX(0)';
                currentIndex = 0;
                updateLogoClasses();
            }, 850);
        }
    }

    // Initial state
    updateLogoClasses();

    setInterval(slide, 4000);
});