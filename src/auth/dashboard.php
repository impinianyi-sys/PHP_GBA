<?php
session_start();

if (!isset($_SESSION['usuari_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control - Bufet GBA</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        .nav { background: #eee; padding: 10px; margin-bottom: 20px; }
        .btn-logout { color: red; font-weight: bold; }
    </style>
</head>
<body>

    <div class="nav">
        <span>Hola, <strong><?php echo htmlspecialchars($_SESSION['nom_usuari']); ?></strong></span> | 
        <span>Rol: <?php echo htmlspecialchars($_SESSION['rol']); ?></span> |
        <a href="logout.php" class="btn-logout">Tancar sessió</a>
    </div>

    <h1>Gestió del Bufet d'Advocats</h1>
    <p>Benvingut al sistema intern. Des d'aquí podràs gestionar els teus casos i clients.</p>

</body>
</html>
