<?php
// Configurar cabeceras para permitir peticiones (CORS) y especificar que devuelve JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$archivo_noticias = 'noticias.json';

// Si es una petición OPTIONS (Preflight de CORS), terminamos aquí
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Manejar petición POST (Crear noticia)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Leer los datos JSON recibidos
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (isset($data['title']) && isset($data['content'])) {
        // Leer el archivo existente
        $noticias = [];
        if (file_exists($archivo_noticias)) {
            $json_existente = file_get_contents($archivo_noticias);
            $noticias = json_decode($json_existente, true);
            if (!is_array($noticias)) {
                $noticias = [];
            }
        }

        // Crear nueva noticia
        $nueva_noticia = [
            'id' => uniqid(),
            'title' => htmlspecialchars($data['title']),
            'content' => htmlspecialchars($data['content']), // en producción real podríamos permitir HTML seguro
            'image' => isset($data['image']) ? filter_var($data['image'], FILTER_SANITIZE_URL) : '',
            'date' => date('c') // Fecha ISO 8601
        ];

        // Añadir al arreglo
        array_push($noticias, $nueva_noticia);

        // Guardar en el archivo JSON
        if (file_put_contents($archivo_noticias, json_encode($noticias, JSON_PRETTY_PRINT))) {
            http_response_code(201); // Creado
            echo json_encode(['status' => 'success', 'message' => 'Noticia guardada con éxito', 'data' => $nueva_noticia]);
        } else {
            http_response_code(500); // Error interno
            echo json_encode(['status' => 'error', 'message' => 'Error al guardar en el archivo json. Verifica los permisos.']);
        }
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(['status' => 'error', 'message' => 'Faltan datos requeridos (título o contenido)']);
    }
} 
// Si alguien accede por GET directamente al PHP, le redirigimos el JSON o le damos error
else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (file_exists($archivo_noticias)) {
        echo file_get_contents($archivo_noticias);
    } else {
        echo json_encode([]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}
?>
