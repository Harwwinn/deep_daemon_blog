<?php
// Incluir el guardia de seguridad para asegurar que el usuario esté logueado
require_once __DIR__ . '/../auth.php';
// Incluir la conexión a la base de datos
require_once __DIR__ . '/../config/conexion.php';

// 1. VERIFICAR QUE LA SOLICITUD SEA POST Y QUE SE HAYA ENVIADO UN 'post_id'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    
    $post_id = (int)$_POST['post_id'];
    $user_id = $_SESSION['user_id']; // ID del usuario logueado

    // 2. VERIFICACIÓN DE SEGURIDAD CRUCIAL:
    // Antes de borrar, comprobamos que el artículo que se quiere borrar
    // realmente pertenece al usuario que está haciendo la solicitud.
    // Esto previene que un usuario malintencionado borre posts de otros
    // adivinando los IDs de los posts.
    
    // NUEVO: Obtenemos el rol del usuario de la sesión.
    // Usamos el operador de fusión de null (??) para evitar errores si la variable no existe.
    $user_role = $_SESSION['user_rol'] ?? 'usuario'; 
    
    $sql_verify = "SELECT autor_id, imagen_destacada FROM articulos WHERE id = ?";
    if ($stmt_verify = $conn->prepare($sql_verify)) {
        $stmt_verify->bind_param("i", $post_id);
        $stmt_verify->execute();
        $result_verify = $stmt_verify->get_result();
        
        if ($result_verify->num_rows === 1) {
            $articulo = $result_verify->fetch_assoc();
            
            // Si el autor_id del artículo coincide con el user_id de la sesión
            if ($articulo['autor_id'] == $user_id || $user_role === 'admin'){
                
                // 3. PROCEDER CON LA ELIMINACIÓN DEL REGISTRO
                $sql_delete = "DELETE FROM articulos WHERE id = ?";
                if ($stmt_delete = $conn->prepare($sql_delete)) {
                    $stmt_delete->bind_param("i", $post_id);
                    if ($stmt_delete->execute()) {
                        
                        // 4. (OPCIONAL PERO RECOMENDADO) BORRAR LA IMAGEN ASOCIADA DEL SERVIDOR
                        $imagen_a_borrar = $articulo['imagen_destacada'];
                        if (!empty($imagen_a_borrar)) {
                            $ruta_imagen = __DIR__ . '/../uploads/imagenes/' . $imagen_a_borrar;
                            if (file_exists($ruta_imagen)) {
                                unlink($ruta_imagen); // unlink() borra el archivo
                            }
                        }
                        
                        // 5. REDIRIGIR DE VUELTA AL DASHBOARD CON UN MENSAJE DE ÉXITO
                        header("Location:" . BASE_URL . "/blogging/dashboard.php?status=deleted_success");
                        exit();
                    }
                }
            }
        }
    }
}

// Si algo falla (el método no es POST, no hay post_id, el usuario no es el autor, etc.),
// simplemente redirigimos al dashboard con un mensaje de error.
header("Location:" . BASE_URL . "/blogging/dashboard.php?status=delete_error");
exit();
?>