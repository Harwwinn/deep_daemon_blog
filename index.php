<?php
// -- Bloque de depuración --
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Guardia de seguridad y sesión
// require_once __DIR__ . '/auth.php';

// Incluir el header (que ya inicia la sesión)
require_once 'homepage/_header.php';
// Incluir la conexión a la base de datos
require_once __DIR__ . '/config/conexion.php';

// --- OBTENER ARTÍCULOS DE LA BASE DE DATOS ---

// 1. Obtener el artículo más reciente para la sección "Hero"
$featured_post = null;
$sql_featured = "SELECT a.*, u.nombre as autor_nombre, IFNULL(u.avatar, 'avatar10.svg') as user_avatar,IFNULL(c.nombre, 'No category') AS categoria_nombre, IFNULL(c.slug, 'no-category') AS categoria_slug  
                 FROM articulos a
                 JOIN usuarios u ON a.autor_id = u.id
                 LEFT JOIN categorias c ON a.categoria_id = c.id
                 ORDER BY a.fecha_publicacion DESC 
                 LIMIT 1";
$result_featured = $conn->query($sql_featured);
if ($result_featured && $result_featured->num_rows > 0) {
    $featured_post = $result_featured->fetch_assoc();
}

// 2. Obtener los 9 siguientes artículos más recientes para la cuadrícula
$latest_posts = [];
$sql_latest = "SELECT a.*, u.nombre as autor_nombre, IFNULL(u.avatar, 'avatar10.svg') as user_avatar,IFNULL(c.nombre, 'No category') AS categoria_nombre, IFNULL(c.slug, 'no-category') AS categoria_slug
               FROM articulos a
               JOIN usuarios u ON a.autor_id = u.id
               LEFT JOIN categorias c ON a.categoria_id = c.id
               ORDER BY a.fecha_publicacion DESC 
               LIMIT 9 OFFSET 1"; // OFFSET 1 para saltar el que ya es destacado
$result_latest = $conn->query($sql_latest);
if ($result_latest && $result_latest->num_rows > 0) {
    while($row = $result_latest->fetch_assoc()) {
        $latest_posts[] = $row;
    }
}

$conn->close();

// Función para formatear fechas amigablemente
function format_date($date_string) {
    $date = new DateTime($date_string);
    // Formato: August 30, 2022
    return $date->format('F j, Y');
}
?>

<!-- Sección Hero con el Artículo Destacado -->
<?php if ($featured_post): ?>
    <a href="blogging/single-post.php?id=<?php echo $featured_post['id']; ?>" class="block group">
        <section class="relative h-[400px] md:h-[500px] bg-cover bg-center text-white flex items-end p-8 transition-transform duration-300 group-hover:scale-105" 
                 style="background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('<?php echo '/uploads/default-stock/' . htmlspecialchars($featured_post['imagen_destacada']); ?>');">
            
            <!-- CAMBIO: El contenido interno ya no necesita ser un enlace -->
            <div class="max-w-xl bg-white/90 backdrop-blur-sm text-gray-800 p-8 rounded-lg shadow-xl">
                <span class="inline-block py-1 px-3 rounded-full bg-primary-100 text-primary-700 text-sm font-semibold uppercase mb-4"><?php echo htmlspecialchars($featured_post['categoria_nombre']); ?></span>
                <h1 class="text-3xl md:text-4xl font-bold leading-tight"><?php echo htmlspecialchars($featured_post['titulo']); ?></h1>
                <div class="mt-4 flex items-center text-sm">
                    <?php $avatar_url = '/assets/img/avatars/' . $featured_post['user_avatar']; ?>
                    <img class="h-8 w-8 rounded-full mr-3" src="<?php echo $avatar_url; ?>" alt="Avatar del autor">
                    <span><?php echo htmlspecialchars($featured_post['autor_nombre']); ?></span>
                    <span class="mx-2">•</span>
                    <span><?php echo format_date($featured_post['fecha_publicacion']); ?></span>
                </div>
            </div>
        </section>
    </a>
<?php endif; ?>

<!-- Sección de Últimos Posts -->
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h2 class="text-3xl font-bold text-gray-900 mb-8">Latest Posts</h2>

     <!-- === ALPINE.JS COMPONENT FOR "LOAD MORE" === -->
    <div x-data="{
        page: 2,
        loading: false,
        noMorePosts: false,
        loadMore() {
            this.loading = true;
            fetch(`/fetch_posts.php?page=${this.page}`)
                .then(response => response.text())
                .then(html => {
                    if (html.trim() === '') {
                        this.noMorePosts = true;
                    } else {
                        // Insert the new posts into the grid
                        this.$refs.postsGrid.insertAdjacentHTML('beforeend', html);
                        this.page++;
                    }
                    this.loading = false;
                });
        }
    }">
    
    <!-- Grid of Posts -->
    <div x-ref="postsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Your initial 9 latest posts are rendered here by PHP -->
        <?php foreach ($latest_posts as $post): ?>
        <article class="bg-white rounded-lg shadow-md overflow-hidden transform hover:-translate-y-2 transition-transform duration-300">
            <!-- ... Post card HTML (same as before) ... -->
            <a href="/blogging/single-post.php?id=<?php echo $post['id']; ?>">
                <img class="h-56 w-full object-cover" 
                        src="/uploads/default-stock/<?php echo htmlspecialchars($post['imagen_destacada']); ?>" 
                        alt="Image for <?php echo htmlspecialchars($post['titulo']); ?>">
            </a>
            <div class="p-6">
                <span class="inline-block py-1 px-3 rounded-full bg-primary-100 text-primary-700 text-sm font-semibold uppercase mb-3">
                    <?php echo htmlspecialchars($post['categoria_nombre']); ?>
                </span>
                <h3 class="text-xl font-bold mb-2">
                    <a href="/blogging/single-post.php?id=<?php echo $post['id']; ?>" class="hover:text-primary-600">
                        <?php echo htmlspecialchars($post['titulo']); ?>
                    </a>
                </h3>
                <div class="flex items-center text-sm text-gray-500">
                    <?php $avatar_url = '/assets/img/avatars/' . $post['user_avatar']; ?>
                    <img class="h-8 w-8 rounded-full mr-3" 
                            src="<?php echo $avatar_url; ?>" 
                            alt="Author Avatar">
                    <span><?php echo htmlspecialchars($post['autor_nombre']); ?></span>
                    <span class="mx-2">&bull;</span>
                    <span><?php echo format_date($post['fecha_publicacion']); ?></span>
                </div>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    
    <!-- "Load More" Button Section -->
    <div class="text-center mt-12">
        <!-- This button is now controlled by Alpine.js -->
        <button @click="loadMore" 
                :disabled="loading || noMorePosts"
                class="inline-block px-8 py-3 text-base font-semibold rounded-lg transition-colors duration-200
                        disabled:opacity-50 disabled:cursor-not-allowed
                        bg-primary-600 text-white hover:bg-primary-700
                        focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            <!-- The button text changes based on the state -->
            <span x-show="!loading && !noMorePosts">Load More Posts</span>
            <span x-show="loading">Loading...</span>
            <span x-show="noMorePosts">No More Posts</span>
        </button>
    </div>
</div>

<?php
// Incluir el footer
require_once 'homepage/_footer.php';
?>