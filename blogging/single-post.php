<?php
// -- Bloque de depuración --
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Incluir el header (que ya inicia la sesión)
require_once '../homepage/_header.php';
// Incluir la conexión a la base de datos
require_once __DIR__ . '../../config/conexion.php';

// 1. OBTENER EL ID DEL ARTÍCULO DE LA URL
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($post_id <= 0) {
    // Si no hay ID o es inválido, mostramos un error y salimos
    echo "<div class='container mx-auto p-8 text-center'><h1 class='text-2xl font-bold text-red-600'>Artículo no encontrado.</h1></div>";
    require_once '_footer.php';
    exit();
}

// 2. INCREMENTAR EL CONTADOR DE VISITAS (usando una consulta preparada para seguridad)
$sql_update_visits = "UPDATE articulos SET visitas = visitas + 1 WHERE id = ?";
if ($stmt_visits = $conn->prepare($sql_update_visits)) {
    $stmt_visits->bind_param("i", $post_id);
    $stmt_visits->execute();
    $stmt_visits->close();
}

// 3. OBTENER LOS DATOS DEL ARTÍCULO Y SU AUTOR
$post = null;
$sql_post = "SELECT a.*, u.nombre as autor_nombre, u.id as autor_id, IFNULL(c.nombre, 'No category') AS categoria_nombre, IFNULL(u.avatar, 'avatar10.svg') as user_avatar, IFNULL(c.slug, 'no-category') AS categoria_slug
             FROM articulos a
             JOIN usuarios u ON a.autor_id = u.id
             LEFT JOIN categorias c ON a.categoria_id = c.id
             WHERE a.id = ?";
             
if ($stmt_post = $conn->prepare($sql_post)) {
    $stmt_post->bind_param("i", $post_id);
    $stmt_post->execute();
    $result_post = $stmt_post->get_result();

    if ($result_post->num_rows === 1) {
        $post = $result_post->fetch_assoc();
    } else {
        // Si el artículo con ese ID no existe, mostramos error
        echo "<div class='container mx-auto p-8 text-center'><h1 class='text-2xl font-bold text-red-600'>The post does not exist.</h1></div>";
        require_once '_footer.php';
        exit();
    }
    $stmt_post->close();
}

$conn->close();

// Función para formatear fechas amigablemente (la movimos aquí para que esté disponible)
function format_date($date_string) {
    $date = new DateTime($date_string);
    return $date->format('F j, Y');
}
?>

<!-- Contenido Principal del Artículo -->
<div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl py-12">
    <article>
        <!-- Encabezado del Artículo -->
        <header class="mb-8">
            <span class="inline-block py-1 px-3 rounded-full bg-primary-100 text-primary-700 text-sm font-semibold uppercase mb-4"><?php echo htmlspecialchars($post['categoria_nombre']); ?></span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight mb-4"><?php echo htmlspecialchars($post['titulo']); ?></h1>
            <div class="flex items-center text-gray-500 text-sm">
                <!-- Asumiremos un avatar por defecto por ahora -->
                <?php $avatar_url = '/assets/img/avatars/' . $post['user_avatar']; ?>
                <img class="h-10 w-10 rounded-full mr-4" src="<?php echo $avatar_url; ?>" alt="Avatar de <?php echo htmlspecialchars($post['autor_nombre']); ?>">
                <div>
                    <p class="font-semibold text-gray-800">Por <?php echo htmlspecialchars($post['autor_nombre']); ?></p>
                    <p>Published <?php echo format_date($post['fecha_publicacion']); ?> • <?php echo number_format($post['visitas']); ?> views</p>
                </div>
            </div>
        </header>

        <!-- Imagen Destacada -->
        <?php if (!empty($post['imagen_destacada'])): ?>
            <figure class="mb-8">
                <img class="w-full h-auto rounded-lg shadow-lg" src="<?php echo BASE_URL . '/uploads/default-stock/' . htmlspecialchars($post['imagen_destacada']); ?>" alt="Imagen destacada para <?php echo htmlspecialchars($post['titulo']); ?>">
            </figure>
        <?php endif; ?>

        <!-- Contenido del Post (Renderizado desde TinyMCE) -->
        <div class="prose lg:prose-xl max-w-none">
            <?php echo $post['contenido']; // El contenido ya es HTML, no necesita htmlspecialchars ?>
        </div>
    </article>
</div>

<!-- (Opcional) Sección de "También te podría interesar" -->
<!-- Aquí podrías hacer otra consulta a la BD para obtener 3 artículos relacionados de la misma categoría -->

<?php
// Incluir el footer
require_once '../homepage/_footer.php';
?>