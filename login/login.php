<?php
// -- Bloque de depuración --
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar la sesión para poder acceder a los mensajes
session_start();

// Si el usuario ya está logueado, redirigirlo al dashboard para que no vea el login de nuevo
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: ../blogging/write_blog.php"); // Asegúrate de tener un archivo dashboard.php
    exit;
}

// Incluir la conexión a la base de datos
require_once '../config/conexion.php';

// Variable para el mensaje
$mensaje = '';

if (isset($_GET['reason']) && $_GET['reason'] == 'session_expired') {
    $mensaje = ['tipo' => 'error', 'texto' => 'Your session has expired due to inactivity. Please log in again.'];
}

// Comprobar si existe un mensaje de la sesión (por ejemplo, desde el registro)
if (isset($_SESSION['mensaje'])) {
    // Asignar el mensaje a nuestra variable local
    $mensaje = $_SESSION['mensaje'];
    
    // MUY IMPORTANTE: Eliminar el mensaje de la sesión para que no se muestre de nuevo si se recarga la página
    unset($_SESSION['mensaje']);
}

// --- INICIO DEL PROCESAMIENTO DEL FORMULARIO DE LOGIN ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- BLOQUE DE VERIFICACIÓN DE reCAPTCHA (AHORA CON cURL) ---
    $recaptcha_secret = '6Lf67pwrAAAAAN0-9PdgcCeiQKwV5YKBgfZbUcH0'; // Tu clave secreta
    $recaptcha_response = $_POST['recaptcha_response'] ?? '';

    $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
    $post_data = http_build_query([
        'secret'   => $recaptcha_secret,
        'response' => $recaptcha_response,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ]);

    // Inicializar cURL
    $ch = curl_init();

    // Configurar las opciones de cURL
    curl_setopt($ch, CURLOPT_URL, $verify_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Ejecutar la petición
    $response = curl_exec($ch);

    // Comprobar errores de cURL
    if ($response === false) {
        error_log("cURL Error en reCAPTCHA: " . curl_error($ch));
        $result = new stdClass(); // Crear un objeto vacío para evitar el siguiente error
        $result->success = false;
    } else {
        // Decodificar la respuesta JSON de Google
        $result = json_decode($response);
    }
    
    // Cerrar cURL
    curl_close($ch);
    // --- FIN DEL BLOQUE cURL ---

    // Comprobar el resultado de reCAPTCHA
    if (!$result || !$result->success || $result->score < 0.5) {
        // Si reCAPTCHA falla o la puntuación es muy baja, mostramos un error
        $mensaje = ['tipo' => 'error', 'texto' => 'The security check failed. Please try again.'];
    } else {
        // Si reCAPTCHA es exitoso, procedemos con la lógica de login
        $correo = trim($_POST['correo']);
        $contrasena_form = trim($_POST['contrasena']);

        $sql = "SELECT id, nombre, correo, contrasena, rol, avatar FROM usuarios WHERE correo = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $correo);
            
            if ($stmt->execute()) {
                $stmt->store_result();
                
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $nombre, $correo_db, $contrasena_hash, $rol, $avatar);
                    if ($stmt->fetch()) {
                        if (password_verify($contrasena_form, $contrasena_hash)) {
                            session_regenerate_id(true);
                            $_SESSION['loggedin'] = true;
                            $_SESSION['user_id'] = $id;
                            $_SESSION['user_nombre'] = $nombre;
                            $_SESSION['user_rol'] = $rol;
                            $_SESSION['last_activity'] = time();
                            $_SESSION['user_avatar'] = $avatar;
                            
                            header("Location: ../index.php");
                            exit();
                        } else {
                            $mensaje = ['tipo' => 'error', 'texto' => 'Your credentials are incorrect.'];
                        }
                    }
                } else {
                    $mensaje = ['tipo' => 'error', 'texto' => 'Your credentials are incorrect.'];
                }
            } else {
                $mensaje = ['tipo' => 'error', 'texto' => 'Error executing the query.'];
            }
            $stmt->close();
        } else {
            $mensaje = ['tipo' => 'error', 'texto' => 'Error preparing the query.'];
        }
        // No cerramos la conexión aquí para que el script pueda seguir funcionando si hay un error
        // $conn->close();
    }
}
// --- FIN DEL PROCESAMIENTO DEL FORMULARIO ---
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deepdaemon</title>
    <link rel="icon" type="image/png" href="/my-favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/my-favicon/favicon.svg" />
    <link rel="shortcut icon" href="/my-favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/my-favicon/apple-touch-icon.png" />
    <link rel="manifest" href="/my-favicon/site.webmanifest" />
    <link href="/src/output.css" rel="stylesheet">
</head>
<!-- CAMBIO: Fondo blanco sólido -->
<body class="bg-white">

  <!-- ======================================================= -->
  <!-- ========= INICIO DEL NUEVO DISEÑO "CLARO" ============= -->
  <!-- ======================================================= -->
  <div class="min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        
        <!-- Encabezado con logo y texto -->
        <div>
            <a href="/index.php">
                <img class="mx-auto h-24 w-auto" src="/assets/img/logo_transparente.png" alt="Logo de Deepdaemon">
            </a>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Log in to your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Welcome to our blog
            </p>
        </div>

        <!-- Formulario y Mensajes -->
        <div class="mt-8 bg-gray-50 p-8 rounded-lg shadow-sm border border-gray-200">
            <!-- Bloque para mostrar mensajes -->
            <?php if (!empty($mensaje)): ?>
                <div class="p-4 mb-6 text-sm rounded-md <?php echo ($mensaje['tipo'] == 'exito') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>" role="alert">
                    <?php echo htmlspecialchars($mensaje['texto']); ?>
                </div>
            <?php endif; ?>

            <form id="login-form" class="space-y-6" action="login.php" method="POST" novalidate>
                <!-- Este campo oculto se llenará automáticamente con el token de reCAPTCHA -->
                <input type="hidden" name="recaptcha_response" id="recaptchaResponse">

            
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                    <div class="mt-1">
                        <input id="email" name="correo" type="email" autocomplete="email" required 
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                    </div>
                    <p id="email-error" class="hidden mt-2 text-sm text-red-600 font-medium"></p>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="mt-1">
                        <input id="password" name="contrasena" type="password" autocomplete="current-password" required 
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                    </div>
                    <p id="password-error" class="hidden mt-2 text-sm text-red-600 font-medium"></p>
                </div>

                <div class="flex items-center justify-end">
                    <div class="text-sm">
                        <a href="/login/forgot-password.php" class="font-medium text-primary-600 hover:text-primary-500">
                            Forgot your password?
                        </a>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Log in
                    </button>
                </div>
            </form>
        </div>
    </div>
  </div>

  <!-- Script de reCAPTCHA -->
   <script src="https://www.google.com/recaptcha/api.js?render=6Lf67pwrAAAAAOcAjwwThOr-GON346BOXZPe5yt8"></script>

  <!-- CAMBIO: Lógica de validación completamente renovada para una mejor UX -->
  <script>
    // Obtener el token de reCAPTCHA
    grecaptcha.ready(function () {
        grecaptcha.execute('6Lf67pwrAAAAAOcAjwwThOr-GON346BOXZPe5yt8', { action: 'login' }).then(function (token) {
            var recaptchaResponse = document.getElementById('recaptchaResponse');
            recaptchaResponse.value = token;
        });
    });


    // Validación del formulario
    const form = document.getElementById('login-form');
    const inputs = Array.from(form.querySelectorAll('input[required]'));

    // NUEVO: Mensajes de error mucho más descriptivos
    const errorMessages = {
      correo: { // <-- Cambiado de 'email' a 'correo'
        valueMissing: 'The email field is required.',
        typeMismatch: 'Por favor, introduce un formato de correo válido (ej: tu@correo.com).'
      },
      contrasena: { // <-- Cambiado de 'password' a 'contrasena'
        valueMissing: 'The password field is required.'
      }
    };

    // NUEVO: Función para mostrar el error y aplicar el estilo
    const showError = (input, message) => {
      const errorElement = document.getElementById(input.id + '-error');
      input.classList.add('input-error'); // Aplica nuestra clase de error
      errorElement.textContent = message;
      errorElement.classList.remove('hidden');
    };

    // NUEVO: Función para ocultar el error y quitar el estilo
    const hideError = (input) => {
      const errorElement = document.getElementById(input.id + '-error');
      input.classList.remove('input-error'); // Quita nuestra clase de error
      errorElement.classList.add('hidden');
    };

    // NUEVO: Función central de validación
    const validateInput = (input) => {
      const validity = input.validity;
      const inputErrors = errorMessages[input.name]; // `name` debe coincidir con las keys del objeto errorMessages

      if (validity.valueMissing) {
        showError(input, inputErrors.valueMissing);
        return false;
      }
      if (validity.typeMismatch) {
        showError(input, inputErrors.typeMismatch);
        return false;
      }
      
      // Si no hay errores, nos aseguramos de que esté limpio
      hideError(input);
      return true;
    };

    // Validamos el formulario al intentar enviarlo
    form.addEventListener('submit', (event) => {
      event.preventDefault(); // Detenemos el envío siempre
      let isFormValid = true;
      
      // Validamos todos los campos uno por uno
      inputs.forEach(input => {
        if (!validateInput(input)) {
          isFormValid = false;
        }
      });

      if (isFormValid) {
        console.log('Formulario válido, enviando al servidor...');
        form.submit(); // Descomenta esta línea para enviar el formulario real
      } else {
        console.log('El formulario contiene errores.');
      }
    });

    // NUEVO: Validación en tiempo real para una mejor experiencia
    inputs.forEach(input => {
      // Valida cuando el usuario sale del campo
      input.addEventListener('blur', () => {
        validateInput(input);
      });

      // Limpia el error mientras el usuario escribe, si el campo se vuelve válido
      input.addEventListener('input', () => {
        if (input.validity.valid) {
          hideError(input);
        }
      });
    });
  </script>

</body>
</html>