document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('hero-3d-canvas-container');
    if (!container) return;

    function getResponsiveScale() {
        if (window.innerWidth < 480) return 0.55; // Celulares pequeños
        if (window.innerWidth < 768) return 0.7;  // Tablets o celulares grandes
        return 1.0; // Escritorio
    }
    let currentScale = getResponsiveScale();

    // SCENE & CAMERA
    const scene = new THREE.Scene();
    
    const camera = new THREE.PerspectiveCamera(50, container.clientWidth / container.clientHeight, 0.1, 1000);
    camera.position.z = 10;
    camera.position.y = 1;

    const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true, powerPreference: "high-performance" });
    renderer.setSize(container.clientWidth, container.clientHeight);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.setClearColor(0x000000, 0); // Fondo transparente
    renderer.domElement.style.width = '100%';
    renderer.domElement.style.height = '100%';
    container.appendChild(renderer.domElement);

    // ILUMINACIÓN NEUTRA Y BALANCEADA (Respeta los colores originales del modelo)
    const ambientLight = new THREE.AmbientLight(0xffffff, 1.2); 
    scene.add(ambientLight);

    const dirLight = new THREE.DirectionalLight(0xffffff, 1.0);
    dirLight.position.set(5, 5, 5);
    scene.add(dirLight);

    const backLight = new THREE.DirectionalLight(0xffffff, 0.7);
    backLight.position.set(-5, 5, -5);
    scene.add(backLight);

    // PARTICULAS DE FONDO ELIMINADAS

    // GLTF LOADER
    const loader = new THREE.GLTFLoader();
    let brainGroup = new THREE.Group();
    scene.add(brainGroup);

    loader.load(
        'assets/logo.glb',
        (gltf) => {
            const brainModel = gltf.scene;
            
            // Asegurarnos de que los materiales reaccionen bien a la luz
            brainModel.traverse((child) => {
                if (child.isMesh) {
                    if(child.material) {
                        child.material.roughness = 0.4;
                        child.material.metalness = 0.2;
                    }
                }
            });

            // AUTOSCALADO Y CENTRADO DEL MODELO
            // Independientemente de cómo se exportó, esto lo encaja perfectamente en la cámara
            const box = new THREE.Box3().setFromObject(brainModel);
            const size = box.getSize(new THREE.Vector3());
            const center = box.getCenter(new THREE.Vector3());
            const maxAxis = Math.max(size.x, size.y, size.z);
            
            // Escalar para que mida un poco más aprovechando el nuevo espacio
            const targetSize = 7.5;
            brainModel.scale.setScalar(targetSize / maxAxis);
            
            // Centrar el pivote para la rotación
            brainModel.position.x = -center.x * (targetSize / maxAxis);
            brainModel.position.y = -center.y * (targetSize / maxAxis);
            brainModel.position.z = -center.z * (targetSize / maxAxis);

            brainGroup.add(brainModel);
            
            // Ajuste manual para centrar visualmente el logo
            brainGroup.position.x = -0.1;
        },
        (xhr) => {
            console.log((xhr.loaded / xhr.total * 100) + '% loaded');
        },
        (error) => {
            console.error('Error cargando el modelo logo.glb:', error);
            // Mostrar un fallback visual si hay error
            const errGeo = new THREE.BoxGeometry(2,2,2);
            const errMat = new THREE.MeshBasicMaterial({color: 0xff0000, wireframe: true});
            brainGroup.add(new THREE.Mesh(errGeo, errMat));
        }
    );

    // INTERACCIÓN MOUSE
    let mouseX = 0;
    let mouseY = 0;
    container.addEventListener('mousemove', (e) => {
        const r = container.getBoundingClientRect();
        mouseX = ((e.clientX - r.left - (r.width/2)) / r.width) * 2;
        mouseY = ((e.clientY - r.top - (r.height/2)) / r.height) * 2;
    });
    container.addEventListener('mouseleave', () => { mouseX = 0; mouseY = 0; });

    let time = 0;

    function animate() {
        requestAnimationFrame(animate);
        time += 0.015;

        // Rotación de las partículas eliminada

        if (brainGroup) {
            // Inclinación dinámica hacia el mouse (sin rotación completa)
            brainGroup.rotation.y += (mouseX * 0.3 - brainGroup.rotation.y) * 0.05;
            brainGroup.rotation.x += (mouseY * 0.3 - brainGroup.rotation.x) * 0.05;
            
            // Efecto flotante orgánico (levitación más lenta). Offset en Y ajustado más arriba.
            brainGroup.position.y = 0.9 + Math.sin(time * 0.4) * 0.15;

            // Palpitación más lenta y sutil con escala responsiva
            const heartbeat = 1.0 + Math.sin(time * 1.2) * 0.025;
            brainGroup.scale.setScalar(heartbeat * currentScale);
        }

        renderer.render(scene, camera);
    }

    animate();

    window.addEventListener('resize', () => {
        if (!container) return;
        currentScale = getResponsiveScale();
        if (container.clientWidth > 0 && container.clientHeight > 0) {
            camera.aspect = container.clientWidth / container.clientHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(container.clientWidth, container.clientHeight);
        }
    });
});
