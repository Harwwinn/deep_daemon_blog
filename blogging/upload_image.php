<?php
// Primero, la seguridad. Solo usuarios logueados pueden subir imágenes.
require_once __DIR__ . '/../auth.php';

/*******************************************************
 * Valida y procesa la subida de imágenes desde TinyMCE.
 *
 * Responde con un JSON que contiene la ubicación de la imagen
 * si tiene éxito, o con un error HTTP si falla.
 *******************************************************/

// Directorio de destino para las imágenes
$directorio_destino = __DIR__ . '/../uploads/content_images/';

// Dominios aceptados para la solicitud (seguridad CORS)
// En producción, cambia 'localhost' por tu dominio real.
$dominios_aceptados = ["http://localhost", "https://deepdaemon.org"];
if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $dominios_aceptados)) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
}

// Comprobar si se subió un archivo
if (!isset($_FILES['file']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'No se ha subido ningún archivo.']);
    exit;
}

$file = $_FILES['file'];

// --- Validaciones de Seguridad ---
// 1. Validar errores de subida de PHP
if ($file['error'] !== UPLOAD_ERR_OK) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Error en la subida del archivo. Código: ' . $file['error']]);
    exit;
}

// 2. Validar tamaño del archivo (ej: 2MB)
$max_file_size = 2 * 1024 * 1024;
if ($file['size'] > $max_file_size) {
    header('HTTP/1.1 413 Payload Too Large');
    echo json_encode(['error' => 'El archivo es demasiado grande (máximo 2MB).']);
    exit;
}

// 3. Validar que sea una imagen real y obtener su tipo MIME
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

$allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($mime_type, $allowed_mime_types)) {
    header('HTTP/1.1 415 Unsupported Media Type');
    echo json_encode(['error' => 'Formato de archivo no válido. Solo se permiten JPG, PNG, GIF, WEBP.']);
    exit;
}

// --- Procesar y Mover el Archivo ---
// Crear un nombre de archivo único para evitar colisiones
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$nombre_archivo_unico = uniqid('img_') . '.' . $extension;
$ruta_completa = $directorio_destino . $nombre_archivo_unico;

if (move_uploaded_file($file['tmp_name'], $ruta_completa)) {
    // Éxito: Devolver la ubicación del archivo en formato JSON
    // Esta es la estructura que TinyMCE espera.
    
    // IMPORTANTE: La URL debe ser absoluta desde la raíz del sitio web.
    $url_publica = '/uploads/content_images/' . $nombre_archivo_unico;
    
    echo json_encode(['location' => $url_publica]);
} else {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'No se pudo mover el archivo subido. Revisa los permisos de la carpeta.']);
}
?>