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

// ==========================================
// NOTICIAS MODAL LOGIC
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('news-modal');
    if (!modal) return; // Si no estamos en la página de noticias, salir

    var closeBtn = document.querySelector('.close-modal');
    var prevBtn = document.getElementById('carousel-prev');
    var nextBtn = document.getElementById('carousel-next');
    var dotsContainer = document.getElementById('carousel-dots');
    
    var currentImages = [];
    var currentIndex = 0;

    function updateCarousel() {
        if (currentImages.length === 0) return;
        var modalImg = document.getElementById('modal-img');
        if (modalImg) modalImg.src = currentImages[currentIndex];

        if (dotsContainer) {
            var dots = dotsContainer.children;
            for (var j = 0; j < dots.length; j++) {
                dots[j].style.background = (j === currentIndex) ? 'var(--accent-purple)' : 'rgba(255,255,255,0.5)';
            }
        }
    }

    // Usar event delegation en el body para detectar clics en los botones de "Leer más"
    // Esto asegura que funcione incluso si los botones se cargan dinámicamente o si hay problemas con querySelectorAll
    document.body.addEventListener('click', function(e) {
        var btn = null;
        
        // Buscar si el elemento clicado o alguno de sus padres es el botón
        var el = e.target;
        while (el && el !== document.body) {
            if (el.classList && el.classList.contains('news-read-more')) {
                btn = el;
                break;
            }
            el = el.parentNode;
        }

        if (btn) {
            // No usamos e.preventDefault() porque el botón no es un enlace <a> sino un <button type="button">
            
            var title = btn.getAttribute('data-title') || '';
            var date = btn.getAttribute('data-date') || '';
            var img = btn.getAttribute('data-img') || '';
            var content = btn.getAttribute('data-content') || '';
            var extrasRaw = btn.getAttribute('data-extras') || '[]';

            var modalTitle = document.getElementById('modal-title');
            var modalDate = document.getElementById('modal-date');
            var modalText = document.getElementById('modal-text');

            if (modalTitle) modalTitle.textContent = title;
            if (modalDate) modalDate.textContent = date;
            if (modalText) modalText.innerHTML = content;

            // Setup Images
            currentImages = [img];
            try {
                var extras = JSON.parse(extrasRaw);
                // Forma segura de chequear arreglos en navegadores muy antiguos
                if (Object.prototype.toString.call(extras) === '[object Array]') {
                    for (var k = 0; k < extras.length; k++) {
                        currentImages.push(extras[k]);
                    }
                }
            } catch (err) {
                // Ignorar error de JSON silenciosamente
            }

            currentIndex = 0;
            if (dotsContainer) dotsContainer.innerHTML = '';

            if (currentImages.length > 1) {
                if (prevBtn) prevBtn.style.display = 'block';
                if (nextBtn) nextBtn.style.display = 'block';

                if (dotsContainer) {
                    for (var idx = 0; idx < currentImages.length; idx++) {
                        (function (dotIndex) {
                            var dot = document.createElement('div');
                            dot.style.width = '10px';
                            dot.style.height = '10px';
                            dot.style.borderRadius = '50%';
                            dot.style.cursor = 'pointer';
                            dot.onclick = function () {
                                currentIndex = dotIndex;
                                updateCarousel();
                            };
                            dotsContainer.appendChild(dot);
                        })(idx);
                    }
                }
            } else {
                if (prevBtn) prevBtn.style.display = 'none';
                if (nextBtn) nextBtn.style.display = 'none';
            }

            updateCarousel();
            modal.style.display = "block";
            // Nota importante: NO usar document.body.style.overflow = "hidden" porque en Safari iOS causa que la página salte al inicio.
        }
    });

    if (prevBtn) {
        prevBtn.onclick = function(e) {
            e.preventDefault();
            currentIndex = (currentIndex > 0) ? currentIndex - 1 : currentImages.length - 1;
            updateCarousel();
        };
    }

    if (nextBtn) {
        nextBtn.onclick = function(e) {
            e.preventDefault();
            currentIndex = (currentIndex < currentImages.length - 1) ? currentIndex + 1 : 0;
            updateCarousel();
        };
    }

    if (closeBtn) {
        closeBtn.onclick = function() {
            modal.style.display = "none";
        };
    }

    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    };
});
