<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title class="notranslate" translate="no">Manos Inclusivas - Iluminando Corazones A.C.</title>
    <meta name="keywords" content="Fundaciones en yucatan, Fundaciones en merida, Iluminando corazones, Manos inclusivas, autismo, neurodesarrollo, terapia infantil, fundacion sin fines de lucro, donaciones yucatan">
    <meta property="og:title" content="Manos Inclusivas - Iluminando Corazones A.C.">
    <meta property="og:description" content="Fundación sin fines de lucro en Mérida, Yucatán, dedicada al neurodesarrollo y bienestar emocional.">
    <meta property="og:image" content="https://miic-neurodesarrollo.org/img/Logo%20circular.webp">
    <link rel="icon" type="image/png" href="img/Logo circular.webp">
    <link rel="apple-touch-icon" href="img/Logo circular.webp">

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style-v2.css?v=24">
    <link rel="stylesheet" href="assets/css/loading.css?v=22">
    
    
    <style id='cache-busted-styles-noticias-v5'>
        /* ANIMATED WAVES HERO NOTICIAS */
        .news-hero {
            position: relative;
            padding: 160px 20px 220px;
            text-align: center;
            background: url('img/Noticias.webp') center center / cover no-repeat;
            margin-bottom: 50px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Dark overlay with animated gradient */
        .news-hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(45deg, rgba(30, 41, 59, 0.9), rgba(79, 70, 229, 0.8), rgba(30, 41, 59, 0.9));
            background-size: 300% 300%;
            animation: auroraShift 45s ease-in-out infinite;
            z-index: 1;
        }

        @keyframes auroraShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .news-hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
        }

        .news-hero h1 {
            font-size: 4.5rem;
            color: #ffffff;
            font-weight: 800;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 4px 15px rgba(0,0,0,0.4);
        }

        
            to { text-shadow: 0 0 20px rgba(255,255,255,0.6), 0 0 30px rgba(236, 72, 153, 0.8); }
        }

        .news-hero p {
            font-size: 1.4rem;
            color: #ffffff !important; text-shadow: 0 2px 5px rgba(0,0,0,0.8);
            line-height: 1.6;
            text-shadow: 0 2px 5px rgba(0,0,0,0.5);
            border-left: 4px solid var(--accent-pink);
            padding-left: 20px;
            text-align: left;
            display: inline-block;
            background: rgba(0,0,0,0.3);
            padding: 15px 25px;
            border-radius: 0 20px 20px 0;
        }

        /* Animated SVG Waves */
        .waves-container {
            position: absolute;
            bottom: -5px; /* slight overlap to prevent gaps */
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
            z-index: 3;
        }

        .waves-container svg {
            position: relative;
            display: block;
            width: calc(200% + 1.3px);
            height: 120px;
            transform: translateX(0);
        }

        .wave-back {
            fill: rgba(248, 249, 250, 0.5); /* Semi-transparent beige/white */
            animation: waveAnimate 40s linear infinite;
        }

        .wave-front {
            fill: #fdfbf7; /* Matches bg-primary */
            animation: waveAnimate 30s linear infinite reverse;
        }

        @keyframes waveAnimate {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        @media (max-width: 768px) {
            .news-hero { padding: 100px 15px 100px; }
            .news-hero h1 { font-size: 2.2rem; margin-bottom: 15px; }
            .news-hero p { 
                font-size: 1.05rem; 
                padding: 15px; 
                border-left: none;
                border-top: 4px solid var(--accent-pink);
                border-radius: 0 0 15px 15px;
                display: block;
                text-align: center;
            }
            .waves-container svg { height: 50px; }
        }
.news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            padding: 60px 0;
        }

        .news-card {
            background: white;
            border-radius: var(--border-radius-soft);
            overflow: hidden;
            box-shadow: var(--shadow-soft);
            transition: var(--transition-fast);
            display: flex;
            flex-direction: column;
        }

        .news-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-hover);
        }

        .news-image {
            width: 100%;
            height: 220px;
            object-fit: contain;
            background-color: var(--bg-tertiary);
            padding: 10px; /* Optional: adds a little breathing room inside the box */
        }

        .news-content {
            padding: 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .news-date {
            font-size: 0.85rem;
            color: var(--accent-purple);
            font-weight: 600;
            margin-bottom: 10px;
            display: block;
        }

        .news-title {
            font-size: 1.3rem;
            color: var(--text-main);
            margin-bottom: 15px;
            line-height: 1.4;
        }

        .news-excerpt {
            color: #555;
            font-size: 0.95rem;
            margin-bottom: 20px;
            flex-grow: 1;
        }

        .news-read-more {
            color: var(--accent-purple);
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .news-read-more:hover {
            color: var(--text-main);
        }


        .news-social-icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all var(--transition-fast, 0.3s ease);
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .news-social-icon:hover {
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }

        /* Modal for full news */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.6);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 0;
            border-radius: var(--border-radius-soft);
            width: 90%;
            max-width: 800px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            position: relative;
            animation: modalFadeIn 0.3s;
            overflow: hidden;
        }

        @keyframes modalFadeIn {
            from {opacity: 0; transform: translateY(-20px);}
            to {opacity: 1; transform: translateY(0);}
        }

        .close-modal {
            position: absolute;
            top: 15px;
            right: 25px;
            color: white;
            font-size: 30px;
            font-weight: bold;
            cursor: pointer;
            z-index: 10;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }

        .close-modal:hover {
            color: #ddd;
        }

        .modal-image {
            width: 100%;
            height: auto;
            max-height: 400px;
            object-fit: contain;
            background-color: var(--bg-tertiary);
            padding: 10px;
        }

        .modal-body {
            padding: 40px;
        }

        .modal-date {
            color: var(--accent-purple);
            font-weight: 600;
            margin-bottom: 10px;
            display: block;
        }

        .modal-title {
            font-size: 2rem;
            color: var(--text-main);
            margin-bottom: 20px;
        }

        .modal-text {
            color: #444;
            line-height: 1.8;
            white-space: pre-line;
        }

        .empty-state {
            text-align: center;
            padding: 50px;
            color: #666;
            grid-column: 1 / -1;
        }
    </style>
    <!-- Favicon (Icono de la pestaña) -->
    <link rel="icon" type="image/png" href="img/Logo circular.webp">
    <!-- Model Viewer for 3D elements -->
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/4.0.0/model-viewer.min.js"></script>
</head>

<body>

    <!-- ========================================== -->
    <!-- PANTALLA DE CARGA (LOADING SCREEN)         -->
    <!-- ========================================== -->
    <div id="loader-wrapper">
        <canvas id="loader-canvas"></canvas>
        <div class="loader-content">
            <div class="loader-logo-container">
                <model-viewer src="assets/logo.glb" auto-rotate auto-rotate-delay="0" rotation-per-second="120deg" camera-orbit="0deg 90deg auto" interaction-prompt="none" disable-zoom disable-pan class="loader-3d-logo"></model-viewer>
            </div>
            <h2 class="loader-text">Cargando Noticias...</h2>
        </div>
    </div>

    <!-- Header & Navigation -->
    <!-- ========================================== -->
    <!-- ENCABEZADO Y NAVEGACIÓN (HEADER & NAV)     -->
    <!-- ========================================== -->
    <header>

        <div class="container navbar">
            <a href="index.html" class="logo-container">
                <model-viewer id="nav-3d-logo" src="assets/logo.glb" alt="Logo Manos Inclusivas" class="notranslate" translate="no"
                    camera-orbit="0deg 90deg auto" interaction-prompt="none" disable-zoom></model-viewer>
                <div class="logo-text notranslate" translate="no">
                    <h2 class="notranslate" translate="no">Manos Inclusivas</h2>
                    <span>Iluminando Corazones A.C.</span>
                </div>
            </a>

            <button class="hamburger" aria-label="Abrir menú">☰</button>

            <nav class="nav-links">
                <div class="nav-item"><a href="index.html" class="nav-link"
                        >INICIO</a>
                </div>
                <div class="nav-item">
                    <a href="nosotros.html" class="nav-link">NOSOTROS</a>
                    <ul class="dropdown-menu">
                        <li><a href="nosotros.html#historia" class="dropdown-item">Nuestra Historia</a></li>
                        <li><a href="nosotros.html#misión" class="dropdown-item">Misión y Visión</a></li>
                        <li><a href="nosotros.html#valores" class="dropdown-item">Valores</a></li>
                        <li><a href="nosotros.html#equipo" class="dropdown-item">Nuestro Equipo</a></li>
                    </ul>
                </div>
                <div class="nav-item">
                    <a href="servicios.html" class="nav-link">SERVICIOS</a>
                    <ul class="dropdown-menu">
                        <li><a href="servicios.html#psicologia" class="dropdown-item">Psicología</a></li>
                        <li><a href="servicios.html#psiquiatria" class="dropdown-item">Psiquiatría</a></li>
                        <li><a href="servicios.html#terapia-lenguaje" class="dropdown-item">Terapia de Lenguaje</a></li>
                        <li><a href="servicios.html#terapia-ocupacional" class="dropdown-item">Terapia Ocupacional</a></li>
                        <li><a href="servicios.html#integracion-sensorial" class="dropdown-item">Integración Sensorial</a></li>
                        <li><a href="servicios.html#hidroterapia" class="dropdown-item">Hidroterapia</a></li>
                        <li><a href="servicios.html#nutricion" class="dropdown-item">Nutrición</a></li>
                        <li><a href="servicios.html#mindfullness" class="dropdown-item">Mindfullness y Aromaterapia</a></li>
                        <li><a href="servicios.html#talleres" class="dropdown-item">Talleres y Capacitaciones</a></li>
                    </ul>
                </div>
                <div class="nav-item">
                    <a href="recursos.html" class="nav-link">RECURSOS</a>
                    <ul class="dropdown-menu">
                        <li><a href="recursos.html#tea" class="dropdown-item">Autismo (TEA)</a></li>
                        <li><a href="recursos.html#tdah" class="dropdown-item">TDAH</a></li>
                        <li><a href="recursos.html#di" class="dropdown-item">Discapacidad Intelectual</a></li>
                        <li><a href="recursos.html#teap" class="dropdown-item">Trastornos del Aprendizaje</a></li>
                        <li><a href="recursos.html#dispraxia" class="dropdown-item">Dispraxia</a></li>
                        <li><a href="recursos.html#lenguaje" class="dropdown-item">Trastornos del Lenguaje</a></li>
                        <li><a href="recursos.html#altas-capacidades" class="dropdown-item">Altas Capacidades</a></li>
                    </ul>
                </div>
                <div class="nav-item"><a href="noticias.php" class="nav-link" style="color: var(--accent-purple);">NOTICIAS</a></div>
                <div class="nav-item"><a href="citas.html" class="nav-link">CITAS</a></div>
                <div class="nav-item"><a href="contacto.html" class="nav-link">CONTACTO</a></div>
                <div class="nav-item"><a href="donar.html" class="btn btn-donate">DONAR</a></div>
            
                <!-- Translate Widget -->
                <div class="nav-item custom-lang-selector">
                    <div class="lang-btn" id="lang-btn">
                        <span class="icon">🌍</span>
                        <span id="current-lang-text">Español</span>
                        <span class="arrow">▼</span>
                    </div>
                    <ul class="lang-dropdown" id="lang-dropdown">
                        <li data-lang="es" class="active">Español</li>
                        <li data-lang="en">English</li>
                        <li data-lang="fr">Français</li>
                        <li data-lang="de">Deutsch</li>
                        <li data-lang="it">Italiano</li>
                        <li data-lang="pt">Português</li>
                        <li data-lang="ja">日本語</li>
                        <li data-lang="zh-CN">中文</li>
                    </ul>
                    <div id="google_translate_element" style="display:none;"></div>
                </div>
            </nav>
        </div>

    </header>

    <main class="bg-primary">
        
        
        <section class="news-hero">
            <div class="news-hero-content">
                <h1>Tablón de Noticias</h1>
                <p style="color: #ffffff !important; text-shadow: 0 2px 5px rgba(0,0,0,0.8);">Mantente al día con nuestros próximos eventos, talleres, actividades y novedades de la fundación.</p>
            </div>
            
            <!-- Animated Waves -->
            <div class="waves-container">
                <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
                    <!-- Back Wave -->
                    <path class="wave-back" d="M0,60 C300,120 300,0 600,60 C900,120 900,0 1200,60 C1500,120 1500,0 1800,60 C2100,120 2100,0 2400,60 L2400,120 L0,120 Z"></path>
                    <!-- Front Wave -->
                    <path class="wave-front" d="M0,80 C300,20 300,140 600,80 C900,20 900,140 1200,80 C1500,20 1500,140 1800,80 C2100,20 2100,140 2400,80 L2400,120 L0,120 Z"></path>
                </svg>
            </div>
        </section>



        <section class="container">
            <div class="news-grid" id="news-container">
                <?php
                // Conectar a la base de datos
                require_once 'admin/config/database.php';
                date_default_timezone_set('America/Mexico_City');
                
                if (isset($pdo)) {
                    // Ordenar por fijadas primero, luego por orden manual (pinned_order), luego por fecha de publicación
                    $stmt = $pdo->query("SELECT * FROM noticias ORDER BY is_pinned DESC, pinned_order ASC, fecha_publicacion DESC, id DESC");
                    $noticias = $stmt->fetchAll();
                    
                    if (count($noticias) > 0) {
                        foreach ($noticias as $index => $news) {
                            // Usar fecha_publicacion si existe, si no usar created_at
                            $raw_date = !empty($news['fecha_publicacion']) ? $news['fecha_publicacion'] : $news['created_at'];
                            $fecha = date('d/m/Y', strtotime($raw_date));
                            
                            // Fix path for portada
                            $imgPath = $news['imagen_path'];
                            if (!empty($imgPath) && strpos($imgPath, 'uploads/') === 0) {
                                $imgPath = 'admin/' . $imgPath;
                            }
                            $imgUrl = !empty($imgPath) ? htmlspecialchars($imgPath) : 'img/Logo circular.webp';
                            
                            $excerpt = mb_strlen($news['contenido']) > 120 ? mb_substr($news['contenido'], 0, 120) . '...' : $news['contenido'];
                            
                            // Highlight para noticias fijadas
                            $pinnedStyle = $news['is_pinned'] ? 'border-top: 4px solid var(--accent-purple);' : '';
                            
                            echo '<article class="news-card" style="'.$pinnedStyle.'">';
                            echo '<img src="' . $imgUrl . '" alt="' . htmlspecialchars($news['titulo']) . '" class="news-image" onerror="this.src=\'img/Logo circular.webp\'">';
                            echo '<div class="news-content">';
                            
                            echo '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">';
                            echo '<span class="news-date">' . $fecha . '</span>';
                            if ($news['is_pinned']) {
                                echo '<span title="Noticia Destacada" style="color: var(--accent-purple); font-size: 1.2rem;">📌</span>';
                            }
                            echo '</div>';
                            
                            echo '<h3 class="news-title">' . htmlspecialchars($news['titulo']) . '</h3>';
                            echo '<p class="news-excerpt">' . htmlspecialchars($excerpt) . '</p>';
                            // Redes sociales
                            $social_html = '<div class="news-social-links" style="margin-top: 15px; display: flex; gap: 10px;">';
                            if (!empty($news['enlace_facebook'])) {
                                $social_html .= '<a href="'.htmlspecialchars($news['enlace_facebook']).'" target="_blank" class="news-social-icon" style="color: #1877F2; background: rgba(24, 119, 242, 0.1); padding: 8px; border-radius: 50%;"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg></a>';
                            }
                            if (!empty($news['enlace_instagram'])) {
                                $social_html .= '<a href="'.htmlspecialchars($news['enlace_instagram']).'" target="_blank" class="news-social-icon" style="color: #E1306C; background: rgba(225, 48, 108, 0.1); padding: 8px; border-radius: 50%;"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line></svg></a>';
                            }
                            if (!empty($news['enlace_twitter'])) {
                                $social_html .= '<a href="'.htmlspecialchars($news['enlace_twitter']).'" target="_blank" class="news-social-icon" style="color: #000; background: rgba(0, 0, 0, 0.1); padding: 8px; border-radius: 50%;"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z"></path></svg></a>';
                            }
                            if (!empty($news['enlace_tiktok'])) {
                                $social_html .= '<a href="'.htmlspecialchars($news['enlace_tiktok']).'" target="_blank" class="news-social-icon" style="color: #000; background: rgba(0, 0, 0, 0.1); padding: 8px; border-radius: 50%;"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M19.589 6.686a4.793 4.793 0 0 1-3.77-4.245V2h-3.445v13.672a2.896 2.896 0 0 1-5.201 1.743l-.002-.001.002.001a2.895 2.895 0 0 1 3.183-4.51v-3.5a6.329 6.329 0 0 0-5.394 10.692 6.33 6.33 0 0 0 10.857-4.424V8.687a8.182 8.182 0 0 0 4.773 1.526V6.79a4.831 4.831 0 0 1-1.003-.104z"/></svg></a>';
                            }
                            if (!empty($news['enlace_youtube'])) {
                                $social_html .= '<a href="'.htmlspecialchars($news['enlace_youtube']).'" target="_blank" class="news-social-icon" style="color: #FF0000; background: rgba(255, 0, 0, 0.1); padding: 8px; border-radius: 50%;"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.5 12 3.5 12 3.5s-7.505 0-9.377.55a3.016 3.016 0 0 0-2.122 2.136C0 8.07 0 12 0 12s0 3.93.501 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.55 9.377.55 9.377.55s7.505 0 9.377-.55a3.016 3.016 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg></a>';
                            }
                            $social_html .= '</div>';
                            
                            echo $social_html;
                            
                            // Extraer y arreglar JSON de extras si existe
                            $extras_arr = [];
                            if (!empty($news['imagenes_extra'])) {
                                $decoded = json_decode($news['imagenes_extra'], true);
                                if (is_array($decoded)) {
                                    foreach ($decoded as $ext) {
                                        if (strpos($ext, 'uploads/') === 0) {
                                            $extras_arr[] = 'admin/' . $ext;
                                        } else {
                                            $extras_arr[] = $ext;
                                        }
                                    }
                                }
                            }
                            $extras_attr = htmlspecialchars(json_encode($extras_arr));

                            // Guardamos los datos completos en atributos de datos para el modal JS
                            echo '<a href="#" class="news-read-more" style="margin-top: 15px;" data-index="'.$index.'" '
                               . 'data-title="'.htmlspecialchars($news['titulo']).'" '
                               . 'data-date="'.$fecha.'" '
                               . 'data-img="'.$imgUrl.'" '
                               . 'data-extras="'.$extras_attr.'" '
                               . 'data-content="'.htmlspecialchars($news['contenido']).'">Leer más <span>→</span></a>';
                            
                            echo '</div>';
                            echo '</article>';
                        }
                    } else {
                        echo '<div class="empty-state"><h2>Aún no hay noticias publicadas.</h2><p>Vuelve pronto para enterarte de nuestras novedades.</p></div>';
                    }
                } else {
                    echo '<div class="empty-state"><h2>Error al cargar noticias.</h2><p>Base de datos no disponible.</p></div>';
                }
                ?>
            </div>
        </section>
    </main>

    <!-- Modal for reading full news -->
    <div id="news-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            
            <!-- Carrusel Container -->
            <div id="modal-carousel" class="modal-carousel" style="position: relative; text-align: center; background: #000;">
                <img id="modal-img" class="modal-image" src="" alt="Noticia" style="max-height: 50vh; object-fit: contain; width: 100%;">
                
                <!-- Flechas tipo WhatsApp -->
                <button class="carousel-btn prev-btn" id="carousel-prev" style="display: none; position: absolute; left: 10px; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.5); color: white; border: none; border-radius: 50%; width: 40px; height: 40px; font-size: 1.5rem; cursor: pointer;">❮</button>
                <button class="carousel-btn next-btn" id="carousel-next" style="display: none; position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.5); color: white; border: none; border-radius: 50%; width: 40px; height: 40px; font-size: 1.5rem; cursor: pointer;">❯</button>
                
                <!-- Puntos indicadores -->
                <div id="carousel-dots" style="position: absolute; bottom: 10px; width: 100%; display: flex; justify-content: center; gap: 8px;"></div>
            </div>

            <div class="modal-body">
                <span id="modal-date" class="modal-date"></span>
                <h2 id="modal-title" class="modal-title"></h2>
                <div id="modal-text" class="modal-text"></div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- PIE DE PÁGINA (FOOTER)                     -->
    <!-- ========================================== -->
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div>
                    <div class="footer-logo">
                        <model-viewer src="assets/logo.glb" alt="Logo Manos Inclusivas" class="notranslate" translate="no" 
                            style="width: 80px; height: 80px; background-color: transparent;" 
                            camera-orbit="0deg 90deg auto" interaction-prompt="none"></model-viewer>
                        <h3 style="color: white; margin: 0; font-size: 1.2rem;" class="notranslate" translate="no">Manos Inclusivas A.C.</h3>
                    </div>
                    <p class="footer-desc">Fundación dedicada al neurodesarrollo, iluminando corazones y conectando
                        vidas en Yucatán.</p>
                    <div class="social-links">
                        <a href="https://www.facebook.com/share/18zML6YwYw/" target="_blank" class="social-icon" aria-label="Facebook">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                        </a>
                        <a href="https://www.instagram.com/miicneurodesarollo?igsh=MXI3aTVsaXRjMGZzMw==&igsi=MXI3aTVsaXRjMGZzMw==" target="_blank" class="social-icon" aria-label="Instagram">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line></svg>
                        </a>
                        <a href="https://x.com/inclusivas43334?s=20" target="_blank" class="social-icon" aria-label="Twitter/X">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z"></path></svg>
                        </a>
                        <a href="https://youtube.com/@miicneurodesarrollo?si=dE3kiuZaIAr2e2By" target="_blank" class="social-icon" aria-label="YouTube">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17"></path><path d="m10 15 5-3-5-3z"></path></svg>
                        </a>
                    </div>
                </div>
                <div>
                    <h4 class="footer-title">Contacto</h4>
                    <ul class="footer-links">
                        <li><a href="https://wa.me/5219991121609?text=Hola,%20me%20gustaría%20recibir%20más%20información." target="_blank">Mensaje por WhatsApp</a></li>
                        <li><a href="https://maps.app.goo.gl/YN3QVJp3aTZ7rAW86" target="_blank">Ubicación</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="footer-title">Enlaces</h4>
                    <ul class="footer-links">
                        <li><a href="nosotros.html">Nosotros</a></li>
                        <li><a href="servicios.html">Servicios</a></li>
                        <li><a href="donar.html">Donativos</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="footer-title">Legal</h4>
                    <ul class="footer-links">
                        <li><a href="legal.html#privacidad">Aviso de Privacidad</a></li>
                        <li><a href="legal.html#terminos">Términos y Condiciones</a></li>
                        <li><a href="legal.html#transparencia">Transparencia</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 <span class="notranslate" translate="no">MANOS INCLUSIVAS ILUMINANDO CORAZONES A.C.</span> Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- ========================================== -->
    <!-- SCRIPTS DEL SITIO (JS & APIS)              -->
    <!-- ========================================== -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/neural-loader.js?v=18"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('news-modal');
            const closeBtn = document.querySelector('.close-modal');
            
            // Carousel Elements
            const modalImg = document.getElementById('modal-img');
            const prevBtn = document.getElementById('carousel-prev');
            const nextBtn = document.getElementById('carousel-next');
            const dotsContainer = document.getElementById('carousel-dots');
            
            let currentImages = [];
            let currentIndex = 0;
            
            function updateCarousel() {
                if(currentImages.length === 0) return;
                modalImg.src = currentImages[currentIndex];
                
                // Update dots
                Array.from(dotsContainer.children).forEach((dot, index) => {
                    dot.style.background = index === currentIndex ? 'var(--accent-purple)' : 'rgba(255,255,255,0.5)';
                });
            }

            // Add event listeners to "Leer más"
            document.querySelectorAll('.news-read-more').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const title = this.getAttribute('data-title');
                    const date = this.getAttribute('data-date');
                    const img = this.getAttribute('data-img');
                    const content = this.getAttribute('data-content');
                    const extrasRaw = this.getAttribute('data-extras');
                    
                    document.getElementById('modal-title').textContent = title;
                    document.getElementById('modal-date').textContent = date;
                    document.getElementById('modal-text').textContent = content;
                    
                    // Setup Images
                    currentImages = [img];
                    try {
                        const extras = JSON.parse(extrasRaw);
                        if(Array.isArray(extras)) {
                            extras.forEach(extra => currentImages.push(extra));
                        }
                    } catch(e) {}
                    
                    currentIndex = 0;
                    dotsContainer.innerHTML = '';
                    
                    if(currentImages.length > 1) {
                        prevBtn.style.display = 'block';
                        nextBtn.style.display = 'block';
                        
                        // Create dots
                        currentImages.forEach((_, i) => {
                            const dot = document.createElement('div');
                            dot.style.width = '10px';
                            dot.style.height = '10px';
                            dot.style.borderRadius = '50%';
                            dot.style.cursor = 'pointer';
                            dot.addEventListener('click', () => {
                                currentIndex = i;
                                updateCarousel();
                            });
                            dotsContainer.appendChild(dot);
                        });
                    } else {
                        prevBtn.style.display = 'none';
                        nextBtn.style.display = 'none';
                    }
                    
                    updateCarousel();
                    
                    modal.style.display = "block";
                    document.body.style.overflow = "hidden"; // Prevent background scrolling
                });
            });

            // Carousel Navigation
            prevBtn.addEventListener('click', () => {
                currentIndex = (currentIndex > 0) ? currentIndex - 1 : currentImages.length - 1;
                updateCarousel();
            });
            nextBtn.addEventListener('click', () => {
                currentIndex = (currentIndex < currentImages.length - 1) ? currentIndex + 1 : 0;
                updateCarousel();
            });

            // Close modal logic
            closeBtn.onclick = function() {
                modal.style.display = "none";
                document.body.style.overflow = "auto";
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                    document.body.style.overflow = "auto";
                }
            }
        });
    </script>
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'es', 
                includedLanguages: 'en,fr,de,zh-CN,it,pt,ja,es', 
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE
            }, 'google_translate_element');
        }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <script src="assets/js/translator.js?v=3?v=16"></script>
</body>

</html>
