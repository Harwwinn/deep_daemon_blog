<?php
require_once __DIR__ . '/config/conexion.php';
// Primero, usamos el guardia normal para asegurar que el usuario esté logueado.
require_once __DIR__ . '/auth.php';

// Ahora, añadimos una verificación extra: ¿es administrador?
if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'admin') {
    // Si no es admin, lo redirigimos a su dashboard normal con un mensaje de error.
    header("Location:" . BASE_URL . "/blogging/dashboard.php?error=unauthorized");
    exit;
}
?>