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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bufet GBA</title>
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
            height: 100vh;
            margin: 0;
        }

        .login-card {
            background: var(--white);
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            border-top: 5px solid var(--granate);
        }

        h1 {
            color: var(--granate);
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        input:focus {
            outline: none;
            border-color: var(--granate);
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
        }

        .alert {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            text-align: center;
        }

        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }

        .links {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.9rem;
        }

        .links a {
            color: var(--granate);
            text-decoration: none;
        }

        .links a:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="login-card">
        <h1>Bufet GBA</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['missatge'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['missatge']) ?>
                <?php unset($_SESSION['missatge']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="loginForm">
            <div class="form-group">
                <label>Nom d'usuari:</label>
                <input type="text" name="nom_usuari" id="userInput" required>
            </div>

            <div class="form-group">
                <label>Contrasenya:</label>
                <input type="password" name="contrasenya" required>
            </div>

            <button type="submit" class="btn-submit">ENTRAR</button>
        </form>

        <div class="links">
            <p>No tens compte? <a href="registro.php">Registra't</a></p>
            <p><a href="recuperar_contrasenya.php">He oblidat la contrasenya</a></p>
        </div>
    </div>

    <script>
        window.onload = function() {
            const userInput = document.getElementById('userInput');
            if (userInput.value === "") {
                userInput.value = "cliente";
            }
        };
    </script>

</body>
</html>
