<?php
require_once '../../configDB.php';
session_start();

if (isset($_SESSION['usuari_id'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_usuari = $_POST['nom_usuari'] ?? '';
    $contrasenya = $_POST['contrasenya'] ?? '';

    if (!empty($nom_usuari) && !empty($contrasenya)) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nombre = ?");
        $stmt->execute([$nom_usuari]);
        $usuari = $stmt->fetch();

        if ($usuari && password_verify($contrasenya, $usuari['contraseña'])) {
            session_regenerate_id(true);

            $_SESSION['usuari_id'] = $usuari['id'];
            $_SESSION['nom_usuari'] = $usuari['nombre'];
            $_SESSION['rol'] = $usuari['rol'];

            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Usuari o contrasenya incorrectes";
        }
    } else {
        $error = "Emplena tots els camps";
    }
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sessió</title>
</head>
<body>
    <h1>Iniciar sessió - Bufet</h1>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if (isset($_SESSION['missatge'])): ?>
        <p style="color: green;"><?= htmlspecialchars($_SESSION['missatge']) ?></p>
        <?php unset($_SESSION['missatge']); ?>
    <?php endif; ?>
   
    <form method="POST">
        <label>Nom d'usuari:</label><br>
        <input type="text" name="nom_usuari" required><br><br>

        <label>Contrasenya:</label><br>
        <input type="password" name="contrasenya" required><br><br>

        <button type="submit">Entrar</button>
    </form>

    <p>No tens compte? <a href="registro.php">Registra't</a></p>
</body>
</html>
