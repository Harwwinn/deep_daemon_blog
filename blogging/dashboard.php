<?php
// -- Bloque de depuración --
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Guardia de seguridad y sesión
require_once __DIR__ . '/../auth.php'; 
// Conexión a la base de datos
require_once __DIR__ . '/../config/conexion.php';

$user_id = $_SESSION['user_id'];
$mensaje_perfil = '';
$mensaje_pass = ''; // Mensaje específico para el cambio de contraseña

// Definir avatars por defecto
$default_avatars = [
    'avatar1.svg', 'avatar2.svg', 'avatar3.svg', 'avatar4.svg', 'avatar5.svg', 
    'avatar6.svg', 'avatar7.svg', 'avatar8.svg', 'avatar9.svg', 'avatar10.svg'
];

// --- LÓGICA PARA ACTUALIZAR DATOS (PERFIL O CONTRASEÑA) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- ACTUALIZAR PERFIL (NOMBRE Y CORREO) ---
    if (isset($_POST['update_profile'])) {
        $nombre = trim($_POST['nombre']);
        $correo = trim($_POST['correo']);
        $avatar = $_POST['avatar'] ?? null;

        // Security check: validate the chosen avatar
        if (!empty($avatar) && !in_array($avatar, $default_avatars)) {
            $avatar = null; // If invalid, set to null
        }
        
        $sql_update_user = "UPDATE usuarios SET nombre = ?, correo = ?, avatar = ? WHERE id = ?";
        if($stmt_user = $conn->prepare($sql_update_user)) {
            $stmt_user->bind_param("sssi", $nombre, $correo, $avatar, $user_id);
            try {
                if ($stmt_user->execute()) {
                    $_SESSION['user_nombre'] = $nombre;
                    $_SESSION['user_avatar'] = $avatar;
                    $mensaje_perfil = ['tipo' => 'exito', 'texto' => 'Profile updated successfully.'];
                }
            } catch (mysqli_sql_exception $e) {
                if ($e->getCode() == 1062) {
                    $mensaje_perfil = ['tipo' => 'error', 'texto' => 'That email is already in use.'];
                } else {
                    $mensaje_perfil = ['tipo' => 'error', 'texto' => 'Something went wrong updating profile.'];
                }
            }
            $stmt_user->close();
        }
    }

    // --- NUEVO: ACTUALIZAR CONTRASEÑA ---
    if (isset($_POST['update_password'])) {
        $current_pass = $_POST['current_password'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];

        // 1. Obtener la contraseña hasheada actual de la BD
        $sql_get_pass = "SELECT contrasena FROM usuarios WHERE id = ?";
        if ($stmt_get = $conn->prepare($sql_get_pass)) {
            $stmt_get->bind_param("i", $user_id);
            $stmt_get->execute();
            $result = $stmt_get->get_result();
            $user = $result->fetch_assoc();
            $current_hash = $user['contrasena'];

            // 2. Verificar que la contraseña actual sea correcta
            if (password_verify($current_pass, $current_hash)) {
                // 3. Verificar que la nueva contraseña y su confirmación coincidan
                if ($new_pass === $confirm_pass) {
                    // 4. Validar la longitud de la nueva contraseña
                    if (strlen($new_pass) >= 8) {
                        // 5. Hashear y guardar la nueva contraseña
                        $new_hash = password_hash($new_pass, PASSWORD_BCRYPT);
                        $sql_update_pass = "UPDATE usuarios SET contrasena = ? WHERE id = ?";
                        if ($stmt_update = $conn->prepare($sql_update_pass)) {
                            $stmt_update->bind_param("si", $new_hash, $user_id);
                            if ($stmt_update->execute()) {
                                $mensaje_pass = ['tipo' => 'exito', 'texto' => 'Password updated successfully.'];
                            } else {
                                $mensaje_pass = ['tipo' => 'error', 'texto' => 'Error saving new password.'];
                            }
                            $stmt_update->close();
                        }
                    } else {
                        $mensaje_pass = ['tipo' => 'error', 'texto' => 'The new password must be at least 8 characters long.'];
                    }
                } else {
                    $mensaje_pass = ['tipo' => 'error', 'texto' => 'The new passwords do not match.'];
                }
            } else {
                $mensaje_pass = ['tipo' => 'error', 'texto' => 'The current password is incorrect.'];
            }
            $stmt_get->close();
        }
    }
}

// --- OBTENER DATOS DEL USUARIO Y ARTÍCULOS ---
// Obtener datos actualizados del usuario para el formulario
$sql_user = "SELECT nombre, correo, avatar FROM usuarios WHERE id = ?";
$stmt_user_fetch = $conn->prepare($sql_user);
$stmt_user_fetch->bind_param("i", $user_id);
$stmt_user_fetch->execute();
$user_data = $stmt_user_fetch->get_result()->fetch_assoc();
$stmt_user_fetch->close();

// Obtener los datos del usuario de la sesión actual
$user_id = $_SESSION['user_id'];
$user_rol = $_SESSION['user_rol']; // Asumiendo que guardas el rol como 'admin' o 'usuario'

// Array para guardar los artículos
$articulos = [];

// 1. Empezamos con la parte base de la consulta SQL
//    Añadimos el JOIN con la tabla 'usuarios' para obtener el nombre del autor
$sql_articles = "SELECT 
                    a.id, 
                    a.titulo, 
                    a.visitas, 
                    a.fecha_publicacion, 
                    IFNULL(c.nombre, 'No category') AS categoria_nombre,
                    u.nombre AS autor_nombre
                 FROM 
                    articulos a 
                 LEFT JOIN 
                    categorias c ON a.categoria_id = c.id
                 LEFT JOIN
                    usuarios u ON a.autor_id = u.id";

// 2. Comprobamos el rol del usuario para añadir la cláusula WHERE
if ($user_rol !== 'admin') {
    // Si NO es admin, filtramos por su ID, como antes
    $sql_articles .= " WHERE a.autor_id = ?";
}

// 3. Añadimos el ordenamiento al final de la consulta
$sql_articles .= " ORDER BY a.fecha_publicacion DESC";

// 4. Preparamos la sentencia
if ($stmt_articles = $conn->prepare($sql_articles)) {
    
    // 5. Vinculamos el parámetro SOLO si es necesario (es decir, si no es admin)
    if ($user_rol !== 'admin') {
        $stmt_articles->bind_param("i", $user_id);
    }
    
    // 6. Ejecutamos la consulta
    $stmt_articles->execute();
    $result = $stmt_articles->get_result();
    
    // 7. Recogemos los resultados (esta parte no cambia)
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $articulos[] = $row;
        }
    }
    
    $stmt_articles->close();
}
$conn->close();


// Función para formatear fechas
function format_date_dashboard($date_string) {
    $date = new DateTime($date_string);
    return $date->format('d/m/Y H:i');
}

// Incluir el header después de toda la lógica de PHP
require_once __DIR__ . '/../homepage/_header.php';
?>

<!-- Contenido del Dashboard -->
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">My Dashboard</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Columna Izquierda: Perfil y Contraseña -->
        <div class="lg:col-span-1 space-y-8">
            <!-- Sección Mi Perfil -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">My Profile</h2>
                <?php if (!empty($mensaje_perfil)): ?>
                    <div class="p-3 mb-4 text-sm rounded-lg <?php echo ($mensaje_perfil['tipo'] == 'exito') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                        <?php echo htmlspecialchars($mensaje_perfil['texto']); ?>
                    </div>
                <?php endif; ?>

                <!-- Alpine.js component to manage live avatar selection -->
                <form action="dashboard.php" method="POST" class="space-y-6" 
                      x-data="{ selectedAvatar: '<?php echo htmlspecialchars($user_data['avatar'] ?? ''); ?>' }">
                    
                    <input type="hidden" name="update_profile" value="1">
                    
                    <!-- Name Field -->
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($user_data['nombre']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    
                    <!-- Email Field -->
                    <div>
                        <label for="correo" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="correo" id="correo" value="<?php echo htmlspecialchars($user_data['correo']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <!-- NEW: Avatar Selector -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Avatar</label>
                        <!-- Hidden input to store the selected avatar filename -->
                        <input type="hidden" name="avatar" x-model="selectedAvatar">
                        
                        <div class="mt-2 grid grid-cols-4 sm:grid-cols-6 gap-3">
                            <?php foreach($default_avatars as $avatar_file): ?>
                                <button type="button" @click="selectedAvatar = '<?php echo $avatar_file; ?>'" 
                                        :class="{ 'ring-4 ring-primary-500 ring-offset-2': selectedAvatar === '<?php echo $avatar_file; ?>' }"
                                        class="relative rounded-full focus:outline-none transition-all duration-150"
                                        title="<?php echo ucfirst(str_replace(['.svg', '-'], ' ', $avatar_file)); ?>">
                                    <img class="h-16 w-16 rounded-full" src="/assets/img/avatars/<?php echo $avatar_file; ?>" alt="Avatar option">
                                    <!-- Checkmark icon overlay -->
                                    <div x-show="selectedAvatar === '<?php echo $avatar_file; ?>'" class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center">
                                        <svg class="h-7 w-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.052-.143z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="text-right pt-4 border-t">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">Save changes</button>
                    </div>
                </form>
            </div>

            <!-- NUEVO: Sección Cambiar Contraseña -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Change Password</h2>
                <?php if (!empty($mensaje_pass)): ?>
                    <div class="p-3 mb-4 text-sm rounded-lg <?php echo ($mensaje_pass['tipo'] == 'exito') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                        <?php echo htmlspecialchars($mensaje_pass['texto']); ?>
                    </div>
                <?php endif; ?>
                <form action="dashboard.php" method="POST" class="space-y-4">
                    <input type="hidden" name="update_password" value="1">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700">Current password</label>
                        <input type="password" name="current_password" id="current_password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500" required>
                    </div>
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700">New password</label>
                        <input type="password" name="new_password" id="new_password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500" required minlength="8">
                    </div>
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500" required>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-700 hover:bg-gray-800">Update password</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Columna Derecha: Artículos del Usuario -->
        <div class="lg:col-span-2">
            <div class="space-y-4"> 
                <!-- Header Section for Articles Table -->
                <div class="space-y-4">
                    <!-- Top row: Main Title -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-800">My Articles</h2>
                            <p class="text-sm text-gray-500">Manage all your published content here.</p>
                        </div>
                    </div>
        
                    
                    <!-- Fila de Botones de Acción -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-t border-gray-200 pt-4">
                        
                        <!-- Grupo 1: Acciones de Contenido (siempre a la izquierda) -->
                        <div class="flex items-center flex-wrap gap-3">
                            <span class="text-sm font-medium text-gray-500 mr-2">Content:</span>
                            <!-- Botón Primario "Crear Artículo" -->
                            <a href="/blogging/write_blog.php" 
                               class="inline-flex items-center gap-x-2 px-4 py-2 bg-primary-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                  <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                                Create Article
                            </a>
                            <!-- Botón Secundario "Cover Photos" (SOLO ADMINS) -->
                            <?php if ($_SESSION['user_rol'] === 'admin'): ?>
                            <a href="/blogging/manage_covers.php" 
                               class="inline-flex items-center gap-x-2 px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                  <path fill-rule="evenodd" d="M1 5.25A2.25 2.25 0 013.25 3h13.5A2.25 2.25 0 0119 5.25v9.5A2.25 2.25 0 0116.75 17H3.25A2.25 2.25 0 011 14.75v-9.5zm1.5 5.81v3.69c0 .414.336.75.75.75h13.5a.75.75 0 00.75-.75v-3.69l-2.6-2.6a.75.75 0 00-1.06 0l-2.6 2.6-1.72-1.72a.75.75 0 00-1.06 0l-3.25 3.25a.75.75 0 00-.02 1.047l.02.013zM3.25 4.5a.75.75 0 00-.75.75v3.19l2.47-2.47a2.25 2.25 0 013.18 0l1.72 1.72 2.6-2.6a2.25 2.25 0 013.18 0l2.6 2.6V5.25a.75.75 0 00-.75-.75H3.25z" clip-rule="evenodd" />
                                </svg>
                                Cover Photos
                            </a>
                            <?php endif; ?>
                        </div>
                
                        <!-- Grupo 2: Acciones de Administración (siempre a la derecha, SOLO ADMINS) -->
                        <?php if ($_SESSION['user_rol'] === 'admin'): ?>
                        <div class="flex items-center flex-wrap gap-3">
                            <span class="text-sm font-medium text-gray-500 mr-2">Admin:</span>
                            <a href="/blogging/manage_users.php" 
                               class="inline-flex items-center gap-x-2 px-4 py-2 bg-gray-700 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-gray-800 transition-colors">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                  <path d="M10 8a3 3 0 100-6 3 3 0 000 6zM3.465 14.493a1.23 1.23 0 00.41 1.412A9.957 9.957 0 0010 18c2.31 0 4.438-.784 6.131-2.095a1.23 1.23 0 00.41-1.412A9.994 9.994 0 0010 12c-2.31 0-4.438.784-6.131 2.095z" />
                                </svg>
                                Manage Users
                            </a>
                            <a href="/blogging/manage_team.php" 
                               class="inline-flex items-center gap-x-2 px-4 py-2 bg-gray-700 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-gray-800 transition-colors">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                  <path d="M7 8a3 3 0 100-6 3 3 0 000 6zM14.5 9a3.5 3.5 0 100-7 3.5 3.5 0 000 7zM1.498 16.42a9.023 9.023 0 0111.453-4.524 3.494 3.494 0 011.1 2.37V18a1 1 0 001 1h.5a1 1 0 001-1v-2.235a3.494 3.494 0 011.1-2.37 9.023 9.023 0 014.35 4.523A1 1 0 0019.5 18h-16a1 1 0 00-.998-1.424z" />
                                </svg>
                                Manage Team
                            </a>
                            <a href="/blogging/manage_categories.php" 
                               class="inline-flex items-center gap-x-2 px-4 py-2 bg-gray-700 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-gray-800 transition-colors">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                   <path fill-rule="evenodd" d="M2.5 3A1.5 1.5 0 001 4.5v2.25a1.5 1.5 0 001.5 1.5h1.25a.75.75 0 010 1.5H2.5A1.5 1.5 0 001 11.25v2.25A1.5 1.5 0 002.5 15h1.25a.75.75 0 010 1.5H2.5A1.5 1.5 0 001 18M18.5 3a.75.75 0 01.75.75v12.5a.75.75 0 01-1.5 0v-12a.75.75 0 01.75-.75zM6.5 3A1.5 1.5 0 018 4.5v12.5a.75.75 0 01-1.5 0V5a.75.75 0 01.75-.75zM2.5 3h12A1.5 1.5 0 0116 4.5v12.5a.75.75 0 01-1.5 0V5a.75.75 0 01-.75-.75h-10a.75.75 0 01-.75.75v12.5a.75.75 0 01-1.5 0V4.5A1.5 1.5 0 012.5 3z" clip-rule="evenodd" />
                                </svg>
                                Categories
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                </div>
                
                <!-- NEW: Second row for Export Buttons, with a top border -->
                <div class="flex flex-col sm:flex-row justify-end items-start sm:items-center gap-4 border-t border-gray-200 pt-4">
                    
                    <span class="text-sm font-medium text-gray-600">Download Data:</span>
                    <!-- Export buttons are now here -->
                    <!-- Grupo de Botones de Exportación -->
                    <div class="flex items-center space-x-2">
                        <!-- Botón para exportar mis artículos -->
                        <a href="/blogging/export_csv.php?type=my_posts" 
                           class="inline-flex items-center gap-x-2 px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-xs font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                           title="Descargar mis artículos como CSV">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.25a.75.75 0 00-1.5 0v4.59L7.3 9.24a.75.75 0 00-1.1 1.02l3.25 3.5a.75.75 0 001.1 0l3.25-3.5a.75.75 0 10-1.1-1.02l-1.95 2.1V6.75z" clip-rule="evenodd" />
                            </svg>
                            Exportar Mis Artículos
                        </a>
    
                        <!-- Botón para exportar TODOS los artículos (SOLO ADMINS) -->
                        <?php if ($_SESSION['user_rol'] === 'admin'): ?>
                        <a href="/blogging/export_csv.php?type=all_posts" 
                           class="inline-flex items-center gap-x-2 px-3 py-2 bg-green-100 border border-green-200 rounded-md shadow-sm text-xs font-medium text-green-800 hover:bg-green-200 transition-colors"
                           title="Download all articles from the database as CSV">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.25a.75.75 0 00-1.5 0v4.59L7.3 9.24a.75.75 0 00-1.1 1.02l3.25 3.5a.75.75 0 001.1 0l3.25-3.5a.75.75 0 10-1.1-1.02l-1.95 2.1V6.75z" clip-rule="evenodd" />
                            </svg>
                            Exportar Todo (Admin)
                        </a>
                        <?php endif; ?>
                    </div>

            </div>
            
            

            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <!-- SOLUCIÓN: Añadimos 'w-full' a la tabla para que ocupe todo el ancho disponible -->
                    <table class="min-w-full w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <!-- COLUMNA EXTRA PARA ADMINS -->
                            
                                <?php if ($user_rol === 'admin'): ?>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Author</th>
                                <?php endif; ?>
                            
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Category</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Views</th>
                                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($articulos)): ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                        You haven't published any articles yet. Create the first one!
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($articulos as $articulo): ?>
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900 truncate">
                                                <a href="/blogging/single-post.php?id=<?php echo $articulo['id']; ?>" 
                                                   class="hover:underline hover:text-indigo-600 transition-colors"
                                                   target="_blank" 
                                                   title="View post: <?php echo htmlspecialchars($articulo['titulo']); ?>">
                                                    <?php echo htmlspecialchars($articulo['titulo']); ?>
                                                </a>
                                                </div>
                                            <div class="text-sm text-gray-500 md:hidden"><?php echo format_date_dashboard($articulo['fecha_publicacion']); ?>
                                            </div>
                                        </td>
                                        
                                    <!-- CELDA EXTRA PARA ADMINS -->
                                  
                                        <?php if ($user_rol === 'admin'): ?>
                                        <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                            <div class="text-sm text-gray-800"><?php echo htmlspecialchars($articulo['autor_nombre'] ?? 'N/A'); ?></div>
                                        </td>
                                        <?php endif; ?>
                                    

                                        
                                        <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"><?php echo htmlspecialchars($articulo['categoria_nombre'] ?? 'Sin categoría'); ?></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
                                            <?php echo number_format($articulo['visitas']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <div class="flex items-center justify-end space-x-2">
                                                <a href="/blogging/edit-post.php?id=<?php echo $articulo['id']; ?>" 
                                                class="p-2 text-indigo-600 bg-indigo-100 rounded-full hover:bg-indigo-200 hover:text-indigo-900 transition-colors"
                                                title="Editar">
                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                                    <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span class="sr-only">Edit</span>
                                                </a>
                                                <button onclick="confirmDelete(event, <?php echo $articulo['id']; ?>)" 
                                                        class="p-2 text-red-600 bg-red-100 rounded-full hover:bg-red-200 hover:text-red-900 transition-colors"
                                                        title="Eliminar">
                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.58.22-2.365.468a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193v-.443A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span class="sr-only">Delete</span>
                                                </button>
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
    </div>
</div>

<!-- El script de JavaScript no necesita cambios -->
<script>
function confirmDelete(event, postId) {
    event.preventDefault();
    if (confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/blogging/delete_post.php'; // Ruta absoluta
        const hiddenField = document.createElement('input');
        hiddenField.type = 'hidden';
        hiddenField.name = 'post_id';
        hiddenField.value = postId;
        form.appendChild(hiddenField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php
// Incluir el footer
require_once __DIR__ . '/../homepage/_footer.php';
?>