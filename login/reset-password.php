<?php
// -- Bloque de depuración --
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// reset-password.php
session_start();
// --- Lógica PHP para validar el token y procesar el formulario (sin cambios) ---
require_once __DIR__ . '/../config/conexion.php';
$token = $_GET['token'] ?? '';
$mensaje = '';
$token_valido = false;
$user_id = null;

if (!empty($token)) {
    $sql_find_token = "SELECT id FROM usuarios WHERE reset_token = ? AND reset_token_expires_at > NOW()";
    if ($stmt_find = $conn->prepare($sql_find_token)) {
        $stmt_find->bind_param("s", $token);
        $stmt_find->execute();
        $result = $stmt_find->get_result();
        if ($result->num_rows === 1) {
            $token_valido = true;
            $user = $result->fetch_assoc();
            $user_id = $user['id'];
        } else {
            $mensaje = ['tipo' => 'error', 'texto' => 'The password reset link is invalid or has expired. Please request a new one.'];
        }
    }
} else {
    $mensaje = ['tipo' => 'error', 'texto' => 'A reset token was not provided.'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $token_valido) {
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass === $confirm_pass) {
        if (strlen($new_pass) >= 8) {
            $new_hash = password_hash($new_pass, PASSWORD_BCRYPT);
            $sql_reset = "UPDATE usuarios SET contrasena = ?, reset_token = NULL, reset_token_expires_at = NULL WHERE id = ?";
            if ($stmt_reset = $conn->prepare($sql_reset)) {
                $stmt_reset->bind_param("si", $new_hash, $user_id);
                if ($stmt_reset->execute()) {
                    $_SESSION['mensaje'] = ['tipo' => 'exito', 'texto' => 'Your password has been updated! You can now log in.'];
                    header("Location: login.php");
                    exit();
                }
            }
        } else {
             $mensaje = ['tipo' => 'error', 'texto' => 'The new password must be at least 8 characters long.'];
        }
    } else {
        $mensaje = ['tipo' => 'error', 'texto' => 'The passwords do not match.'];
    }
}

// Incluir el header después de la lógica
require_once __DIR__ . '/../homepage/_header.php';
?>

<!-- ================================================================= -->
<!-- ========= INICIO DEL NUEVO DISEÑO "CLARO" ======================= -->
<!-- ================================================================= -->

<div class="bg-white min-h-screen flex flex-col">
    <!-- Contenido principal centrado -->
    <div class="flex-grow flex justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            
            <!-- Encabezado -->
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Set your new password
                </h2>
            </div>

            <!-- Formulario y Mensajes -->
            <div class="mt-8 bg-gray-50 p-8 rounded-lg shadow-sm border border-gray-200">
                
                <!-- Mensaje de error (si el token es inválido) o de validación del formulario -->
                <?php if (!empty($mensaje)): ?>
                    <div class="p-4 mb-6 text-sm rounded-md bg-red-100 text-red-800">
                        <?php echo htmlspecialchars($mensaje['texto']); ?>
                    </div>
                <?php endif; ?>

                <!-- El formulario solo se muestra si el token es válido -->
                <?php if ($token_valido): ?>
                <form class="space-y-6" action="reset-password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700">New password</label>
                        <div class="mt-1">
                            <input id="new_password" name="new_password" type="password" required minlength="8"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm new password</label>
                        <div class="mt-1">
                            <input id="confirm_password" name="confirm_password" type="password" required
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Update password
                        </button>
                    </div>
                </form>
                <?php else: ?>
                    <!-- Mensaje alternativo si el token ya no es válido, con un enlace para volver a empezar -->
                    <div class="text-center">
                        <a href="/blog_educativo_local/login/forgot-password.php" class="font-medium text-primary-600 hover:text-primary-500">
                            Get a new link
                        </a>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<!-- ================================================================= -->
<!-- ========= FIN DEL NUEVO DISEÑO "CLARO" ========================== -->
<!-- ================================================================= -->

<?php 
// Incluir el footer
require_once __DIR__ . '/../homepage/_footer.php';
?>