
document.addEventListener('DOMContentLoaded', () => {
    const bubble = document.querySelector('.hero-recursos-content');
    if(!bubble) return;
    
    // Mouse Interaction (PC)
    document.addEventListener('mousemove', (e) => {
        const x = (window.innerWidth / 2 - e.pageX) / 30;
        const y = (window.innerHeight / 2 - e.pageY) / 30;
        
        bubble.style.transform = `translate(${x}px, ${y}px) rotate(${x/10}deg)`;
    });

    // Gyroscope Interaction (Mobile)
    if (window.DeviceOrientationEvent) {
        window.addEventListener('deviceorientation', (e) => {
            if (e.gamma !== null && e.beta !== null) {
                // gamma is left-to-right tilt in degrees, where right is positive
                // beta is front-to-back tilt in degrees, where front is positive
                const x = e.gamma / 2; // adjust sensitivity
                const y = (e.beta - 45) / 2; // assuming device is held at 45 degree angle
                
                bubble.style.transform = `translate(${x}px, ${y}px) rotate(${x/10}deg)`;
            }
        });
    }
});
