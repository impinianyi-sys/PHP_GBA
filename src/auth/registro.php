<?php
// registro.php
require_once '../../configDB.php'; 

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recollim dades fent servir els 'name' del teu HTML
    $nom_usuari = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $contrasenya = $_POST['contraseña'] ?? '';
    $confirmar_contrasenya = $_POST['confirmar_contraseña'] ?? '';

    $errors = [];

    // Validacions segons apunts [cite: 1141, 1148]
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

    // Comprovar si l'usuari existeix a la taula 'usuarios' [cite: 1166]
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE nombre = ? OR email = ?");
        $stmt->execute([$nom_usuari, $email]);
        if ($stmt->fetch()) {
            $errors[] = "L'usuari o email ja existeix";
        }
    }

    // Inserció a la base de dades [cite: 1179, 1182]
    if (empty($errors)) {
        // Hash de seguretat [cite: 1176, 2758]
        $hash_contrasenya = password_hash($contrasenya, PASSWORD_DEFAULT);

        // ATENCIÓ: Taula 'usuarios' i columnes 'nombre', 'email', 'contraseña', 'rol'
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, contraseña, rol) VALUES (?, ?, ?, ?)");
        
        if ($stmt->execute([$nom_usuari, $email, $hash_contrasenya, 'usuario'])) {
            $_SESSION['missatge'] = "Registre exitós!";
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
        <input type="text" name="nombre" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Contrasenya:</label><br>
        <input type="password" name="contraseña" required><br><br>

        <label>Confirmar contrasenya:</label><br>
        <input type="password" name="confirmar_contraseña" required><br><br>

        <button type="submit">Registrar-se</button>
    </form>
    
    <p>Ja tens compte? <a href="login.php">Inicia sessió</a></p>
</body>
</html>
