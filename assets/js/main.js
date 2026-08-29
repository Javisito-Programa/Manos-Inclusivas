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

    // Transparent Header on Scroll
    const header = document.querySelector('header');
    if (header) {
        const isTransparent = header.getAttribute('data-transparent') === 'true';
        if (isTransparent) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            });
        }
    }

    // Hero Image Carousel
    const slides = document.querySelectorAll('.hero-slide');
    if (slides.length > 0) {
        let currentSlide = 0;
        setInterval(() => {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }, 5000); // Change image every 5 seconds
    }
});


// ==========================================
// MAGICAL PARTICLES FOR LOADING SCREEN
// ==========================================
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('loader-canvas');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    let width = canvas.width = window.innerWidth;
    let height = canvas.height = window.innerHeight;
    
    const particles = [];
    const particleCount = 40; // Number of magical orbs
    
    // Create particles
    for (let i = 0; i < particleCount; i++) {
        particles.push({
            x: Math.random() * width,
            y: Math.random() * height,
            radius: Math.random() * 4 + 1,
            vx: (Math.random() - 0.5) * 1.5,
            vy: (Math.random() - 0.5) * 1.5,
            color: ['#6B46C1', '#14B8A6', '#F59E0B', '#FFF'][Math.floor(Math.random() * 4)],
            alpha: Math.random() * 0.5 + 0.1
        });
    }
    
    function draw() {
        ctx.clearRect(0, 0, width, height);
        
        particles.forEach(p => {
            // Move
            p.x += p.vx;
            p.y += p.vy;
            
            // Bounce off edges
            if (p.x < 0 || p.x > width) p.vx *= -1;
            if (p.y < 0 || p.y > height) p.vy *= -1;
            
            // Draw
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
            ctx.fillStyle = p.color;
            ctx.globalAlpha = p.alpha;
            ctx.fill();
            
            // Add a glowing effect to some
            if (p.radius > 3) {
                ctx.shadowBlur = 10;
                ctx.shadowColor = p.color;
            } else {
                ctx.shadowBlur = 0;
            }
        });
        
        ctx.globalAlpha = 1;
        requestAnimationFrame(draw);
    }
    
    draw();
    
    window.addEventListener('resize', () => {
        width = canvas.width = window.innerWidth;
        height = canvas.height = window.innerHeight;
    });
});

// Modal logic moved to noticias.php inline to bypass aggressive cache
