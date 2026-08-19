document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('loader-canvas');
    if (!canvas) return; // Exit if no loader canvas (e.g. on pages without loader)

    const ctx = canvas.getContext('2d');
    const loaderWrapper = document.getElementById('loader-wrapper');
    const loaderText = document.querySelector('.loader-text');
    
    // Set canvas to full screen
    let width, height;
    function resize() {
        width = canvas.width = window.innerWidth;
        height = canvas.height = window.innerHeight;
    }
    window.addEventListener('resize', resize);
    resize();

    // Node colors from strict palette
    const colors = ['#9575CD', '#81C784', '#E57373', '#64B5F6'];
    const particles = [];
    const numParticles = Math.min(120, Math.floor((width * height) / 8000));
    let isCollapsing = false;

    class Particle {
        constructor() {
            this.x = Math.random() * width;
            this.y = Math.random() * height;
            this.vx = (Math.random() - 0.5) * 0.6;
            this.vy = (Math.random() - 0.5) * 0.6;
            this.radius = Math.random() * 4.3 + 1.4;
            this.color = colors[Math.floor(Math.random() * colors.length)];
            this.targetX = width / 2; // Center for collapse
            this.targetY = height / 2;
        }

        update() {
            this.x += this.vx;
            this.y += this.vy;

            // Bounce off edges
            if (this.x < 0 || this.x > width) this.vx = -this.vx;
            if (this.y < 0 || this.y > height) this.vy = -this.vy;
        }

        draw() {
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
            ctx.fillStyle = this.color;
            ctx.shadowBlur = 10;
            ctx.shadowColor = this.color;
            ctx.fill();
            ctx.shadowBlur = 0; // reset
        }
    }

    // Initialize particles
    for (let i = 0; i < numParticles; i++) {
        particles.push(new Particle());
    }

    // Draw lines between close particles
    function connectParticles() {
        for (let i = 0; i < particles.length; i++) {
            for (let j = i + 1; j < particles.length; j++) {
                const p1 = particles[i];
                const p2 = particles[j];
                const dx = p1.x - p2.x;
                const dy = p1.y - p2.y;
                const distance = Math.sqrt(dx * dx + dy * dy);

                if (distance < 175) {
                    ctx.beginPath();
                    ctx.moveTo(p1.x, p1.y);
                    ctx.lineTo(p2.x, p2.y);
                    // Line opacity based on distance
                    const opacity = 1 - (distance / 175);
                    ctx.strokeStyle = `rgba(149, 117, 205, ${opacity * 0.5})`; // Using purple as base line color
                    ctx.lineWidth = 1.5;
                    ctx.stroke();
                }
            }
        }
    }

    let animationFrameId;
    function animate() {
        ctx.clearRect(0, 0, width, height);
        
        particles.forEach(p => {
            p.update();
            p.draw();
        });
        
        connectParticles();
        animationFrameId = requestAnimationFrame(animate);
    }

    // Start animation
    animate();

    // Determinar el tiempo de carga basado en la página actual
    const isHomePage = window.location.pathname.endsWith('index.html') || window.location.pathname === '/' || window.location.pathname.endsWith('Fundacion/');
    const loadTime = isHomePage ? 2500 : 1200;

    // Trigger fade out and load after a delay (simulating page load)
    // In a real app, this would be tied to window.onload or specific fetch promises
    setTimeout(() => {
        document.body.classList.add('loaded'); // CSS transitions handle the fade out
        
        // Remove DOM elements after fade out (matches the 0.8s CSS transition time)
        setTimeout(() => {
            cancelAnimationFrame(animationFrameId);
            loaderWrapper.remove();
        }, 800);
    }, loadTime);
});
