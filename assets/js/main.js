document.addEventListener('DOMContentLoaded', () => {
    // Mobile Menu Toggle
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');

    if (hamburger && navLinks) {
        hamburger.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            hamburger.textContent = navLinks.classList.contains('active') ? '✕' : '☰';
        });
    }

    // Accessibility Widget Toggle
    const a11yBtn = document.getElementById('a11y-btn');
    const a11yMenu = document.getElementById('a11y-menu');

    if (a11yBtn && a11yMenu) {
        a11yBtn.addEventListener('click', () => {
            a11yMenu.classList.toggle('active');
        });
    }

    // High Contrast Mode Toggle
    const contrastBtn = document.getElementById('toggle-contrast');
    if (contrastBtn) {
        contrastBtn.addEventListener('click', () => {
            document.body.classList.toggle('high-contrast');
            const isHighContrast = document.body.classList.contains('high-contrast');
            localStorage.setItem('highContrast', isHighContrast);
        });

        // Check local storage for preference
        if (localStorage.getItem('highContrast') === 'true') {
            document.body.classList.add('high-contrast');
        }
    }

    // Text Size Adjustment
    const increaseTextBtn = document.getElementById('increase-text');
    const decreaseTextBtn = document.getElementById('decrease-text');
    let currentSizeLevel = 0; // 0: normal, 1: large, 2: xlarge

    if (increaseTextBtn && decreaseTextBtn) {
        increaseTextBtn.addEventListener('click', () => {
            if (currentSizeLevel < 2) {
                currentSizeLevel++;
                updateTextSize();
            }
        });

        decreaseTextBtn.addEventListener('click', () => {
            if (currentSizeLevel > 0) {
                currentSizeLevel--;
                updateTextSize();
            }
        });
    }

    function updateTextSize() {
        document.body.classList.remove('text-large', 'text-xlarge');
        if (currentSizeLevel === 1) document.body.classList.add('text-large');
        if (currentSizeLevel === 2) document.body.classList.add('text-xlarge');
    }
});
