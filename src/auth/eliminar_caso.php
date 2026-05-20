<?php
session_start();
require_once '../../configDB.php';

// 1. Control estricto de acceso: Solo abogados autenticados
if (!isset($_SESSION['usuari_id']) || $_SESSION['rol'] !== 'abogado') {
    header('Location: login.php');
    exit;
}

// 2. Comprobar que el ID existe y es un número entero válido
if (isset($_GET['id'])) {
    $id_caso = (int)$_GET['id'];
    $abogado_id = $_SESSION['usuari_id'];

    try {
        // 3. Sentencia preparada con doble filtro (Evita inyección SQL y destrucción ilícita)
        $sql = "DELETE FROM casos WHERE id = ? AND abogado_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_caso, $abogado_id]);

        // 4. Redirección exitosa con un parámetro de mensaje
        header('Location: dashboard.php?status=deleted');
        exit;
        
    } catch (PDOException $e) {
        // Protección de infraestructura: Evitar romper el servidor revelando logs internos
        die("Error de seguretat en la base de dades al eliminar l'expedient.");
    }
} else {
    header('Location: dashboard.php');
    exit;
}
?>
