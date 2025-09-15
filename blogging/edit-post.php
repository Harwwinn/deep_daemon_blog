<?php
// -- Bloque de depuración --
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Guardia de seguridad y sesión
require_once __DIR__ . '/../auth.php';
// Conexión a la BD
require_once __DIR__ . '/../config/conexion.php';

// Define the array of default images. The paths are relative to the 'uploads' folder.
// --- NUEVO: Obtener la lista de imágenes dinámicamente ---
$upload_dir = __DIR__ . '/../uploads/default-stock/';
$all_files = scandir($upload_dir);
// Filtrar para quitar '.' y '..' y asegurarse de que sean archivos de imagen
$default_images = array_filter($all_files, function($file) {
    $image_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    return in_array($extension, $image_types);
});
// --- FIN DEL BLOQUE NUEVO ---


// Obtener categorías para el dropdown
$categorias_disponibles = [];
$sql_categorias = "SELECT id, nombre FROM categorias ORDER BY nombre ASC";
if ($result_cat = $conn->query($sql_categorias)) {
    $categorias_disponibles = $result_cat->fetch_all(MYSQLI_ASSOC);
}

// 1. OBTENER EL ID DEL POST Y VERIFICAR QUE EL USUARIO ES EL AUTOR
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];
$post = null;
$error_msg = '';

if ($post_id > 0) {
    $sql_verify = "SELECT * FROM articulos WHERE id = ? AND autor_id = ?";
    if ($stmt_verify = $conn->prepare($sql_verify)) {
        $stmt_verify->bind_param("ii", $post_id, $user_id);
        $stmt_verify->execute();
        $result = $stmt_verify->get_result();
        
        if ($result->num_rows === 1) {
            $post = $result->fetch_assoc();
        } else {
            // Si el post no existe o no pertenece al usuario, lo sacamos de aquí
            header("Location: " . BASE_URL . "/blogging/dashboard.php?error=not_found_or_unauthorized");
            exit();
        }
        $stmt_verify->close();
    }
} else {
    header("Location: " . BASE_URL . "/blogging/dashboard.php?error=invalid_id");
    exit();
}

// 2. PROCESAR EL FORMULARIO DE ACTUALIZACIÓN
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST['titulo']);
    $contenido = trim($_POST['contenido']);
    $categoria_id = trim($_POST['categoria_id']);
    // Get the selected image name from the hidden input
    $imagen_destacada_nombre = trim($_POST['imagen_destacada']);

    if (empty($titulo) || empty($categoria_id)) {
        $error_msg = "Title and category are mandatory.";
    }

    if (!empty($imagen_destacada_nombre) && !in_array($imagen_destacada_nombre, $default_images)) {
        $error_msg = "Invalid featured image selected.";
    }
    
    if (empty($error_msg)) {
        // Prepare the SQL UPDATE query
        $sql_update = "UPDATE articulos SET titulo = ?, contenido = ?, imagen_destacada = ?, categoria_id = ? WHERE id = ? AND autor_id = ?";
        if($stmt_update = $conn->prepare($sql_update)) {
            $stmt_update->bind_param("sssiii", $titulo, $contenido, $imagen_destacada_nombre, $categoria_id, $post_id, $user_id);
            
            if ($stmt_update->execute()) {
                header("Location: " . BASE_URL . "/blogging/dashboard.php?status=updated");
                exit();
            } else {
                $error_msg = "Error updating the article.";
            }
            $stmt_update->close();
        }
    }
}

$conn->close();

// Incluir el header
require_once __DIR__ . '/../homepage/_header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Artículo</title>
    <!-- El CSS se incluye en _header.php -->
    
    <!-- Script de TinyMCE (reemplaza con tu API Key) -->
    <script src="https://cdn.tiny.cloud/1/8511dydbgylcnrlse705ds9376k9izxpjiy7m0e3q6i8fxv4/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body class="bg-gray-100" x-data="{ modalOpen: false, selectedImage: '<?php echo htmlspecialchars($post['imagen_destacada']); ?>' }">
    <div class="container mx-auto p-4 sm:p-6 lg:p-8">
         <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Edit Article</h1>
            <a href="/blogging/dashboard.php" class="text-sm font-medium text-primary-600 hover:text-primary-800">← Back to Dashboard</a>
        </div>

        <?php if(!empty($error_msg)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $error_msg; ?></span>
            </div>
        <?php endif; ?>

        <form action="edit-post.php?id=<?php echo $post_id; ?>" method="POST">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Main Column: Form -->
                <div class="lg:col-span-2 bg-white p-8 rounded-lg shadow-md space-y-6">
                    <!-- Title Field -->
                    <div>
                        <label for="titulo" class="block text-sm font-medium text-gray-700 mb-1">Post Title</label>
                        <input type="text" name="titulo" id="titulo" value="<?php echo htmlspecialchars($post['titulo']); ?>" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500" required>
                    </div>
                    <!-- Content Field -->
                    <div>
                        <label for="contenido" class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                        <textarea name="contenido" id="contenido" class="h-64"><?php echo htmlspecialchars($post['contenido']); ?></textarea>
                    </div>
                </div>

                <!-- Side Column: Metadata -->
                <div class="lg:col-span-1 space-y-8">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Publication</h3>
                        
                        <!-- Featured Image Selector -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cover Image</label>
                            <input type="hidden" name="imagen_destacada" x-model="selectedImage">
                            <div class="w-full h-40 bg-gray-200 rounded-md flex items-center justify-center mb-2 overflow-hidden">
                                <template x-if="selectedImage">
                                    <img :src="'/uploads/default-stock/' + selectedImage" alt="Selected Image" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!selectedImage">
                                    <span class="text-gray-500">No image selected</span>
                                </template>
                            </div>
                            <button type="button" @click="modalOpen = true" class="w-full text-sm font-semibold text-primary-600 hover:text-primary-800">
                                Select image from gallery
                            </button>
                        </div>
                        
                        <!-- Category Field -->
                        <div class="mt-6">
                            <label for="categoria_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select name="categoria_id" id="categoria_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md shadow-sm" required>
                                <option value="">Select a category</option>
                                <?php foreach ($categorias_disponibles as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo ($post['categoria_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-8 border-t pt-6 flex justify-end space-x-4">
                            <a href="/blogging/dashboard.php" class="px-5 py-2.5 border border-gray-300 rounded-md shadow-sm text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 active:scale-95 transform transition-all duration-150 ease-in-out">Cancel</a>
                            <button type="submit" class="inline-flex justify-center items-center gap-x-2 px-5 py-2.5 border border-transparent rounded-md shadow-sm text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 active:scale-95 transform transition-all duration-150 ease-in-out">Save Changes</button>
                        </div>
                    </div>
                    <!-- Help Panel -->
                     <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                        <h4 class="text-md font-semibold text-blue-800">Tips</h4>
                        <ul class="mt-2 list-disc list-inside text-sm text-blue-700 space-y-1">
                            <li>Use headings (Heading 2, Heading 3) to structure your article.</li>
                            <li>Insert images into the content to make it more visual.</li>
                            <li>Add links to external sources to give more credibility.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Image Gallery Modal (same as in write_blog.php) -->
    <div x-show="modalOpen" @keydown.escape.window="modalOpen = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
        <div @click.away="modalOpen = false" class="bg-white rounded-lg shadow-xl w-full max-w-4xl p-6">
            <h3 class="text-xl font-bold mb-4">Select an Image</h3>
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-4 max-h-[60vh] overflow-y-auto">
                <?php foreach ($default_images as $image): ?>
                    <button type="button" @click="selectedImage = '<?php echo $image; ?>'; modalOpen = false;" class="aspect-w-1 aspect-h-1 block rounded-md overflow-hidden focus:outline-none focus:ring-4 focus:ring-primary-400">
                        <img src="/uploads/default-stock/<?php echo $image; ?>" alt="Stock Image" class="w-full h-full object-cover">
                    </button>
                <?php endforeach; ?>
            </div>
            <!-- ... Cancel button ... -->
            <div class="text-right mt-6">
                <button type="button" @click="modalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <script>
        // Inicialización de TinyMCE
        tinymce.init({
            selector: 'textarea#contenido', // Selecciona el textarea por su ID
            plugins: 'advlist autolink lists link image charmap preview anchor pagebreak searchreplace wordcount visualblocks code fullscreen insertdatetime media table help',
            toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | removeformat | help',
            height: 600,
            // Le decimos a TinyMCE a qué URL debe enviar las imágenes
            images_upload_url: '/blogging/upload_image.php',
            // Opcional: para que al pegar una imagen, se suba automáticamente
            automatic_uploads: true,
            // Opcional: Define qué tipos de archivo puede seleccionar el usuario
            //file_picker_types: 'image',
            menubar: 'file edit view insert format tools table help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
        });
    </script>
</body>
</html>

<?php
// Incluir el footer
require_once __DIR__ . '/../homepage/_footer.php';
?>