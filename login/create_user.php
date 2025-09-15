<?php
// -- Bloque de depuración --
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar una sesión para poder guardar mensajes
session_start();

// Guardia de seguridad: solo administradores pueden acceder.
require_once __DIR__ . '/../admin_auth.php';

// Variable para almacenar el mensaje de error o éxito
$mensaje = '';

// 1. Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. Incluir el archivo de conexión
    require_once '../config/conexion.php';

    // 3. Recoger y limpiar los datos del formulario
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);
    $rol = $_POST['rol']; // Asignamos un rol por defecto

    // Validación básica en el backend
    if (empty($nombre) || empty($correo) || empty($contrasena) || empty($rol)) {
        $mensaje = ['tipo' => 'error', 'texto' => 'Todos los campos son obligatorios.'];
    } elseif (!in_array($rol, ['usuario', 'admin'])) {
        $mensaje = ['tipo' => 'error', 'texto' => 'El rol seleccionado no es válido.'];
    } else {
      // 4. Seguridad: Hashear la contraseña
      // ¡NUNCA guardes contraseñas en texto plano!
      $contrasena_hash = password_hash($contrasena, PASSWORD_BCRYPT);

      // 5. Preparar la consulta SQL para prevenir inyecciones SQL
      // La columna 'id' no se incluye porque es AUTO_INCREMENT
      $sql = "INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES (?, ?, ?, ?)";
      
      $stmt = $conn->prepare($sql);

      if ($stmt) {
          // "ssss" significa que los 4 parámetros son strings (cadenas de texto)
          $stmt->bind_param("ssss", $nombre, $correo, $contrasena_hash, $rol);
          
          // 6. Ejecutar la consulta y verificar si fue exitosa
          try {
              // Intentamos ejecutar la consulta
              if ($stmt->execute()) {
                  // Si tiene éxito, guardamos mensaje y redirigimos
                   $mensaje = ['tipo' => 'exito', 'texto' => 'Usuario "' . htmlspecialchars($nombre) . '" creado con éxito.'];
                   $_POST = array(); // Limpiamos el formulario
              }
          } catch (mysqli_sql_exception $e) {
              // Si se lanza una excepción (como la de duplicado), la atrapamos aquí
              
              // Verificamos si el código de error de la excepción es 1062 (duplicado)
              if ($e->getCode() == 1062) {
                  $mensaje = ['tipo' => 'error', 'texto' => 'El correo electrónico ya está registrado. Por favor, utiliza otro.'];
              } else {
                  // Para cualquier otro error de base de datos
                  $mensaje = ['tipo' => 'error', 'texto' => 'Error de base de datos: ' . $e->getMessage()];
                  // En un entorno de producción, podrías querer registrar el error real ($e->getMessage()) en un archivo de log
                  // y mostrar un mensaje más genérico al usuario.
              }
          }
          $stmt->close();
      } else {
          $mensaje = ['tipo' => 'error', 'texto' => 'Error al preparar la consulta.'];
      }
    }
    $conn->close();
}
// Incluir el header
require_once __DIR__ . '/../homepage/_header.php';
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | Mi Plataforma</title>
    <!-- Usamos la misma ruta absoluta al CSS -->
    <link href="/blog_educativo_local/src/output.css" rel="stylesheet">
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
            <a href="/blog_educativo_local/index.php">
                <img class="mx-auto h-24 w-auto" src="/blog_educativo_local/assets/img/logo_transparente.png" alt="Logo de Deepdaemon">
            </a>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Registra un nuevo usuario
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Estás creando una cuenta como Administrador.
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

            <form id="register-form" class="space-y-6" action="create_user.php" method="POST" novalidate>
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre completo</label>
                    <div class="mt-1">
                        <input id="nombre" name="nombre" type="text" autocomplete="name" value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>"required 
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                    </div>
                    <p id="nombre-error" class="hidden mt-2 text-sm text-red-600 font-medium"></p>
                </div>

                <div>
                    <label for="correo" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                    <div class="mt-1">
                        <input id="correo" name="correo" type="email" value="<?php echo htmlspecialchars($_POST['correo'] ?? ''); ?>" autocomplete="email" required 
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                    </div>
                    <p id="correo-error" class="hidden mt-2 text-sm text-red-600 font-medium"></p>
                </div>

                <div>
                    <label for="contrasena" class="block text-sm font-medium text-gray-700">Contraseña</label>
                    <div class="mt-1">
                        <input id="contrasena" name="contrasena" type="password" autocomplete="new-password" required minlength="8"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                    </div>
                    <p id="contrasena-error" class="hidden mt-2 text-sm text-red-600 font-medium"></p>
                </div>

                <div>
                    <label for="password_confirm" class="block text-sm font-medium text-gray-700">Confirmar contraseña</label>
                    <div class="mt-1">
                        <input id="password_confirm" name="password_confirm" type="password" autocomplete="new-password" required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                    </div>
                    <p id="password_confirm-error" class="hidden mt-2 text-sm text-red-600 font-medium"></p>
                </div>

                <!-- CAMBIO: Se añade el selector de rol -->
                <div>
                    <label for="rol" class="block text-sm font-medium text-gray-700">Tipo de Usuario (Rol)</label>
                    <div class="mt-1">
                        <select id="rol" name="rol" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            <option value="usuario" <?php echo (($_POST['rol'] ?? 'usuario') === 'usuario') ? 'selected' : ''; ?>>Usuario</option>
                            <option value="admin" <?php echo (($_POST['rol'] ?? '') === 'admin') ? 'selected' : ''; ?>>Administrador</option>
                        </select>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Crear usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
  </div>

  <script>
    // La lógica de validación es similar a la del login, pero adaptada para los nuevos campos.
    const form = document.getElementById('register-form');
    const inputs = Array.from(form.querySelectorAll('input[required]'));
    
    // Capturamos los campos de contraseña específicamente para la validación de coincidencia.
    const passwordInput = document.getElementById('contrasena');
    const passwordConfirmInput = document.getElementById('password_confirm');

    const errorMessages = {
      nombre: {
        valueMissing: 'El campo de nombre es obligatorio.'
      },
      correo: {
        valueMissing: 'El campo de correo electrónico es obligatorio.',
        typeMismatch: 'Por favor, introduce un formato de correo válido.'
      },
      contrasena: {
        valueMissing: 'El campo de contraseña es obligatorio.',
        tooShort: 'La contraseña debe tener al menos 8 caracteres.'
      },
      password_confirm: {
        valueMissing: 'Por favor, confirma tu contraseña.',
        customError: 'Las contraseñas no coinciden.' // Mensaje para nuestro error personalizado
      }
    };

    const showError = (input, message) => {
      const errorElement = document.getElementById(input.id + '-error');
      input.classList.add('input-error');
      errorElement.textContent = message;
      errorElement.classList.remove('hidden');
    };

    const hideError = (input) => {
      const errorElement = document.getElementById(input.id + '-error');
      input.classList.remove('input-error');
      errorElement.classList.add('hidden');
    };

    const validateInput = (input) => {
      // Usamos la API de validación del navegador.
      const validity = input.validity;
      // Limpiamos errores personalizados anteriores antes de re-validar.
      input.setCustomValidity('');

      // VALIDACIÓN ESPECIAL: Coincidencia de contraseñas
      if (input.id === 'password_confirm' && passwordInput.value !== passwordConfirmInput.value) {
        // Si no coinciden, establecemos un error personalizado.
        input.setCustomValidity(errorMessages.password_confirm.customError);
      }
      
      const inputErrors = errorMessages[input.name];
      
      // Comprobamos los errores en orden de prioridad.
      if (validity.valueMissing) { showError(input, inputErrors.valueMissing); return false; }
      if (validity.typeMismatch) { showError(input, inputErrors.typeMismatch); return false; }
      if (validity.tooShort) { showError(input, inputErrors.tooShort); return false; }
      if (validity.customError) { showError(input, inputErrors.customError); return false; }
      
      hideError(input);
      return true;
    };

    form.addEventListener('submit', (event) => {
      event.preventDefault();
      let isFormValid = true;
      inputs.forEach(input => {
        if (!validateInput(input)) {
          isFormValid = false;
        }
      });
      if (isFormValid) {
        console.log('Formulario de registro válido, listo para enviar.');
        form.submit(); // Descomenta para enviar el formulario.
      }
    });

    inputs.forEach(input => {
      input.addEventListener('blur', () => validateInput(input));
      
      input.addEventListener('input', () => {
        // Limpia el error mientras el usuario escribe si el campo se vuelve válido.
        if(input.id === 'password_confirm' || input.id === 'contrasena') {
          // Si se está editando una de las contraseñas, re-validamos la de confirmación
          // para dar feedback instantáneo sobre la coincidencia.
          validateInput(passwordConfirmInput);
        } else {
           if (input.validity.valid) hideError(input);
        }
      });
    });
  </script>
</body>
</html>

<?php
require_once __DIR__ . '/../homepage/_footer.php';
?>