<?php
// -- Bloque de depuración --
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Guardia de seguridad para administradores
require_once __DIR__ . '/../admin_auth.php';
require_once __DIR__ . '/../config/conexion.php';

// Iniciar sesión para manejar mensajes de estado
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$mensaje = '';
// Comprobar si hay un mensaje de una redirección anterior (patrón PRG)
if (isset($_SESSION['form_message'])) {
    $mensaje = $_SESSION['form_message'];
    unset($_SESSION['form_message']);
}

$usuario_a_editar = null;
$modo_edicion = false;


// Definir avatars por defecto
$default_avatars = [
    'avatar1.svg',
    'avatar2.svg',
    'avatar3.svg',        
    'avatar4.svg',
    'avatar5.svg',
    'avatar6.svg',
    'avatar7.svg',
    'avatar8.svg',
    'avatar9.svg',
    'avatar10.svg',
];

// --- LÓGICA POST COMPLETA (AÑADIR/EDITAR/ELIMINAR) ---
function esContrasenaSegura($contrasena) {
    // Mínimo 8 caracteres
    if (strlen($contrasena) < 8) return false;
    // Debe contener al menos una letra mayúscula
    if (!preg_match('/[A-Z]/', $contrasena)) return false;
    // Debe contener al menos una letra minúscula
    if (!preg_match('/[a-z]/', $contrasena)) return false;
    // Debe contener al menos un número
    if (!preg_match('/[0-9]/', $contrasena)) return false;
    // Debe contener al menos un símbolo
    if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $contrasena)) return false;

    return true;
}

// Lógica para AÑADIR o EDITAR un usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_user'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $rol = $_POST['rol'];
    $contrasena = trim($_POST['contrasena']);
    $avatar = $_POST['avatar'] ?? null;

    // Security check for avatar
    if (!empty($avatar) && !in_array($avatar, $default_avatars)) {
        $avatar = null;
    }

    if ($id === $_SESSION['user_id'] && $rol !== 'admin') {
        $_SESSION['form_message'] = ['tipo' => 'error', 'texto' => 'You cannot change your own administrator role.'];
        header("Location: manage_users.php?edit=" . $id);
        exit();
    }

    if ($id > 0) { // MODO UPDATE
        if (!empty($contrasena) && !esContrasenaSegura($contrasena)) {
            $_SESSION['form_message'] = ['tipo' => 'error', 'texto' => 'The new password is not secure. It must meet the requirements.'];
            header("Location: manage_users.php?edit=" . $id);
            exit();
        }
        $sql = "UPDATE usuarios SET nombre = ?, correo = ?, rol = ?, avatar = ? WHERE id = ?";
        if (!empty($contrasena)) {
            $sql = "UPDATE usuarios SET nombre = ?, correo = ?, rol = ?, avatar = ?, contrasena = ? WHERE id = ?";
        }
        if ($stmt = $conn->prepare($sql)) {
            if (!empty($contrasena)) {
                $contrasena_hash = password_hash($contrasena, PASSWORD_BCRYPT);
                $stmt->bind_param("sssssi", $nombre, $correo, $rol, $avatar, $contrasena_hash, $id);
            } else {
                $stmt->bind_param("ssssi", $nombre, $correo, $rol, $avatar, $id);
            }
            if ($stmt->execute()) {
                if ($id === $_SESSION['user_id']) {
                    // Si es el mismo, actualizamos los datos de la sesión para que el cambio se refleje inmediatamente
                    $_SESSION['user_nombre'] = $nombre;
                    $_SESSION['user_avatar'] = $avatar;
                    // Opcional: también podrías querer actualizar el rol si lo cambiaste
                    $_SESSION['user_rol'] = $rol; 
                }
                $_SESSION['form_message'] = ['tipo' => 'exito', 'texto' => 'User successfully updated.'];
            } else {
                $_SESSION['form_message'] = ['tipo' => 'error', 'texto' => 'Error updating. The email address may already be in use.'];
            }
            $stmt->close();
        }
    } else { // --- LÓGICA DE INSERT ---
        if (empty($nombre) || empty($correo) || empty($contrasena) || empty($rol)) {
            $_SESSION['form_message'] = ['tipo' => 'error', 'texto' => 'All fields are required to create a new user.'];
        } elseif (!empty($contrasena) && !esContrasenaSegura($contrasena)) {
            $_SESSION['form_message'] = ['tipo' => 'error', 'texto' => 'The password is not secure. It must have at least 8 characters, one uppercase letter, one lowercase letter, one number, and one symbol.'];
        }else {
            $contrasena_hash = password_hash($contrasena, PASSWORD_BCRYPT);
            $sql = "INSERT INTO usuarios (nombre, correo, contrasena, rol, avatar) VALUES (?, ?, ?, ?, ?)";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("sssss", $nombre, $correo, $contrasena_hash, $rol, $avatar);
                if ($stmt->execute()) {
                    $_SESSION['form_message'] = ['tipo' => 'exito', 'texto' => 'User created successfully.'];
                } else {
                    if ($conn->errno == 1062) {
                        $_SESSION['form_message'] = ['tipo' => 'error', 'texto' => 'The email is already registered.'];
                    } else {
                        $_SESSION['form_message'] = ['tipo' => 'error', 'texto' => 'The user could not be created.'];
                    }
                }
                $stmt->close();
            }
        }
    }
    header("Location: manage_users.php");
    exit();
}

// --- LÓGICA DE DELETE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $id_a_borrar = (int)$_POST['id'];
    $admin_id = $_SESSION['user_id'];

    if ($id_a_borrar === $admin_id) {
        $_SESSION['form_message'] = ['tipo' => 'error', 'texto' => 'You cannot delete your own administrator account.'];
        header("Location: manage_users.php");
        exit();
    }

    $sql_check_articles = "SELECT COUNT(*) as article_count FROM articulos WHERE autor_id = ?";
    if ($stmt_check = $conn->prepare($sql_check_articles)) {
        $stmt_check->bind_param("i", $id_a_borrar);
        $stmt_check->execute();
        $result = $stmt_check->get_result()->fetch_assoc();
        if ($result['article_count'] > 0) {
            $_SESSION['form_message'] = ['tipo' => 'error', 'texto' => 'You can not delete a user who has published articles. Reassign or delete their articles first.'];
            header("Location: manage_users.php");
            exit();
        }
        $stmt_check->close();
    }
    
    $sql_delete = "DELETE FROM usuarios WHERE id = ?";
    if ($stmt_delete = $conn->prepare($sql_delete)) {
        $stmt_delete->bind_param("i", $id_a_borrar);
        if ($stmt_delete->execute()) {
            $_SESSION['form_message'] = ['tipo' => 'exito', 'texto' => 'User successfully deleted.'];
        } else {
            $_SESSION['form_message'] = ['tipo' => 'error', 'texto' => 'Something went wrong deleting user.'];
        }
        $stmt_delete->close();
    }
    header("Location: manage_users.php");
    exit();
}


// Lógica para ENTRAR EN MODO EDICIÓN (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit'])) {
    $id_a_editar = (int)$_GET['edit'];
    $sql_edit = "SELECT id, nombre, correo, rol, avatar FROM usuarios WHERE id = ?";
    if ($stmt_edit = $conn->prepare($sql_edit)) {
        $stmt_edit->bind_param("i", $id_a_editar);
        $stmt_edit->execute();
        $usuario_a_editar = $stmt_edit->get_result()->fetch_assoc();
        if ($usuario_a_editar) {
            $modo_edicion = true;
        }
        $stmt_edit->close();
    }
}

// Obtener todos los usuarios para la lista
// Obtener todos los usuarios de la base de datos
$usuarios = [];
$sql = "SELECT id, nombre, correo, rol, avatar FROM usuarios ORDER BY nombre ASC"; // <-- Asegúrate de que esta línea esté correcta
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
}
$conn->close();

require_once __DIR__ . '/../homepage/_header.php';
?>

<body class="bg-gray-100">
    <div class="container mx-auto p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Manage Users</h1>
        
        <?php if (!empty($mensaje)): /* ... bloque de mensaje ... */ endif; ?>

        <!-- Formulario para Añadir/Editar Usuario -->
        <div class="bg-white p-8 rounded-lg shadow-md mb-8">
            <h2 class="text-2xl font-semibold mb-6 border-b pb-4">
                <?php echo $modo_edicion ? 'Editing User"' . htmlspecialchars($usuario_a_editar['nombre']) . '"' : 'Add New User'; ?>
            </h2>
            
            <!-- Componente Alpine.js para la validación en vivo de la contraseña -->
            <form action="manage_users.php" method="POST" class="space-y-6"
                  x-data="{
                      selectedAvatar: '<?php echo htmlspecialchars($usuario_a_editar['avatar'] ?? ''); ?>',
                      isEditMode: <?php echo $modo_edicion ? 'true' : 'false'; ?>,
                      formData: {
                          nombre: '<?php echo addslashes(htmlspecialchars($usuario_a_editar['nombre'] ?? '')); ?>',
                          correo: '<?php echo addslashes(htmlspecialchars($usuario_a_editar['correo'] ?? '')); ?>',
                          contrasena: '',
                          rol: '<?php echo addslashes(htmlspecialchars($usuario_a_editar['rol'] ?? 'usuario')); ?>'
                      },
                      errors: {},
                      validations: { length: false, uppercase: false, lowercase: false, number: false, symbol: false },
                      
                      validatePassword() {
                          this.validations.length = this.formData.contrasena.length >= 8;
                          this.validations.uppercase = /[A-Z]/.test(this.formData.contrasena);
                          this.validations.lowercase = /[a-z]/.test(this.formData.contrasena);
                          this.validations.number = /[0-9]/.test(this.formData.contrasena);
                          this.validations.symbol = /[\'^£$%&*()}{@#~?><>,|=_+¬-]/.test(this.formData.contrasena);
                          return Object.values(this.validations).every(Boolean);
                      },

                      validateForm() {
                          this.errors = {};
                          if (!this.formData.nombre) this.errors.nombre = 'Name is required.';
                          if (!this.formData.correo) {
                              this.errors.correo = 'Email is required.';
                          } else if (!/^\S+@\S+\.\S+$/.test(this.formData.correo)) {
                              this.errors.correo = 'Invalid email format.';
                          }
                          if (!this.isEditMode && !this.formData.contrasena) {
                              this.errors.contrasena = 'Password is required.';
                          }
                          if (this.formData.contrasena && !this.validatePassword()) {
                              this.errors.contrasena = 'Password does not meet requirements.';
                          }
                          return Object.keys(this.errors).length === 0;
                      },

                      handleSubmit(event) {
                           // Simplemente ejecutamos la validación.
                          // Si devuelve 'false', detenemos el envío.
                          if (!this.validateForm()) {
                              event.preventDefault();
                          }
                          // Si devuelve 'true', el formulario se envía normalmente.
                          // El div de error aparecerá automáticamente porque
                          // validateForm() ya ha rellenado el objeto 'errors'.
                      }
                  }"
                  @submit="handleSubmit">
                
                <?php if ($modo_edicion): ?><input type="hidden" name="id" value="<?php echo $usuario_a_editar['id']; ?>"><?php endif; ?>
                <input type="hidden" name="save_user" value="1">
                
                <!-- Avatar Selector -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Avatar</label>
                    <input type="hidden" name="avatar" x-model="selectedAvatar">
                    <div class="mt-2 grid grid-cols-5 sm:grid-cols-10 gap-3">
                        <?php foreach($default_avatars as $avatar_file): ?>
                            <button type="button" @click="selectedAvatar = '<?php echo $avatar_file; ?>'" 
                                    :class="{ 'ring-4 ring-primary-500 ring-offset-2': selectedAvatar === '<?php echo $avatar_file; ?>' }"
                                    class="relative rounded-full focus:outline-none transition-all duration-150">
                                <img class="h-14 w-14 rounded-full" src="/assets/img/avatars/<?php echo $avatar_file; ?>" alt="Avatar option">
                                <div x-show="selectedAvatar === '<?php echo $avatar_file; ?>'" class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center">
                                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.052-.143z" clip-rule="evenodd" />
                                    </svg><!-- Checkmark Icon -->
                                </div>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="nombre" id="nombre" x-model="formData.nombre"  
                               :class="{ 'border-red-500': errors.nombre, 'border-gray-300': !errors.nombre }" 
                               class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        <p x-show="errors.nombre" x-text="errors.nombre" class="mt-1 text-sm text-red-600"></p>
                    </div>
                    <div>
                        <label for="correo" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="correo" id="correo" x-model="formData.correo"
                               :class="{ 'border-red-500': errors.correo, 'border-gray-300': !errors.correo }" 
                               class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        <p x-show="errors.correo" x-text="errors.correo" class="mt-1 text-sm text-red-600"></p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    <div>
                        <label for="contrasena" class="block text-sm font-medium text-gray-700">Password</label>
                        <!-- Alpine.js escucha los cambios en este campo -->
                        <input type="password" name="contrasena" id="contrasena" 
                               x-model="formData.contrasena" @input="validatePassword"
                               :class="{ 'border-red-500': errors.contrasena, 'border-gray-300': !errors.contrasena }"
                               placeholder="<?php echo $modo_edicion ? 'Dejar en blanco para no cambiar' : ''; ?>" 
                               <?php echo !$modo_edicion ? '' : ''; ?> 
                               class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        <p x-show="errors.contrasena" x-text="errors.contrasena" class="mt-1 text-sm text-red-600"></p>
                    </div>
                    
                    <!-- Indicador visual de seguridad de contraseña -->
                    <div x-show="formData.contrasena.length > 0" class="mt-2 p-4 bg-gray-50 border rounded-lg space-y-2" x-transition>
                        <p class="text-sm font-semibold text-gray-800">The password must contain:</p>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li class="flex items-center" :class="{ 'text-green-600': validations.length, 'text-gray-500': !validations.length }">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                At least 8 characters
                            </li>
                            <li class="flex items-center" :class="{ 'text-green-600': validations.lowercase, 'text-gray-500': !validations.lowercase }">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                A lowercase letter
                            </li>
                            <li class="flex items-center" :class="{ 'text-green-600': validations.uppercase, 'text-gray-500': !validations.uppercase }">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                A CAPITAL LETTER
                            </li>
                            <li class="flex items-center" :class="{ 'text-green-600': validations.number, 'text-gray-500': !validations.number }">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                At least one number
                            </li>
                            <li class="flex items-center" :class="{ 'text-green-600': validations.symbol, 'text-gray-500': !validations.symbol }">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                At least one symbol (@, #, $, etc.)
                            </li>
                        </ul>
                    </div>
                </div>

                <div>
                    <label for="rol" class="block text-sm font-medium text-gray-700">Rol</label>
                    <select name="rol" id="rol" x-model="formData.rol"  
                            class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        <option value="usuario" <?php echo (($usuario_a_editar['rol'] ?? 'usuario') === 'usuario') ? 'selected' : ''; ?>>User</option>
                        <option value="admin" <?php echo (($usuario_a_editar['rol'] ?? '') === 'admin') ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>

                <!-- NUEVO: Bloque para el mensaje de error general -->
                <div x-show="Object.keys(errors).length > 0" class="p-4 bg-red-50 border-l-4 border-red-400 text-red-700" x-transition>
                    <p class="font-bold">Something went wrong</p>
                    <p>Please review the fields marked in red before saving.</p>
                </div>

                <!-- Contenedor de Botones de Acción -->
                <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end items-center gap-x-4">
                    <!-- Botón Secundario: Cancelar (solo en modo edición) -->
                    <?php if ($modo_edicion): ?>
                        <a href="manage_users.php" 
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
                        <?php echo $modo_edicion ? 'Update User' : 'Create User'; ?>
                    </button>
                </div>
            </form>
        </div>

                <!-- Lista de Usuarios Existentes -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
             <h2 class="text-2xl font-semibold text-gray-800 p-6 border-b border-gray-200">All Users</h2>
             <div class="overflow-x-auto">
                <table class="min-w-full w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                            <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($usuarios)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    No users found
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($usuarios as $user): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <!-- Display avatar in table -->
                                                <img class="h-10 w-10 rounded-full" src="/assets/img/avatars/<?php echo htmlspecialchars($user['avatar'] ?? 'avatar10.svg'); ?>" alt="User avatar">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['nombre']); ?></div>
                                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($user['correo']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo ($user['rol'] === 'admin') ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'; ?>">
                                            <?php echo ucfirst(htmlspecialchars($user['rol'])); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="manage_users.php?edit=<?php echo $user['id']; ?>" 
                                               class="p-2 text-indigo-600 bg-indigo-100 rounded-full hover:bg-indigo-200 hover:text-indigo-900 transition-colors" 
                                               title="Edit">
                                                <span class="sr-only">Edit</span>
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                                    <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                <form action="manage_users.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This action is irreversible.')" class="inline">
                                                    <input type="hidden" name="delete_user" value="1">
                                                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                    <button type="submit" 
                                                            class="p-2 text-red-600 bg-red-100 rounded-full hover:bg-red-200 hover:text-red-900 transition-colors" 
                                                            title="Delete">
                                                        <span class="sr-only">Delete</span>
                                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.58.22-2.365.468a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193v-.443A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>