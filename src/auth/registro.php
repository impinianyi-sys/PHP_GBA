<?php

require_once '../../configDB.php'; 

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_usuari = $_POST['nom_usuari'] ?? '';
    $email = $_POST['email'] ?? '';
    $contrasenya = $_POST['contrasenya'] ?? '';
    $confirmar_contrasenya = $_POST['confirmar_contrasenya'] ?? '';

    $errors = [];


    if (strlen($nom_usuari) < 3) {
        $errors[] = "El nom d'usuari ha de tenir mínim 3 caràcters";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email no vàlid";
    }

    if (strlen($contrasenya) < 8) {
        $errors[] = "La contrasenya ha de tenir mínim 8 caràcters";
    }

    if ($contrasenya !== $confirmar_contrasenya) {
        $errors[] = "Les contrasenyes no coincideixen";
    }


    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM usuaris WHERE nom_usuari = ? OR email = ?");
        $stmt->execute([$nom_usuari, $email]);
        if ($stmt->fetch()) {
            $errors[] = "L'usuari o email ja existeix";
        }
    }


    if (empty($errors)) {

        $hash_contrasenya = password_hash($contrasenya, PASSWORD_DEFAULT);


        $stmt = $pdo->prepare("INSERT INTO usuaris (nom_usuari, email, contrasenya) VALUES (?, ?, ?)");
        
        if ($stmt->execute([$nom_usuari, $email, $hash_contrasenya])) {
            $_SESSION['missatge'] = "Registre exitós! Ja pots iniciar sessió.";
            header('Location: login.php');
            exit;
        } else {
            $errors[] = "Error en crear l'usuari";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Registre - Bufet d'Advocats</title>
</head>
<body>
    <h1>Crear compte de personal</h1>

    <?php if (!empty($errors)): ?>
        <div style="color: red;">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <label>Nom d'usuari:</label><br>
        <input type="text" name="nom_usuari" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Contrasenya:</label><br>
        <input type="password" name="contrasenya" required><br><br>

        <label>Confirmar contrasenya:</label><br>
        <input type="password" name="confirmar_contrasenya" required><br><br>

        <button type="submit">Registrar-se</button>
    </form>
    
    <p>Ja tens compte? <a href="login.php">Inicia sessió</a></p>
</body>
</html>
