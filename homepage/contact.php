<?php
// contact.php
session_start();

// Cargar PHPMailer
require __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Variable para mensajes de estado
$mensaje = [];

// Procesar el formulario cuando se envíe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recoger y sanear los datos del formulario
    $nombre = strip_tags(trim($_POST["nombre"]));
    $correo = filter_var(trim($_POST["correo"]), FILTER_SANITIZE_EMAIL);
    $asunto = strip_tags(trim($_POST["asunto"]));
    $contenido = trim($_POST["contenido"]);

    // 2. Validar los datos
    if (empty($nombre) || empty($correo) || empty($asunto) || empty($contenido)) {
        $mensaje = ['tipo' => 'error', 'texto' => 'Todos los campos son obligatorios.'];
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = ['tipo' => 'error', 'texto' => 'Por favor, introduce una dirección de correo válida.'];
    } else {
        // 3. Si todo es válido, procedemos a enviar el correo
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor (ajusta con tus credenciales)
            $mail->isSMTP();
            $mail->Host       = 'mail.deepdaemon.org';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'contacto@deepdaemon.org'; // TU CORREO DE GMAIL
            $mail->Password   = 'gmMgdOy+f-jk'; // TU CONTRASEÑA DE APLICACIÓN
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->CharSet    = 'UTF-8';

            // Remitente y destinatarios
            // El 'From' es tu correo, pero el 'Reply-To' es el del usuario que te contacta
            $mail->setFrom('contacto@deepdaemon.org', 'Formulario de Contacto');
            $mail->addAddress('deepdaemon.mx@gmail.com', 'Marco Armendáriz'); // <-- ¡AQUÍ VA TU CORREO!
            $mail->addReplyTo($correo, $nombre);

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Deepdaemon - New contact message: ' . $asunto;
            $mail->Body    = "<h2>You have received a new contact message.</h2>" .
                             "<p><strong>Name:</strong> " . htmlspecialchars($nombre) . "</p>" .
                             "<p><strong>Email:</strong> " . htmlspecialchars($correo) . "</p>" .
                             "<p><strong>Subject:</strong> " . htmlspecialchars($asunto) . "</p>" .
                             "<hr>" .
                             "<p><strong>Message:</strong></p>" .
                             "<div>" . nl2br(htmlspecialchars($contenido)) . "</div>";
            $mail->AltBody = "Name: {$nombre}\nEmail: {$correo}\nSubject: {$asunto}\n\nMessage:\n{$contenido}";

            $mail->send();
            $mensaje = ['tipo' => 'exito', 'texto' => '¡Thanks for your message! We will respond to you shortly..'];
        } catch (Exception $e) {
            $mensaje = ['tipo' => 'error', 'texto' => "The message could not be sent. Mailer error.: {$mail->ErrorInfo}"];
        }
    }
}

// Incluir el header
require_once __DIR__ . '/_header.php';
?>

<!-- ======================================================= -->
<!-- ========= CONTENIDO DE LA PÁGINA DE CONTACTO ======== -->
<!-- ======================================================= -->
<div class="bg-white py-10 sm:py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            
            <!-- Encabezado de la Sección -->
            <div class="text-center">
                <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">
                    Get in Touch
                </h1>
                <p class="mt-4 text-lg text-gray-600">
                    Do you have any questions, suggestions, or just want to say hello? We'd love to hear from you.
                </p>
            </div>

            <!-- Formulario y Mensajes -->
            <div class="mt-12 bg-gray-50 p-8 rounded-lg shadow-sm border border-gray-200">
                <!-- Bloque para mostrar mensajes de estado -->
                <?php if (!empty($mensaje)): ?>
                    <div class="p-4 mb-6 text-sm rounded-md <?php echo ($mensaje['tipo'] == 'exito') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                        <?php echo htmlspecialchars($mensaje['texto']); ?>
                    </div>
                <?php endif; ?>

                <!-- Si el mensaje es de éxito, podemos ocultar el formulario -->
                <?php if (empty($mensaje) || $mensaje['tipo'] !== 'exito'): ?>
                <form action="contact.php" method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700">Your name</label>
                            <div class="mt-1">
                                <input type="text" name="nombre" id="nombre" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                        <div>
                            <label for="correo" class="block text-sm font-medium text-gray-700">Your email</label>
                            <div class="mt-1">
                                <input type="email" name="correo" id="correo" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="asunto" class="block text-sm font-medium text-gray-700">Subject</for>
                        <div class="mt-1">
                            <input type="text" name="asunto" id="asunto" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>
                    <div>
                        <label for="contenido" class="block text-sm font-medium text-gray-700">Message</label>
                        <div class="mt-1">
                            <textarea id="contenido" name="contenido" rows="6" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"></textarea>
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="inline-flex justify-center py-3 px-6 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Send Message
                        </button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir el footer
require_once __DIR__ . '/_footer.php';
?>