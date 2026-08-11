document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('hero-3d-canvas-container');
    if (!container) return;

    // SCENE & CAMERA
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(60, container.clientWidth / container.clientHeight, 0.1, 1000);
    camera.position.z = 28; // Posición ideal para ver el cerebro completo
    camera.position.y = 5;  // Mirando ligeramente desde arriba

    const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true, powerPreference: "high-performance" });
    renderer.setSize(container.clientWidth, container.clientHeight);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.setClearColor(0x000000, 0); 
    renderer.domElement.style.width = '100%';
    renderer.domElement.style.height = '100%';
    container.appendChild(renderer.domElement);

    // TEXTURA DE PARTÍCULA (Suave resplandor)
    const createParticleTexture = () => {
        const canvas = document.createElement('canvas');
        canvas.width = 32; canvas.height = 32;
        const ctx = canvas.getContext('2d');
        const gradient = ctx.createRadialGradient(16, 16, 0, 16, 16, 16);
        gradient.addColorStop(0, `rgba(255, 255, 255, 1)`);
        gradient.addColorStop(0.2, `rgba(0, 229, 255, 0.8)`);
        gradient.addColorStop(1, 'rgba(0,0,0,0)');
        ctx.fillStyle = gradient;
        ctx.fillRect(0,0,32,32);
        return new THREE.CanvasTexture(canvas);
    };
    const particleTexture = createParticleTexture();

    const group = new THREE.Group();
    scene.add(group);

    // ==========================================
    // GENERADOR PARAMÉTRICO DE CEREBRO 3D
    // ==========================================
    const particlePositions = [];
    const particleColors = [];
    const connectionPoints = []; // Para las líneas
    
    const colorCyan = new THREE.Color(0x00E5FF);
    const colorPurple = new THREE.Color(0x9575CD);
    const colorPink = new THREE.Color(0xFF4081);

    const numParticles = 6000;

    for(let i = 0; i < numParticles; i++) {
        // Coordenadas esféricas aleatorias
        let u = Math.random();
        let v = Math.random();
        let theta = u * 2.0 * Math.PI; 
        let phi = Math.acos(2.0 * v - 1.0); 
        
        // Vector unitario
        let nx = Math.sin(phi) * Math.cos(theta);
        let ny = Math.cos(phi);
        let nz = Math.sin(phi) * Math.sin(theta);
        
        // Dimensiones base del cerebro (Largo en Z, Ancho en X)
        let x = nx * 9.5;
        let y = ny * 7.5;
        let z = nz * 11.5;
        
        // 1. Fisura Longitudinal (El hueco que separa los hemisferios derecho e izquierdo)
        // Aplicamos el hundimiento principalmente en la parte superior (ny > 0)
        let fissure = Math.exp(-Math.pow(nx * 5, 2)); // 1 cerca de x=0, cae rápido
        if (ny > 0) {
            y -= fissure * 3.5;
        } else {
            // Aplanar ligeramente la base del cerebro
            y += Math.pow(Math.abs(ny), 2) * 2;
        }
        
        // 2. Afinar la parte frontal (Lóbulo frontal)
        if (z > 0) {
            x *= (1 - nz * 0.25);
            y *= (1 - nz * 0.15);
        }
        
        // 3. Circunvoluciones (Los pliegues característicos del cerebro)
        let folds = (Math.sin(nx * 20) * Math.sin(ny * 22) * Math.sin(nz * 20)) * 0.6;
        x += nx * folds;
        y += ny * folds;
        z += nz * folds;
        
        // 4. Grosor de la corteza (dispersión)
        let depth = (Math.random() - 0.5) * 1.5;
        x += nx * depth;
        y += ny * depth;
        z += nz * depth;

        particlePositions.push(x, y, z);
        
        // Guardar algunos puntos para dibujar las redes
        if(i % 15 === 0) {
            connectionPoints.push(new THREE.Vector3(x, y, z));
        }

        // Colores: Mezcla de Cian de la marca y tonos morados
        let mix = colorCyan.clone().lerp(colorPurple, Math.random());
        // Destellos rosas ocasionales
        if(Math.random() < 0.05) mix = colorPink;
        
        particleColors.push(mix.r, mix.g, mix.b);
    }

    // Mesh del Cerebro
    const geo = new THREE.BufferGeometry();
    geo.setAttribute('position', new THREE.Float32BufferAttribute(particlePositions, 3));
    geo.setAttribute('color', new THREE.Float32BufferAttribute(particleColors, 3));
    
    const mat = new THREE.PointsMaterial({
        size: 0.35, 
        vertexColors: true,
        map: particleTexture,
        transparent: true,
        blending: THREE.AdditiveBlending, 
        depthWrite: false,
        opacity: 0.8
    });
    
    const brainMesh = new THREE.Points(geo, mat);
    group.add(brainMesh);

    // ==========================================
    // REDES NEURONALES (Líneas conectando la corteza)
    // ==========================================
    const linePositions = [];
    const maxDist = 2.5;

    // Conectar puntos cercanos para darle aspecto de 'Red Neuronal'
    for(let i=0; i<connectionPoints.length; i++) {
        let connected = 0;
        for(let j=i+1; j<connectionPoints.length; j++) {
            if(connected > 3) break; // Máximo 3 conexiones por nodo
            
            let d = connectionPoints[i].distanceTo(connectionPoints[j]);
            if(d < maxDist) {
                linePositions.push(
                    connectionPoints[i].x, connectionPoints[i].y, connectionPoints[i].z,
                    connectionPoints[j].x, connectionPoints[j].y, connectionPoints[j].z
                );
                connected++;
            }
        }
    }

    const lineGeo = new THREE.BufferGeometry();
    lineGeo.setAttribute('position', new THREE.Float32BufferAttribute(linePositions, 3));
    const lineMat = new THREE.LineBasicMaterial({
        color: 0x00E5FF,
        transparent: true,
        opacity: 0.15,
        blending: THREE.AdditiveBlending,
        depthWrite: false
    });
    const brainLines = new THREE.LineSegments(lineGeo, lineMat);
    group.add(brainLines);

    // ==========================================
    // ANIMACIÓN Y CÁMARA
    // ==========================================
    let time = 0;
    let mouseX = 0, mouseY = 0;

    container.addEventListener('mousemove', (e) => {
        const r = container.getBoundingClientRect();
        mouseX = ((e.clientX - r.left - (r.width/2)) / r.width) * 2;
        mouseY = ((e.clientY - r.top - (r.height/2)) / r.height) * 2;
    });
    container.addEventListener('mouseleave', () => { mouseX = 0; mouseY = 0; });

    function animate() {
        requestAnimationFrame(animate);
        time += 0.01;

        // Rotación general 
        group.rotation.y += (mouseX * 0.3 - group.rotation.y) * 0.05 + 0.005; // Gira constantemente
        group.rotation.x += ((mouseY * 0.3) - group.rotation.x) * 0.05;

        // Pequeño latido simulando actividad cerebral
        const scale = 1.0 + Math.sin(time * 3) * 0.01;
        group.scale.set(scale, scale, scale);

        renderer.render(scene, camera);
    }

    animate();

    window.addEventListener('resize', () => {
        if (!container) return;
        if (container.clientWidth > 0 && container.clientHeight > 0) {
            camera.aspect = container.clientWidth / container.clientHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(container.clientWidth, container.clientHeight);
        }
    });
});
