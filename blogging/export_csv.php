<?php
// Guardia de seguridad: solo usuarios logueados pueden exportar.
require_once __DIR__ . '/../auth.php';
// Incluir la conexión a la base de datos
require_once __DIR__ . '/../config/conexion.php';

// Determinar el tipo de exportación solicitado (mis artículos o todos)
$export_type = $_GET['type'] ?? 'my_posts';
$user_id = $_SESSION['user_id'];
$user_rol = $_SESSION['user_rol'];

// --- PREPARAR LA CONSULTA SQL BASADA EN EL TIPO DE EXPORTACIÓN ---
$sql = "";
$filename = "export.csv";

if ($export_type === 'my_posts') {
    // Consulta para obtener solo los artículos del usuario logueado
    $sql = "SELECT a.id, a.titulo, a.fecha_publicacion, a.visitas, c.nombre AS categoria_nombre
            FROM articulos a
            LEFT JOIN categorias c ON a.categoria_id = c.id
            WHERE a.autor_id = ?
            ORDER BY a.fecha_publicacion DESC";
    
    $filename = "mis_articulos_" . date('Y-m-d') . ".csv";

} elseif ($export_type === 'all_posts' && $user_rol === 'admin') {
    // Consulta para obtener TODOS los artículos (solo para admins)
    $sql = "SELECT a.id, a.titulo, a.fecha_publicacion, a.visitas, c.nombre AS categoria_nombre, u.nombre AS autor_nombre
            FROM articulos a
            LEFT JOIN categorias c ON a.categoria_id = c.id
            JOIN usuarios u ON a.autor_id = u.id
            ORDER BY a.fecha_publicacion DESC";

    $filename = "todos_los_articulos_" . date('Y-m-d') . ".csv";
} else {
    // Si el tipo es inválido o un usuario no-admin intenta exportar todo, detenemos la ejecución.
    die("Acceso no autorizado o tipo de exportación inválido.");
}

// --- GENERAR Y DESCARGAR EL ARCHIVO CSV ---

// Cabeceras HTTP para forzar la descarga del archivo
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Abrir el "archivo" de salida de PHP
$output = fopen('php://output', 'w');

// Escribir la fila de encabezados del CSV
if ($export_type === 'all_posts' && $user_rol === 'admin') {
    fputcsv($output, ['ID', 'Titulo', 'Fecha de Publicacion', 'Visitas', 'Categoria', 'Autor']);
} else {
    fputcsv($output, ['ID', 'Titulo', 'Fecha de Publicacion', 'Visitas', 'Categoria']);
}

// Ejecutar la consulta y escribir los datos en el archivo
if ($stmt = $conn->prepare($sql)) {
    if ($export_type === 'my_posts') {
        $stmt->bind_param("i", $user_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
    }
    $stmt->close();
}

$conn->close();
exit(); // Terminar el script
?>