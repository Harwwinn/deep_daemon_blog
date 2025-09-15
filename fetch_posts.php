<?php
// -- Bloque de depuración --
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// This script returns only the HTML for the post cards, not a full page.
require_once __DIR__ . '/config/conexion.php';

// --- PAGINATION LOGIC ---
$posts_per_page = 9; // Should match the number on the index page

// Get the page number to fetch from the GET request (e.g., fetch_posts.php?page=2)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}

// Calculate the offset. Note that page 1 on index.php is the initial 9 posts,
// so the first AJAX request should be for page 2.
$offset = ($page - 1) * $posts_per_page;

// Get the posts for the requested page
$posts = [];
$sql = "SELECT a.*, u.nombre AS autor_nombre, IFNULL(u.avatar, 'avatar10.svg') AS user_avatar, IFNULL(c.nombre, 'Uncategorized') AS categoria_nombre
        FROM articulos a
        JOIN usuarios u ON a.autor_id = u.id
        LEFT JOIN categorias c ON a.categoria_id = c.id
        ORDER BY a.fecha_publicacion DESC
        LIMIT ? OFFSET ?";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ii", $posts_per_page, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $posts = $result->fetch_all(MYSQLI_ASSOC);
    }
    $stmt->close();
}
$conn->close();

function format_date($date_string) {
    $date = new DateTime($date_string);
    return $date->format('F j, Y');
}

// --- OUTPUT THE HTML ---
// We loop through the fetched posts and generate the HTML for each card.
// This raw HTML is what the JavaScript will receive and inject into the page.
?>
<?php if (!empty($posts)): ?>
    <?php foreach ($posts as $post): ?>
    <article class="bg-white rounded-lg shadow-md overflow-hidden transform hover:-translate-y-2 transition-transform duration-300">
        <a href="single-post.php?id=<?php echo $post['id']; ?>">
            <img class="h-56 w-full object-cover" src="/uploads/<?php echo htmlspecialchars($post['imagen_destacada']); ?>" alt="Image for <?php echo htmlspecialchars($post['titulo']); ?>">
        </a>
        <div class="p-6">
            <span class="inline-block py-1 px-3 rounded-full bg-primary-100 text-primary-700 text-sm font-semibold uppercase mb-3"><?php echo htmlspecialchars($post['categoria_nombre']); ?></span>
            <h3 class="text-xl font-bold mb-2">
                <a href="single-post.php?id=<?php echo $post['id']; ?>" class="hover:text-primary-600"><?php echo htmlspecialchars($post['titulo']); ?></a>
            </h3>
            <div class="flex items-center text-sm text-gray-500">
                <?php $avatar_url = '/assets/img/avatars/' . $post['user_avatar']; ?>
                <img class="h-8 w-8 rounded-full mr-3" src="<?php echo $avatar_url; ?>" alt="Author Avatar">
                <span><?php echo htmlspecialchars($post['autor_nombre']); ?></span>
                <span class="mx-2">&bull;</span>
                <span><?php echo format_date($post['fecha_publicacion']); ?></span>
            </div>
        </div>
    </article>
    <?php endforeach; ?>
<?php endif; ?>