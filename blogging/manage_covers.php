<?php
// -- Bloque de depuración --
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Security Guardian for admins
require_once __DIR__ . '/../admin_auth.php';
require_once __DIR__ . '/../config/conexion.php'; // Needed if you add DB interaction later

// Start session for status messages
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$mensaje = '';
if (isset($_SESSION['form_message'])) {
    $mensaje = $_SESSION['form_message'];
    unset($_SESSION['form_message']);
}

$upload_dir = __DIR__ . '/../uploads/default-stock/';

// --- LOGIC FOR UPLOADING A NEW IMAGE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['new_cover_photo'])) {
    $file = $_FILES['new_cover_photo'];
    
    // Check for upload errors
    if ($file['error'] === UPLOAD_ERR_OK) {
        $max_file_size = 2 * 1024 * 1024; // 2 MB
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        // 1. Validate size
        if ($file['size'] > $max_file_size) {
            $_SESSION['form_message'] = ['tipo' => 'error', 'texto' => 'The image is too large. Maximum size is 2 MB.'];
        } else {
            // 2. Validate MIME type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (in_array($mime_type, $allowed_types)) {
                // Sanitize filename to prevent directory traversal attacks
                $filename = basename($file['name']); 
                $destination = $upload_dir . $filename;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $_SESSION['form_message'] = ['tipo' => 'exito', 'texto' => 'Image "' . htmlspecialchars($filename) . '" uploaded successfully.'];
                } else {
                    $_SESSION['form_message'] = ['tipo' => 'error', 'texto' => 'Failed to move the uploaded file. Check folder permissions.'];
                }
            } else {
                $_SESSION['form_message'] = ['tipo' => 'error', 'texto' => 'Invalid file format. Only JPG, PNG, GIF, and WEBP are allowed.'];
            }
        }
    } else {
         $_SESSION['form_message'] = ['tipo' => 'error', 'texto' => 'An error occurred during upload. Please try again.'];
    }

    header("Location: manage_covers.php");
    exit();
}

// --- LOGIC FOR DELETING AN IMAGE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_cover'])) {
    $filename_to_delete = basename($_POST['filename']); // basename() for security
    $file_path = $upload_dir . $filename_to_delete;

    if (file_exists($file_path) && is_writable($file_path)) {
        if (unlink($file_path)) {
            $_SESSION['form_message'] = ['tipo' => 'exito', 'texto' => 'Image "' . htmlspecialchars($filename_to_delete) . '" deleted successfully.'];
        } else {
            $_SESSION['form_message'] = ['tipo' => 'error', 'texto' => 'Could not delete the image file.'];
        }
    } else {
        $_SESSION['form_message'] = ['tipo' => 'error', 'texto' => 'File not found or permission denied.'];
    }
    
    header("Location: manage_covers.php");
    exit();
}

// Read the directory to get the list of current images
$default_images = array_diff(scandir($upload_dir), ['.', '..']); // scandir gets all files, array_diff removes '.' and '..'

require_once __DIR__ . '/../homepage/_header.php';
?>

<body class="bg-gray-100">
    <div class="container mx-auto p-4 sm:p-6 lg:p-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Manage Cover Photos</h1>
            <a href="/blogging/dashboard.php" class="text-sm font-medium text-primary-600 hover:text-primary-800">← Back to Dashboard</a>
        </div>
        
                <?php if (!empty($mensaje)): ?>
                    <div class="p-4 mb-6 text-sm rounded-md <?php echo ($mensaje['tipo'] == 'exito') ? 'bg-green-100 border border-green-300 text-green-800' : 'bg-red-100 border border-red-300 text-red-800'; ?>" role="alert">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <?php if ($mensaje['tipo'] == 'exito'): ?>
                                    <!-- Checkmark Icon for Success -->
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                    </svg>
                                <?php else: ?>
                                    <!-- X Circle Icon for Error -->
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <div class="ml-3">
                                <p class="font-bold"><?php echo ($mensaje['tipo'] == 'exito') ? 'Success!' : 'Error'; ?></p>
                                <p><?php echo htmlspecialchars($mensaje['texto']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
        
        <!-- Upload Form -->
        <div class="bg-white p-6 sm:p-8 rounded-lg shadow-md mb-8">
            <h2 class="text-xl font-semibold text-gray-800 border-b pb-4 mb-6">Upload New Cover Photo</h2>
            <form action="manage_covers.php" method="POST" enctype="multipart/form-data">
                <div>
                    <label for="new_cover_photo" class="block text-sm font-medium text-gray-700">Select Image File</label>
                    <div class="mt-2 flex items-center gap-x-4">
                        <input type="file" name="new_cover_photo" id="new_cover_photo" required 
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        <input type="hidden" name="MAX_FILE_SIZE" value="2097152" /> <!-- 2 MB Client-side limit -->
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-primary-700">
                            Upload
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Maximum file size: 2 MB. Allowed formats: JPG, PNG, GIF, WEBP.</p>
                </div>
            </form>
        </div>

                <!-- Gallery of available images -->
        <div class="bg-white p-6 sm:p-8 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-800 border-b pb-4 mb-6">Available Gallery</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                <?php if (empty($default_images)): ?>
                    <p class="col-span-full text-center text-gray-500">The gallery is empty. Upload an image to get started.</p>
                <?php else: ?>
                    <?php foreach ($default_images as $image): ?>
                        
                        <!-- === INICIO DE LA TARJETA DE IMAGEN SIMPLE (SIN OVERLAY) === -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg overflow-hidden flex flex-col">
                            
                            <!-- 1. La Imagen -->
                            <div class="aspect-w-1 aspect-h-1">
                                <img src="/uploads/default-stock/<?php echo htmlspecialchars($image); ?>" 
                                     alt="Stock Cover Image" 
                                     class="w-full h-full object-cover">
                            </div>
                            
                            <!-- 2. Información y Acción (debajo de la imagen) -->
                            <div class="p-3 flex-grow flex flex-col justify-between">
                                <!-- Nombre del archivo -->
                                <p class="text-xs text-gray-600 break-all leading-tight">
                                    <?php echo htmlspecialchars($image); ?>
                                </p>
                                
                                <!-- Formulario de borrado -->
                                <form action="manage_covers.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this image?');" class="mt-2 self-end">
                                    <input type="hidden" name="delete_cover" value="1">
                                    <input type="hidden" name="filename" value="<?php echo htmlspecialchars($image); ?>">
                                    <button type="submit" class="p-1.5 text-red-600 bg-red-100 rounded-full hover:bg-red-200" title="Delete Image">
                                        <svg class="h-4 w-4" xmlns="http://www.w.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.58.22-2.365.468a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193v-.443A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <!-- === FIN DE LA TARJETA DE IMAGEN SIMPLE === -->

                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
<?php
// Include the footer
require_once __DIR__ . '/../homepage/_footer.php';
?>