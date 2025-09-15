<?php
// Guardia de seguridad: solo administradores pueden ejecutar este script.
require_once __DIR__ . '/../admin_auth.php';
// Incluir conexión a la BD
require_once __DIR__ . '/../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    
    $user_id_to_delete = (int)$_POST['user_id'];
    $admin_id = $_SESSION['user_id'];

    // 1. PRIMERA BARRERA DE SEGURIDAD: Prevenir que un admin se borre a sí mismo.
    if ($user_id_to_delete === $admin_id) {
        header("Location: manage_users.php?status=self_delete_error");
        exit();
    }

    // 2. SEGUNDA BARRERA DE SEGURIDAD: Prevenir borrar un autor con artículos.
    $sql_check_articles = "SELECT COUNT(*) as article_count FROM articulos WHERE autor_id = ?";
    if ($stmt_check = $conn->prepare($sql_check_articles)) {
        $stmt_check->bind_param("i", $user_id_to_delete);
        $stmt_check->execute();
        $result = $stmt_check->get_result()->fetch_assoc();

        if ($result['article_count'] > 0) {
            // Si el usuario tiene artículos, no lo borramos.
            header("Location: manage_users.php?status=has_articles_error");
            exit();
        }
    }
    
    // 3. SI TODAS LAS BARRERAS PASAN, PROCEDER A BORRAR
    $sql_delete = "DELETE FROM usuarios WHERE id = ?";
    if ($stmt_delete = $conn->prepare($sql_delete)) {
        $stmt_delete->bind_param("i", $user_id_to_delete);
        if ($stmt_delete->execute()) {
            // Éxito
            header("Location: manage_users.php?status=deleted_success");
            exit();
        }
    }
}

// Si algo falla, redirigir con un error genérico
header("Location: manage_users.php?status=delete_error");
exit();
?>