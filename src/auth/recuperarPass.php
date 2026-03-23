<?php
require_once '../../configDB.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $pdo->prepare("SELECT id, nombre FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuari = $stmt->fetch();
        
        if ($usuari) {
            $token = bin2hex(random_bytes(32));
            $expiracio = date('Y-m-d H:i:s', strtotime('+1 hour'));       

            $success = "S'ha enviat un email amb instruccions (Simulat: Token actiu)";
        } else {
            $error = "No existeix cap usuari amb aquest email";
        }
    } else {
        $error = "Email no vàlid";
    }
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Recuperar contrasenya</title>
</head>
<body>
    <h1>Recuperar contrasenya</h1>
    
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <p style="color: green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>
        <button type="submit">Enviar instruccions</button>
    </form>
    
    <p><a href="login.php">Torna a iniciar sessió</a></p>
</body>
</html>
