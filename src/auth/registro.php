<?php
require_once '../../configDB.php';
session_start();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_complet = $_POST['nom_complet'] ?? '';
    $email = $_POST['email'] ?? '';
    $pass = $_POST['contrasenya'] ?? '';
    $pass_confirm = $_POST['confirmar_contrasenya'] ?? '';

    if ($pass !== $pass_confirm) {
        $error = "Les contrasenyes no coincideixen.";
    } else {
        $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? OR nombre = ?");
            $stmt->execute([$email, $nom_complet]);
            
            if ($stmt->fetch()) {
                $error = "Aquest correu o usuari ja està registrat.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, contraseña, rol) VALUES (?, ?, ?, 'cliente')");
                $stmt->execute([$nom_complet, $email, $pass_hash]);
                header('Location: login.php?registro=success');
                exit;
            }
        } catch (PDOException $e) {
            $error = "Error en el sistema. Intenta-ho més tard.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GBA ADVOS | Registre Premium</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --azul-porsche: #004a99;
            --negro-puro: #000000;
            --blanco: #ffffff;
            --rojo: #ff4d4d;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--negro-puro);
            height: 100vh;
            display: flex; justify-content: center; align-items: center;
            overflow: hidden; position: relative;
        }

        .background-image {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background-image: url('https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=2069&auto=format&fit=crop');
            background-size: cover; background-position: center;
            filter: brightness(0.2); z-index: -1;
        }

        .register-card {
            background: rgba(15, 15, 15, 0.9);
            backdrop-filter: blur(25px);
            padding: 40px 50px;
            width: 100%; max-width: 480px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 40px 100px rgba(0,0,0,0.8);
            text-align: center;
        }

        .logo { font-size: 1.3rem; font-weight: 700; letter-spacing: 6px; color: var(--blanco); margin-bottom: 30px; text-transform: uppercase; }

        .input-box { margin-bottom: 25px; text-align: left; position: relative; }

        label { display: block; font-size: 0.65rem; color: var(--blanco); text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px; font-weight: 600; }

        input {
            width: 100%; background: transparent; border: none; border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 8px 0; color: var(--blanco); font-size: 0.95rem; outline: none; transition: border-color 0.4s;
        }

        input:focus { border-bottom: 1px solid var(--azul-porsche); }

        /* --- TOOLTIP HOVER --- */
        .input-box::after {
            content: attr(data-hint);
            position: absolute;
            top: -25px;
            right: 0;
            font-size: 0.65rem;
            color: var(--azul-porsche);
            font-weight: 700;
            opacity: 0;
            transition: opacity 0.3s ease, transform 0.3s ease;
            transform: translateY(5px);
            pointer-events: none;
            text-transform: none;
        }

        .input-box:hover::after {
            opacity: 1;
            transform: translateY(0);
        }

        /* --- STRENGTH BAR --- */
        .strength-bar-container { width: 100%; height: 3px; background: rgba(255, 255, 255, 0.1); margin-top: 8px; }
        .strength-bar { height: 100%; width: 0%; transition: all 0.4s ease; }

        .btn-submit {
            width: 100%; padding: 15px; background: var(--blanco); color: var(--negro-puro); border: none;
            font-weight: 700; text-transform: uppercase; letter-spacing: 3px; cursor: pointer; transition: all 0.3s ease; margin-top: 20px;
        }

        .btn-submit:hover { background: var(--azul-porsche); color: var(--blanco); }

        .links { margin-top: 25px; font-size: 0.75rem; }
        .links a { color: rgba(255, 255, 255, 0.5); text-decoration: none; transition: color 0.3s; }
        .links a:hover { color: var(--blanco); }

        .error-msg { color: var(--rojo); font-size: 0.8rem; margin-bottom: 15px; font-weight: 600; text-align: left; }
    </style>
</head>
<body>

    <div class="background-image"></div>

    <div class="register-card">
        <div class="logo">GBA<span>.</span>ADVOS</div>

        <?php if ($error !== ""): ?>
            <p class="error-msg">✕ <?= $error ?></p>
        <?php endif; ?>

        <form method="POST" id="regForm">
            <div class="input-box" data-hint="ex: Agustin Martinez">
                <label>Nom Complet</label>
                <input type="text" name="nom_complet" required autocomplete="off">
            </div>

            <div class="input-box" data-hint="ex: hola@gmail.com">
                <label>Correu Electrònic</label>
                <input type="email" name="email" required autocomplete="off">
            </div>

            <div class="input-box">
                <label>Contrasenya</label>
                <input type="password" name="contrasenya" id="passInput" required>
                <div class="strength-bar-container">
                    <div class="strength-bar" id="strengthBar"></div>
                </div>
            </div>

            <div class="input-box">
                <label>Confirmar Contrasenya</label>
                <input type="password" name="confirmar_contrasenya" required>
            </div>

            <button type="submit" class="btn-submit">Registrar</button>
        </form>

        <div class="links">
            <a href="login.php">Ja tens compte? Inicia Sessió</a>
        </div>
    </div>

    <script>
        const passInput = document.getElementById('passInput');
        const strengthBar = document.getElementById('strengthBar');

        passInput.addEventListener('input', () => {
            const val = passInput.value;
            let strength = 0;
            if (val.length > 5) strength += 33;
            if (val.match(/[A-Z]/) && val.match(/[0-9]/)) strength += 33;
            if (val.match(/[^A-Za-z0-9]/)) strength += 34;

            strengthBar.style.width = strength + '%';
            if (strength <= 33) strengthBar.style.backgroundColor = '#ff4d4d';
            else if (strength <= 66) strengthBar.style.backgroundColor = '#ffcc00';
            else strengthBar.style.backgroundColor = '#00cc66';
            if(val.length === 0) strengthBar.style.width = '0%';
        });
    </script>
</body>
</html>
