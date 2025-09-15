<?php
// Guardia de seguridad: solo usuarios logueados pueden crear artículos
require_once __DIR__ . '/../auth.php'; 

// Variable para mensajes de error o éxito
$error_msg = '';

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

// Incluimos la conexión
require_once __DIR__ . '/../config/conexion.php';

// Obtener categorías para el dropdown
$categorias_disponibles = [];
$sql_categorias = "SELECT id, nombre FROM categorias ORDER BY nombre ASC";
if ($result_cat = $conn->query($sql_categorias)) {
    $categorias_disponibles = $result_cat->fetch_all(MYSQLI_ASSOC);
}

// Lógica para procesar el formulario cuando se envíe
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recoger datos del formulario
    $titulo = trim($_POST['titulo']);
    $contenido = trim($_POST['contenido']);
    $categoria = trim($_POST['categoria_id']);
    $autor_id = $_SESSION['user_id'];

    // --- NEW LOGIC: Get selected image name from the form ---
    $imagen_destacada_nombre = trim($_POST['imagen_destacada']);

    // 1. Validar campos de texto
    if (empty($titulo) || empty($categoria)) {
        $error_msg = "El título y la categoría son obligatorios.";
    }

    // 2. Validate that the selected image is one of the allowed default images (security check)
    if (!empty($imagen_destacada_nombre) && !in_array($imagen_destacada_nombre, $default_images)) {
        $error_msg = "Invalid featured image selected.";
    }
    
    // 3. ¡CRUCIAL! Solo intentamos insertar en la BD si NO hay errores hasta ahora.
    if (empty($error_msg)) {
        $sql = "INSERT INTO articulos (titulo, contenido, imagen_destacada, categoria_id, autor_id, fecha_publicacion) VALUES (?, ?, ?, ?, ?, NOW())";
        
        if($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssssi", $titulo, $contenido, $imagen_destacada_nombre, $categoria, $autor_id);
            if ($stmt->execute()) {
                header("Location: " . BASE_URL . "/blogging/dashboard.php?status=created_success");
                exit();
            } else {
                $error_msg = "Error al guardar el artículo en la base de datos.";
            }
            $stmt->close();
        } else {
            $error_msg = "Error al preparar la consulta a la base de datos.";
        }
    }
    
    $conn->close();
}
// Incluir el header después de toda la lógica PHP
require_once __DIR__ . '/../homepage/_header.php';
?>

<!-- El HTML del formulario, ahora con el header y mejoras de UX -->
 <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Post</title>
    <link href="/src/output.css" rel="stylesheet">
    
    <!-- SCRIPT DE TINYMCE: Reemplaza 'TU_API_KEY_AQUI' con tu clave -->
    <script src="https://cdn.tiny.cloud/1/8511dydbgylcnrlse705ds9376k9izxpjiy7m0e3q6i8fxv4/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body class="bg-gray-100" x-data="{ modalOpen: false, selectedImage: '' }">
    <div class="container mx-auto p-4 sm:p-6 lg:p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Create New Post</h1>
            <a href="/blogging/dashboard.php" class="text-sm font-medium text-primary-600 hover:text-primary-800">← Return to dashboard</a>
        </div>

        <!-- El mensaje de error ahora será más estético y útil -->
        <?php if(!empty($error_msg)): ?>
        <div class="flex items-center bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <svg class="h-6 w-6 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
            <div>
                <p class="font-bold">Could not save</p>
                <p><?php echo $error_msg; ?></p>
            </div>
        </div>
        <?php endif; ?>

        <form action="write_blog.php" method="POST">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8"> 
                <!-- Columna Principal: Formulario -->
                <div class="lg:col-span-2 bg-white p-8 rounded-lg shadow-md space-y-6">
                    <!-- Campo Título -->
                    <div>
                        <label for="titulo" class="block text-sm font-medium text-gray-700 mb-1">Post title</label>
                        <input type="text" name="titulo" id="titulo" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500" required>
                    </div>

                    <!-- Campo Contenido con TinyMCE -->
                    <div>
                        <label for="contenido" class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                        <textarea name="contenido" id="contenido" class="h-64"></textarea>
                    </div>
                </div>

                <!-- Columna Lateral: Metadatos y Ayuda -->
                <div class="lg:col-span-1 space-y-8">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Post</h3>
                        <!-- === NEW FEATURED IMAGE SELECTOR === -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cover Image</label>
                            <!-- This hidden input will store the path of the selected image -->
                            <input type="hidden" name="imagen_destacada" x-model="selectedImage">
                            
                            <!-- Preview of the selected image -->
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
                        <!-- === END OF NEW SELECTOR === -->
                        
                        
                        <!-- Campo Categoría -->
                        <div class="mt-6">
                            <label for="categoria_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select name="categoria_id" id="categoria_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md shadow-sm" required>
                                <option value="">Select a category</option>
                                <?php foreach ($categorias_disponibles as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>">
                                        <?php echo htmlspecialchars($cat['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Botón de Envío -->
                        <div class="mt-8 border-t pt-6">
                            <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                                Post
                            </button>
                        </div>
                    </div>

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

    <!-- === NEW IMAGE GALLERY MODAL === -->
    <div x-show="modalOpen" @keydown.escape.window="modalOpen = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
        <div @click.away="modalOpen = false" class="bg-white rounded-lg shadow-xl w-full max-w-4xl p-6">
            <h3 class="text-xl font-bold mb-4">Select an Image</h3>
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-4 max-h-[60vh] overflow-y-auto">
                <?php foreach ($default_images as $image): ?>
                    <button type="button" @click="selectedImage = '<?php echo $image; ?>'; modalOpen = false;" class="aspect-w-1 aspect-h-1 block rounded-md overflow-hidden focus:outline-none focus:ring-4 focus:ring-primary-400">
                        <img src="<?php echo BASE_URL . '/uploads/default-stock/'; ?><?php echo $image; ?>" alt="Stock Image" class="w-full h-full object-cover">
                    </button>
                <?php endforeach; ?>
            </div>
            <div class="text-right mt-6">
                <button type="button" @click="modalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                    Cancel
                </button>
            </div>
        </div>
    </div>
    <!-- === END OF MODAL === -->    

    <script>
        tinymce.init({
            selector: 'textarea#contenido',
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