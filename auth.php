<?php
// auth.php - Guardia de seguridad y de tiempo de sesión

// Iniciar la sesión si no está ya iniciada, para poder comprobarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---- INICIO DEL BLOQUE DE EXPIRACIÓN DE SESIÓN ----

// 1. Definir el tiempo de inactividad en segundos (ej: 30 minutos)
$inactive_timeout = 1800; // 30 minutos * 60 segundos

// 2. Comprobar si la variable 'last_activity' existe en la sesión
if (isset($_SESSION['last_activity'])) {
    
    // 3. Calcular el tiempo de inactividad
    $session_life = time() - $_SESSION['last_activity'];
    
    // 4. Si el tiempo de inactividad supera nuestro límite
    if ($session_life > $inactive_timeout) {
        
        // Destruir la sesión completamente (podemos usar el código de logout.php)
        session_unset();    // Elimina todas las variables de sesión
        session_destroy();  // Destruye la sesión
        
        // Redirigir al login con un mensaje (opcional)
        header("Location: /login/login.php?reason=session_expired");
        exit;
    }
}

// 5. Si la sesión sigue activa, actualizamos la hora de la última actividad
// Esto "resetea" el contador cada vez que el usuario carga una página.
$_SESSION['last_activity'] = time();

// ---- FIN DEL BLOQUE DE EXPIRACIÓN DE SESIÓN ----


// ---- BLOQUE DE VERIFICACIÓN DE LOGIN (el que ya tenías) ----
// Comprobar si el usuario no está logueado (podría haber sido destruida arriba)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    
    // Si no está logueado, redirigirlo a la página de login
    header("Location: /login/login.php");
    exit;
}
?>