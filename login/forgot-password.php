<?php
// -- Bloque de depuración --
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// forgot-password.php
session_start();

// --- INCLUIR AUTOLOAD DE COMPOSER Y LIBRERÍAS DE PHPMailer ---
require __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// La lógica de PHP que ya tienes no cambia, la incluyo aquí para que el archivo esté completo.
require_once __DIR__ . '/../config/conexion.php';
$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['correo']);

    $sql_find_user = "SELECT id, nombre FROM usuarios WHERE correo = ?";
    if ($stmt_find = $conn->prepare($sql_find_user)) {
        $stmt_find->bind_param("s", $correo);
        $stmt_find->execute();
        $result = $stmt_find->get_result();

        // Por seguridad, siempre mostramos un mensaje genérico para no revelar si un correo existe o no.
        $mensaje = ['tipo' => 'exito', 'texto' => 'If your email address is in our system, you will receive a link to reset your password.'];

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            $token = bin2hex(random_bytes(32));
            $expires_at = new DateTime();
            $expires_at->add(new DateInterval('PT1H'));
            $expires_at_str = $expires_at->format('Y-m-d H:i:s');
            
            $sql_update_token = "UPDATE usuarios SET reset_token = ?, reset_token_expires_at = ? WHERE id = ?";
            if ($stmt_update = $conn->prepare($sql_update_token)) {
                $stmt_update->bind_param("ssi", $token, $expires_at_str, $user['id']);
                $stmt_update->execute();
                
                // --- INICIO DEL NUEVO BLOQUE DE ENVÍO CON PHPMailer ---
                $mail = new PHPMailer(true);
                try {
                    // Configuración del servidor (usando Gmail como ejemplo)
                    $mail->isSMTP();
                    $mail->Host       = 'mail.deepdaemon.org';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'contacto@deepdaemon.org'; // TU CORREO DE GMAIL
                    $mail->Password   = 'gmMgdOy+f-jk'; // LA CONTRASEÑA DE APLICACIÓN DE 16 DÍGITOS
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port       = 465;
                    $mail->CharSet = 'UTF-8';

                    // Remitente y destinatarios
                    $mail->setFrom('contacto@deepdaemon.org', 'Deepdaemon');
                    $mail->addAddress($correo, $user['nombre']); // Añade el destinatario

                    // Contenido del correo
                    $mail->isHTML(true); // Permite enviar HTML en el correo
                    $reset_link = "https://deepdaemon.org/login/reset-password.php?token=" . $token;
                    $mail->Subject = 'Password reset';
                    $mail->Body    = 'Hi ' . htmlspecialchars($user['nombre'], ENT_QUOTES, 'UTF-8') . ',<br><br>' .
                                     'We have received a request to reset your password. Click the following link to continue:<br>' .
                                     '<a href="' . $reset_link . '">Reset my password</a><br><br>' .
                                     'If you did not request this, you can ignore this email.<br>' .
                                     'The link will expire in 1 hour.<br><br>' .
                                     'Regards,<br>';
                    $mail->AltBody = 'To reset your password, copy and paste this link into your browser: ' . $reset_link;

                    $mail->send();
                    // El mensaje de éxito ya está definido fuera de este bloque.
                } catch (Exception $e) {
                    $mensaje = ['tipo' => 'error', 'texto' => "The email could not be sent. Mailer error: {$mail->ErrorInfo}"];
                    error_log("Failed to send reset email to:" . $correo . " - Error: " . $mail->ErrorInfo);
                }
                // --- FIN DEL NUEVO BLOQUE DE ENVÍO ---
            }
        }
    }
}
// Incluir el header
require_once __DIR__ . '/../homepage/_header.php';
?>

<div class="bg-white">
    <!-- Contenido principal centrado -->
    <div class="container mx-auto flex flex-col items-center justify-center py-10 sm:py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            
            <!-- Encabezado con logo y texto -->
            <div>
                <!-- Puedes volver a poner tu logo aquí si lo tienes en formato SVG o imagen -->
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Recover your access
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Or <a href="/login/login.php" class="font-medium text-primary-600 hover:text-primary-500">go back to log in</a>
                </p>
            </div>

            <!-- Formulario y Mensajes -->
            <div class="mt-8 bg-gray-50 p-8 rounded-lg shadow-sm border border-gray-200">
                <!-- Bloque para mostrar mensajes -->
                <?php if (!empty($mensaje)): ?>
                    <div class="p-4 mb-6 text-sm rounded-md <?php echo ($mensaje['tipo'] == 'exito') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                        <?php echo htmlspecialchars($mensaje['texto']); ?>
                    </div>
                <?php endif; ?>

                <form class="space-y-6" action="forgot-password.php" method="POST">
                    <div>
                        <label for="correo" class="block text-sm font-medium text-gray-700">Email</label>
                        <div class="mt-1">
                            <input id="correo" name="correo" type="email" autocomplete="email" required 
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Send recovery link
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php 
// Incluir el footer
require_once __DIR__ . '/../homepage/_footer.php';
?>