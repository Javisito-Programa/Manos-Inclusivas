<?php
// Configurar cabeceras para permitir peticiones (CORS) y especificar que devuelve JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$archivo_noticias = 'noticias.json';
$upload_dir = '../img/noticias/';

// Si es una petición OPTIONS (Preflight de CORS), terminamos aquí
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Función para leer las noticias
function getNoticias() {
    global $archivo_noticias;
    if (file_exists($archivo_noticias)) {
        $json_existente = file_get_contents($archivo_noticias);
        $noticias = json_decode($json_existente, true);
        if (is_array($noticias)) {
            return $noticias;
        }
    }
    return [];
}

// Función para guardar las noticias
function saveNoticias($noticias) {
    global $archivo_noticias;
    return file_put_contents($archivo_noticias, json_encode($noticias, JSON_PRETTY_PRINT));
}

// Manejar petición DELETE (Borrar noticia)
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' || (isset($_GET['action']) && $_GET['action'] == 'delete')) {
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    if (empty($id)) {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = isset($input['id']) ? $input['id'] : '';
    }

    if (!empty($id)) {
        $noticias = getNoticias();
        $noticias_filtradas = array_filter($noticias, function($n) use ($id) {
            return $n['id'] !== $id;
        });

        // Reindex array
        $noticias_filtradas = array_values($noticias_filtradas);

        if (saveNoticias($noticias_filtradas)) {
            echo json_encode(['status' => 'success', 'message' => 'Noticia eliminada']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al guardar cambios']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
    }
    exit();
}

// Manejar petición POST (Crear o Actualizar noticia)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Si viene como JSON (caso antiguo o peticiones sin archivo)
    $input = file_get_contents('php://input');
    $json_data = json_decode($input, true);
    
    $title = isset($_POST['title']) ? $_POST['title'] : (isset($json_data['title']) ? $json_data['title'] : '');
    $content = isset($_POST['content']) ? $_POST['content'] : (isset($json_data['content']) ? $json_data['content'] : '');
    $id = isset($_POST['id']) ? $_POST['id'] : (isset($json_data['id']) ? $json_data['id'] : '');
    $imageUrl = isset($_POST['image']) ? $_POST['image'] : (isset($json_data['image']) ? $json_data['image'] : '');

    if (!empty($title) && !empty($content)) {
        $noticias = getNoticias();
        $finalImagePath = $imageUrl;

        // Manejar subida de archivo si existe
        if (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] === UPLOAD_ERR_OK) {
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $fileTmpPath = $_FILES['imageFile']['tmp_name'];
            $fileName = $_FILES['imageFile']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'webp');
            
            if (in_array($fileExtension, $allowedfileExtensions)) {
                $newFileName = uniqid('img_', true) . '.' . $fileExtension;
                $dest_path = $upload_dir . $newFileName;
                
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    // La ruta que se guardará en JSON debe ser relativa a donde se muestran (ej. index.html)
                    // Por lo tanto será 'img/noticias/nombre.jpg'
                    $finalImagePath = 'img/noticias/' . $newFileName;
                } else {
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'Hubo un error moviendo el archivo al directorio de destino. Verifica permisos.']);
                    exit();
                }
            } else {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Sube un archivo de imagen válido (jpg, png, gif, webp).']);
                exit();
            }
        }

        if (!empty($id)) {
            // Actualizar existente
            $updated = false;
            foreach ($noticias as &$n) {
                if ($n['id'] === $id) {
                    $n['title'] = htmlspecialchars($title);
                    $n['content'] = htmlspecialchars($content);
                    // Si se subió una nueva imagen o se cambió la URL, actualizamos
                    if (!empty($finalImagePath)) {
                        $n['image'] = filter_var($finalImagePath, FILTER_SANITIZE_URL);
                    }
                    $n['date'] = date('c'); // Actualizamos la fecha
                    $updated = true;
                    break;
                }
            }
            
            if (!$updated) {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Noticia no encontrada para actualizar']);
                exit();
            }
            $mensaje = 'Noticia actualizada con éxito';
        } else {
            // Crear nueva
            $nueva_noticia = [
                'id' => (string) round(microtime(true) * 1000), // Usar timestamp en ms como ID para ser consistente
                'title' => htmlspecialchars($title),
                'content' => htmlspecialchars($content),
                'image' => filter_var($finalImagePath, FILTER_SANITIZE_URL),
                'date' => date('c')
            ];
            array_unshift($noticias, $nueva_noticia); // Añadir al principio
            $mensaje = 'Noticia creada con éxito';
        }

        if (saveNoticias($noticias)) {
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => $mensaje]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al guardar en el archivo JSON. Verifica los permisos.']);
        }
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(['status' => 'error', 'message' => 'Faltan datos requeridos (título o contenido)']);
    }
} 
// Manejar petición GET (Leer noticias)
else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(getNoticias());
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}
?>
