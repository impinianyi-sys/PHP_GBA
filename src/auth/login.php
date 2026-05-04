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
            $error = "Accés denegat. Credencials incorrectes.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GBA ADVOS | Accés Premium</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --azul-porsche: #004a99;
            --negro-puro: #000000;
            --blanco: #ffffff;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--negro-puro);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
        }

        /* IMAGEN DE FONDO JURÍDICA */
        .background-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            /* Imagen de abogados en juicio / despacho */
            background-image: url('https://images.unsplash.com/photo-1505664194779-8beaceb93744?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            filter: brightness(0.25) contrast(1.1); /* Oscurecemos para el estilo Porsche */
            z-index: -1;
        }

        /* CONTENEDOR LOGIN */
        .login-card {
            background: rgba(15, 15, 15, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 60px;
            width: 100%;
            max-width: 420px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 40px 100px rgba(0,0,0,0.8);
            text-align: center;
        }

        .logo {
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: 8px;
            color: var(--blanco);
            margin-bottom: 50px;
            text-transform: uppercase;
        }

        .logo span { color: var(--azul-porsche); }

        .input-box {
            margin-bottom: 35px;
            text-align: left;
        }

        /* TEXTO EN BLANCO PARA ETIQUETAS */
        label {
            display: block;
            font-size: 0.7rem;
            color: var(--blanco); 
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 12px;
            font-weight: 600;
        }

        /* TEXTO EN BLANCO PARA INPUTS */
        input {
            width: 100%;
            background: transparent;
            border: none;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            padding: 10px 0;
            color: var(--blanco);
            font-size: 1rem;
            outline: none;
            transition: border-color 0.4s;
        }

        input:focus {
            border-bottom: 2px solid var(--azul-porsche);
        }

        .btn-submit {
            width: 100%;
            padding: 18px;
            background: var(--blanco);
            color: var(--negro-puro);
            border: none;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 15px;
        }

        .btn-submit:hover {
            background: var(--azul-porsche);
            color: var(--blanco);
        }

        .links {
            margin-top: 35px;
            display: flex;
            justify-content: space-between;
            font-size: 0.75rem;
        }

        .links a {
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            transition: color 0.3s;
        }

        .links a:hover { color: var(--blanco); }

        .error { color: #ff4d4d; font-size: 0.85rem; margin-bottom: 20px; font-weight: 600; }
    </style>
</head>
<body>

    <div class="background-image"></div>

    <div class="login-card">
        <div class="logo">GBA<span>.</span>ADVOS</div>

        <?php if (isset($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="input-box">
                <label for="usuari">Usuari</label>
                <input type="text" name="nom_usuari" id="usuari" required autofocus>
            </div>

            <div class="input-box">
                <label for="pass">Contrasenya</label>
                <input type="password" name="contrasenya" id="pass" required>
            </div>

            <button type="submit" class="btn-submit">Accedir</button>
        </form>

        <div class="links">
            <a href="registro.php">Registre</a>
            <a href="#">Recuperar clau</a>
        </div>
    </div>

</body>
</html>
