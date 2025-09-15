<?php
// -- Bloque de depuración --
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Es fundamental iniciar la sesión para poder destruirla.
session_start();

// 1. Vaciar todas las variables de la sesión.
// Esto asegura que todos los datos guardados (como user_id, user_nombre) se eliminen.
$_SESSION = array();

// 2. Destruir la cookie de sesión del navegador.
// Esto es un paso de seguridad adicional. Si la sesión usa cookies,
// se le dice al navegador que la cookie expire en el pasado, eliminándola efectivamente.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Finalmente, destruir la sesión del servidor.
// Este comando elimina el archivo de sesión del servidor.
session_destroy();

// 4. Redirigir al usuario a la página de login.
// Después de cerrar sesión, el usuario no debe permanecer en una página protegida.
header("Location: /login/login.php");

// 5. Terminar la ejecución del script.
// Es una buena práctica llamar a exit() después de una redirección para asegurar
// que no se ejecute ningún otro código.
exit();
?>