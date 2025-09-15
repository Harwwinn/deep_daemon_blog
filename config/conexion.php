<?php
// conexion.php

$host = "localhost";
$usuario = "deepdae1_adminbark";
$contrasena = "p70cG]g.xp{W";
$basedatos = "deepdae1_bark_blogdb";

// Crear conexión
//$conn = mysqli_connect($host, $usuario, $contrasena, $basedatos);
// La ruta al socket de MySQL en XAMPP para macOS
//$socket = '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock';

$conn = new mysqli($host, $usuario, $contrasena, $basedatos, null);

// Verificar conexión
if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Opcional: configurar charset para UTF-8
mysqli_set_charset($conn, "utf8mb4");


define('BASE_URL', ''); // Or just '/' if it's at the root
define('BASE_PATH', __DIR__ . '/..'); // The absolute server path to the project root

?>
