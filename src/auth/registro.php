<?php
require_once '../../configDB.php'; 

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_usuari = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $contrasenya = $_POST['contraseña'] ?? '';
    $confirmar_contrasenya = $_POST['confirmar_contraseña'] ?? '';

    $errors = [];

    if (strlen($nom_usuari) < 3) $errors[] = "El nom ha de tenir mínim 3 caràcters";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email no vàlid";
    if (strlen($contrasenya) < 8) $errors[] = "La contrasenya ha de tenir mínim 8 caràcters";
    if ($contrasenya !== $confirmar_contrasenya) $errors[] = "Les contrasenyes no coincideixen";

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE nombre = ? OR email = ?");
        $stmt->execute([$nom_usuari, $email]);
        if ($stmt->fetch()) {
            $errors[] = "L'usuari o email ja existeix";
        }
    }

    if (empty($errors)) {
        $hash_contrasenya = password_hash($contrasenya, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, contraseña, rol) VALUES (?, ?, ?, ?)");
        
        if ($stmt->execute([$nom_usuari, $email, $hash_contrasenya, 'cliente'])) {
            session_unset();
            session_destroy();
            session_start();

            $_SESSION['missatge'] = "Compte creat amb èxit!";
            header('Location: login.php');
            exit;
        } else {
            $errors[] = "Error en crear el compte";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registre - Bufet GBA</title>
    <style>
        :root {
            --granate: #800020;
            --granate-light: #a52a2a;
            --white: #ffffff;
            --gray-light: #f4f4f4;
            --text-dark: #333;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, var(--gray-light) 0%, #e0e0e0 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px 0;
        }

        .register-card {
            background: var(--white);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
            border-top: 5px solid var(--granate);
        }

        h1 {
            color: var(--granate);
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.6rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.4rem;
            color: var(--text-dark);
            font-weight: 600;
            font-size: 0.9rem;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            transition: all 0.3s;
        }

        input:focus {
            outline: none;
            border-color: var(--granate);
            box-shadow: 0 0 5px rgba(128, 0, 32, 0.2);
        }

        .btn-submit {
            width: 100%;
            background-color: var(--granate);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 1rem;
        }

        .btn-submit:hover {
            background-color: var(--granate-light);
            transform: translateY(-1px);
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-size: 0.85rem;
            border: 1px solid #f5c6cb;
        }

        .links {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.9rem;
        }

        .links a {
            color: var(--granate);
            text-decoration: none;
            font-weight: bold;
        }

        .links a:hover { text-decoration: underline; }

        .password-strength {
            height: 5px;
            width: 100%;
            background: #eee;
            margin-top: 5px;
            border-radius: 3px;
            overflow: hidden;
        }

        #strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s, background 0.3s;
        }
    </style>
</head>
<body>

    <div class="register-card">
        <h1>Crear compte GBA</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert-error">
                <?php foreach ($errors as $error): ?>
                    <div style="margin-bottom: 5px;">• <?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="registerForm">
            <div class="form-group">
                <label>Nom complet:</label>
                <input type="text" name="nombre" placeholder="Ex: Joan Garcia" required>
            </div>

            <div class="form-group">
                <label>Email personal:</label>
                <input type="email" name="email" placeholder="correu@exemple.com" required>
            </div>

            <div class="form-group">
                <label>Contrasenya:</label>
                <input type="password" name="contraseña" id="passInput" required>
                <div class="password-strength"><div id="strength-bar"></div></div>
            </div>

            <div class="form-group">
                <label>Confirmar contrasenya:</label>
                <input type="password" name="confirmar_contraseña" required>
            </div>

            <button type="submit" class="btn-submit">REGISTRAR-SE</button>
        </form>

        <div class="links">
            <p>Ja tens compte? <a href="login.php">Inicia sessió</a></p>
        </div>
    </div>

    <script>
        const passInput = document.getElementById('passInput');
        const strengthBar = document.getElementById('strength-bar');

        passInput.addEventListener('input', () => {
            const val = passInput.value;
            let width = 0;
            let color = '#eee';

            if (val.length > 0) width = 25;
            if (val.length >= 8) {
                width = 100;
                color = '#27ae60'; // Verd si compleix el mínim
            } else if (val.length > 4) {
                width = 50;
                color = '#f1c40f'; // Groc si és curta
            } else {
                color = '#e74c3c'; // Vermell si és molt curta
            }

            strengthBar.style.width = width + '%';
            strengthBar.style.background = color;
        });
    </script>

</body>
</html>
