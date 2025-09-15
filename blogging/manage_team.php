<?php
// -- Bloque de depuración --
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Guardia de seguridad para administradores
require_once __DIR__ . '/../admin_auth.php';
require_once __DIR__ . '/../config/conexion.php';

// Iniciar sesión aquí arriba si no lo hace admin_auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensaje = '';
// Comprobar si hay un mensaje de una redirección anterior
if (isset($_SESSION['form_message'])) {
    $mensaje = $_SESSION['form_message'];
    unset($_SESSION['form_message']); // Limpiar el mensaje para que no se muestre de nuevo
}

$colaborador_a_editar = null; // Variable para almacenar datos si estamos en modo edición
$modo_edicion = false;

// Función para crear un "slug" para nombres de archivo
function create_slug($string){
   $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($string));
   return trim($slug, '-');
}

// Lógica para AÑADIR o EDITAR un colaborador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_collaborator'])) {
    // Recoger y sanear datos
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nombre = trim($_POST['nombre']);
    $rol = trim($_POST['rol']);
    $bio = trim($_POST['bio']);
    $enlace_scholar = trim($_POST['enlace_scholar']) ?: null;
    $enlace_twitter = trim($_POST['enlace_twitter']) ?: null;
    $enlace_linkedin = trim($_POST['enlace_linkedin']) ?: null;
    $es_fundador = isset($_POST['es_fundador']) ? 1 : 0;
    
    // Manejo de la foto
    $foto_nombre = $_POST['foto_actual'] ?? '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $directorio = __DIR__ . '/../assets/img/';
        $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nuevo_nombre = 'collab_' . create_slug($nombre) . '_' . uniqid() . '.' . $extension;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $directorio . $nuevo_nombre)) {
            // Si hay una foto anterior y es diferente, bórrala
            if(!empty($foto_nombre) && file_exists($directorio . $foto_nombre)) {
                unlink($directorio . $foto_nombre);
            }
            $foto_nombre = $nuevo_nombre;
        }
    }
    
    if ($id > 0) { // Modo UPDATE
        $sql = "UPDATE colaboradores SET nombre=?, rol=?, bio=?, foto=?, enlace_scholar=?, enlace_twitter=?, enlace_linkedin=?, es_fundador=? WHERE id=?";
        if($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssssssii", $nombre, $rol, $bio, $foto_nombre, $enlace_scholar, $enlace_twitter, $enlace_linkedin, $es_fundador, $id);
            if($stmt->execute()) {
                $_SESSION['form_message'] = ['tipo' => 'exito', 'texto' => 'Successfully updated'];
                $success = true;
            } else {
                $_SESSION['form_message'] = ['tipo' => 'error', 'texto' => 'Something went wrong updating member.'];
            }
        }
    } else { // Modo INSERT
        $sql = "INSERT INTO colaboradores (nombre, rol, bio, foto, enlace_scholar, enlace_twitter, enlace_linkedin, es_fundador) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        if($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssssssi", $nombre, $rol, $bio, $foto_nombre, $enlace_scholar, $enlace_twitter, $enlace_linkedin, $es_fundador);
            if($stmt->execute()) {
                $_SESSION['form_message'] = ['tipo' => 'exito', 'texto' => 'Collaborator successfully added.'];
            } else {
                $_SESSION['form_message'] = ['tipo' => 'error', 'texto' => 'Something went wrong adding the member'];
            }
        }
    }

    // --- EL CAMBIO CRUCIAL: LA REDIRECCIÓN ---
    $conn->close();
    // Redirigimos a la misma página para "limpiar" la solicitud POST
    header("Location: manage_team.php");
    exit();
}

// Lógica para ELIMINAR un colaborador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_collaborator'])) {
    $id_a_borrar = (int)$_POST['id'];
    // Primero, obtener el nombre del archivo de la foto para borrarlo del servidor
    $sql_get_photo = "SELECT foto FROM colaboradores WHERE id = ?";
    if($stmt_get = $conn->prepare($sql_get_photo)) {
        $stmt_get->bind_param("i", $id_a_borrar);
        $stmt_get->execute();
        $result_photo = $stmt_get->get_result()->fetch_assoc();
        if($result_photo && !empty($result_photo['foto'])) {
            $ruta_foto = __DIR__ . '/../assets/img/' . $result_photo['foto'];
            if(file_exists($ruta_foto)) {
                unlink($ruta_foto);
            }
        }
    }
    
    $sql_delete = "DELETE FROM colaboradores WHERE id = ?";
    if($stmt_delete = $conn->prepare($sql_delete)) {
        $stmt_delete->bind_param("i", $id_a_borrar);
        if($stmt_delete->execute()) {
            $_SESSION['form_message'] = ['tipo' => 'exito', 'texto' => 'Succesfully deleted.'];
        } else {
            $_SESSION['form_message'] = ['tipo' => 'error', 'texto' => 'Something went wrong.'];
        }
    }
    // --- LA REDIRECCIÓN ---
    $conn->close();
    header("Location: manage_team.php");
    exit();
}

// Lógica para ENTRAR EN MODO EDICIÓN
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit'])) {
    $id_a_editar = (int)$_GET['edit'];
    $sql_edit = "SELECT * FROM colaboradores WHERE id = ?";
    if($stmt_edit = $conn->prepare($sql_edit)) {
        $stmt_edit->bind_param("i", $id_a_editar);
        $stmt_edit->execute();
        $colaborador_a_editar = $stmt_edit->get_result()->fetch_assoc();
        if($colaborador_a_editar) {
            $modo_edicion = true;
        }
    }
}

// Obtener todos los colaboradores para la lista
$colaboradores = $conn->query("SELECT * FROM colaboradores ORDER BY es_fundador DESC, nombre ASC")->fetch_all(MYSQLI_ASSOC);
$conn->close();

require_once __DIR__ . '/../homepage/_header.php';
?>

<body class="bg-gray-100">
    <div class="container mx-auto p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Manage team</h1>
                <p class="mt-1 text-sm text-gray-500">Add, edit or remove team members.</p>
            </div>
            <a href="/blogging/dashboard.php" class="mt-4 sm:mt-0 text-sm font-medium text-primary-600 hover:text-primary-800">← Return to Dashboard</a>
        </div>
        
        <?php if (!empty($mensaje)): ?>
            <div class="p-4 mb-6 text-sm rounded-md <?php echo ($mensaje['tipo'] == 'exito') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>" role="alert">
                <p class="font-bold"><?php echo ($mensaje['tipo'] == 'exito') ? '¡Éxito!' : 'Error'; ?></p>
                <p><?php echo htmlspecialchars($mensaje['texto']); ?></p>
            </div>
        <?php endif; ?>

        <!-- Formulario para Añadir/Editar Colaborador -->
        <div class="bg-white p-6 sm:p-8 rounded-lg shadow-md mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-4"><?php echo $modo_edicion ? 'Editing ' . htmlspecialchars($colaborador_a_editar['nombre']) : 'Add new member'; ?></h2>
            <form action="manage_team.php" method="POST" enctype="multipart/form-data" x-data="{ photoPreview: null }">
                <!-- Inputs ocultos para la lógica -->
                <?php if ($modo_edicion): ?><input type="hidden" name="id" value="<?php echo $colaborador_a_editar['id']; ?>"><?php endif; ?>
                <input type="hidden" name="save_collaborator" value="1">
                <input type="hidden" name="foto_actual" value="<?php echo htmlspecialchars($colaborador_a_editar['foto'] ?? ''); ?>">

                <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
                    <!-- Columna de Información Básica -->
                    <div class="md:col-span-4 space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="nombre" class="block text-sm font-medium text-gray-700">Name</label>
                                <!-- ESTILOS AÑADIDOS -->
                                <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($colaborador_a_editar['nombre'] ?? ''); ?>" required 
                                       class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="rol" class="block text-sm font-medium text-gray-700">Rol</label>
                                <!-- ESTILOS AÑADIDOS -->
                                <input type="text" name="rol" id="rol" value="<?php echo htmlspecialchars($colaborador_a_editar['rol'] ?? 'Colaborador'); ?>" required 
                                       class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            </div>
                        </div>
                        <div>
                            <label for="bio" class="block text-sm font-medium text-gray-700">Biography</label>
                            <!-- ESTILOS AÑADIDOS -->
                            <textarea name="bio" id="bio" rows="4" 
                                      class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"><?php echo htmlspecialchars($colaborador_a_editar['bio'] ?? ''); ?></textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div>
                                <label for="enlace_scholar" class="block text-sm font-medium text-gray-700">URL Google Scholar</label>
                                <!-- ESTILOS AÑADIDOS -->
                                <input type="url" name="enlace_scholar" value="<?php echo htmlspecialchars($colaborador_a_editar['enlace_scholar'] ?? ''); ?>" id="enlace_scholar" 
                                       class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="enlace_twitter" class="block text-sm font-medium text-gray-700">URL Personal site/Portfolio</label>
                                <!-- ESTILOS AÑADIDOS -->
                                <input type="url" name="enlace_twitter" value="<?php echo htmlspecialchars($colaborador_a_editar['enlace_twitter'] ?? ''); ?>" id="enlace_twitter" 
                                       class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="enlace_linkedin" class="block text-sm font-medium text-gray-700">URL LinkedIn</label>
                                <!-- ESTILOS AÑADIDOS -->
                                <input type="url" name="enlace_linkedin" value="<?php echo htmlspecialchars($colaborador_a_editar['enlace_linkedin'] ?? ''); ?>" id="enlace_linkedin" 
                                       class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                    <!-- Columna de Foto y Configuración -->
                    <div class="md:col-span-2 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Foto de Perfil</label>
                            <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <!-- Se muestra solo si hay una nueva imagen seleccionada -->
                                    <template x-if="photoPreview">
                                        <img :src="photoPreview" class="mx-auto h-24 w-24 rounded-lg object-cover mb-4">
                                    </template>
                                     <!-- Se muestra solo si NO hay una nueva imagen seleccionada (estado inicial) -->
                                    <template x-if="!photoPreview">
                                        <div>
                                            <?php if ($modo_edicion && !empty($colaborador_a_editar['foto'])): ?>
                                                <img src="/assets/img/<?php echo htmlspecialchars($colaborador_a_editar['foto']); ?>" class="mx-auto h-24 w-24 rounded-lg object-cover mb-4">
                                            <?php else: ?>
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                            <?php endif; ?>
                                        </div>
                                    </template>
                                    <div class="flex text-sm text-gray-600"><label for="foto" class="relative cursor-pointer bg-white rounded-md font-medium text-primary-600 hover:text-primary-500"><span>Upload photo</span><input id="foto" name="foto" type="file" class="sr-only" @change="photoPreview = URL.createObjectURL($event.target.files[0])"></label></div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <!-- ESTILOS AÑADIDOS -->
                            <input type="checkbox" name="es_fundador" id="es_fundador" value="1" <?php echo ($colaborador_a_editar['es_fundador'] ?? 0) ? 'checked' : ''; ?> 
                                   class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <label for="es_fundador" class="ml-2 block text-sm text-gray-900">Mark as founder</label>
                        </div>
                    </div>
                </div>
                 <!-- CAMBIO: Contenedor de botones mejorado con alineación y borde superior -->
                <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end items-center gap-x-4">
                    <!-- Botón Secundario: Cancelar -->
                    <?php if ($modo_edicion): ?>
                        <a href="manage_team.php" 
                           class="px-5 py-2.5 border border-gray-300 rounded-md shadow-sm text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150 ease-in-out">
                            Cancel
                        </a>
                    <?php endif; ?>

                    <!-- Botón Primario: Guardar / Actualizar -->
                    <button type="submit" 
                            class="inline-flex items-center gap-x-2 px-5 py-2.5 border border-transparent rounded-md shadow-sm text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 active:scale-95 transform transition-all duration-150 ease-in-out">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                        <?php echo $modo_edicion ? 'Update member info.' : 'Save'; ?>
                    </button>
                </div>
            </form>
        </div>

        <!-- Lista de Colaboradores Existentes -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <h2 class="text-2xl font-semibold text-gray-800 p-6 border-b">Team</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full w-full">
                    <!-- ... (El thead no cambia) ... -->
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach($colaboradores as $colab): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4"><img src="/assets/img/<?php echo htmlspecialchars($colab['foto'] ?? 'default.png'); ?>" class="h-12 w-12 rounded-full object-cover"></td>
                            <td class="px-6 py-4 font-medium text-gray-900"><?php echo htmlspecialchars($colab['nombre']); ?></td>
                            <td class="px-6 py-4 text-gray-500"><?php echo htmlspecialchars($colab['rol']); ?></td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="manage_team.php?edit=<?php echo $colab['id']; ?>" class="inline-flex items-center justify-center p-2 text-indigo-600 bg-indigo-100 rounded-full hover:bg-indigo-200" title="Edit">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" /><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" /></svg>
                                </a>
                                <form action="manage_team.php" method="POST" onsubmit="return confirm('¿Seguro?')" class="inline">
                                    <input type="hidden" name="delete_collaborator" value="1"><input type="hidden" name="id" value="<?php echo $colab['id']; ?>">
                                    <button type="submit" class="p-2 text-red-600 bg-red-100 rounded-full hover:bg-red-200" title="Delete">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.58.22-2.365.468a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193v-.443A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" /></svg>
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
</body>
<?php require_once __DIR__ . '/../homepage/_footer.php'; ?>