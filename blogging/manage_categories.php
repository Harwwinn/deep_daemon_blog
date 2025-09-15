<?php
// -- Bloque de depuración --
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Guardia de seguridad para administradores
require_once __DIR__ . '/../admin_auth.php';
require_once __DIR__ . '/../config/conexion.php';

$mensaje = '';
$categorias = [];

$categoria_a_editar = null;
$modo_edicion = false;

// Función para crear un "slug" amigable para URLs
function create_slug($string){
   $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($string));
   return trim($slug, '-');
}

// --- LOGIC FOR POST REQUESTS (ADD/EDIT/DELETE) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- ADD or EDIT a category ---
    if (isset($_POST['save_category'])) {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $nombre = trim($_POST['nombre']);
        
        if (empty($nombre)) {
            $_SESSION['form_message'] = ['tipo' => 'error', 'texto' => 'The category name cannot be empty.'];
        } else {
            $slug = create_slug($nombre);
            
            if ($id > 0) { // UPDATE mode
                $sql = "UPDATE categorias SET nombre = ?, slug = ? WHERE id = ?";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("ssi", $nombre, $slug, $id);
                    if ($stmt->execute()) {
                        $_SESSION['form_message'] = ['tipo' => 'exito', 'texto' => 'Category updated successfully.'];
                    } else {
                        $_SESSION['form_message'] = ['tipo' => 'error', 'texto' => 'Error: That category name or slug may already exist.'];
                    }
                }
            } else { // INSERT mode
                $sql = "INSERT INTO categorias (nombre, slug) VALUES (?, ?)";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("ss", $nombre, $slug);
                    if ($stmt->execute()) {
                        $_SESSION['form_message'] = ['tipo' => 'exito', 'texto' => 'Category added successfully.'];
                    } else {
                        $_SESSION['form_message'] = ['tipo' => 'error', 'texto' => 'Error: That category may already exist.'];
                    }
                }
            }
        }
    }

    // --- DELETE a category ---
    if (isset($_POST['delete_category'])) {
        $id_a_borrar = (int)$_POST['id'];
        $sql_delete = "DELETE FROM categorias WHERE id = ?";
        if ($stmt_delete = $conn->prepare($sql_delete)) {
            $stmt_delete->bind_param("i", $id_a_borrar);
            if ($stmt_delete->execute()) {
                $_SESSION['form_message'] = ['tipo' => 'exito', 'texto' => 'Category deleted successfully.'];
            } else {
                $_SESSION['form_message'] = ['tipo' => 'error', 'texto' => 'Error deleting category. It might be in use.'];
            }
        }
    }
    
    // Redirect after any POST action to prevent form resubmission
    $conn->close(); // Cerramos la conexión ANTES de redirigir
    header("Location: manage_categories.php");
    exit();
}

// Lógica para entrar en modo edición
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit'])) {
    $id_a_editar = (int)$_GET['edit'];
    $sql_edit = "SELECT id, nombre FROM categorias WHERE id = ?";
    if ($stmt_edit = $conn->prepare($sql_edit)) {
        $stmt_edit->bind_param("i", $id_a_editar);
        $stmt_edit->execute();
        $result_edit = $stmt_edit->get_result();
        if ($result_edit->num_rows === 1) {
            $categoria_a_editar = $result_edit->fetch_assoc();
            $modo_edicion = true;
        }
        $stmt_edit->close();
    }
}

// Obtener todas las categorías para mostrarlas en la tabla
$sql_select = "SELECT id, nombre, slug FROM categorias ORDER BY nombre ASC";
$result = $conn->query($sql_select);
if ($result) {
    $categorias = $result->fetch_all(MYSQLI_ASSOC);
}
$conn->close();

require_once __DIR__ . '/../homepage/_header.php';
?>

<body class="bg-gray-100">
    <div class="container mx-auto p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Gestionar Categorías</h1>
        
        <?php if (!empty($mensaje)): /* ... bloque de mensaje ... */ endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Columna para Añadir Nueva Categoría -->
            <div class="md:col-span-1">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4"><?php echo $modo_edicion ? 'Edit Category' : 'Add New Category'; ?></h2>
                    <form action="manage_categories.php" method="POST" class="space-y-4">
                        <?php if ($modo_edicion): ?>
                            <input type="hidden" name="id" value="<?php echo $categoria_a_editar['id']; ?>">
                        <?php endif; ?>
                        <input type="hidden" name="save_category" value="1">
                        <div>
                            <label for="nombre" class="block text-sm font-medium">Category name</label>
                            <input type="text" name="nombre" value="<?php echo htmlspecialchars($categoria_a_editar['nombre'] ?? ''); ?>" id="nombre" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        </div>
                        <div class="flex items-center gap-x-4">
                            <?php if ($modo_edicion): ?>
                                <a href="manage_categories.php" class="w-full text-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">Cancel</a>
                            <?php endif; ?>
                            <button type="submit" class="w-full inline-flex justify-center items-center gap-x-2 px-4 py-2 bg-primary-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                  <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                                </svg>
                                 <?php echo $modo_edicion ? 'Update Category' : 'Add Category'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Columna para Ver Categorías Existentes -->
            <div class="md:col-span-2">
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <table class="min-w-full w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug (URL)</th>
                                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Delete</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($categorias as $categoria): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($categoria['nombre']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono"><?php echo htmlspecialchars($categoria['slug']); ?></td>
                                <td class="px-6 py-4 text-right">
                                    <a href="manage_categories.php?edit=<?php echo $categoria['id']; ?>" class="font-medium text-indigo-600 hover:text-indigo-900">Edit</a>
                                    <form action="manage_categories.php" method="POST" onsubmit="return confirm('Are you sure? Deleting a category will leave its posts uncategorized.');">
                                        <input type="hidden" name="delete_category" value="1">
                                        <input type="hidden" name="id" value="<?php echo $categoria['id']; ?>">
                                        <button type="submit" class="inline-flex items-center gap-x-1 text-sm font-medium text-red-600 hover:text-red-900 transition-colors">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.58.22-2.365.468a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193v-.443A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                            </svg>
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
<?php require_once __DIR__ . '/../homepage/_footer.php'; ?>