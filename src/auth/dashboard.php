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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Bufet GBA</title>
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #3498db;
            --admin-bg: #fff3cd;
            --admin-border: #ffeeba;
            --user-bg: #d1ecf1;
            --user-border: #bee5eb;
            --danger: #e74c3c;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background-color: var(--primary);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .container {
            padding: 2rem;
            max-width: 1000px;
            margin: 0 auto;
            width: 100%;
        }

        .card {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-top: 1rem;
        }

        .admin-section {
            background-color: var(--admin-bg);
            border-left: 5px solid #ffc107;
            padding: 1.5rem;
            border-radius: 4px;
        }

        .user-section {
            background-color: var(--user-bg);
            border-left: 5px solid var(--accent);
            padding: 1.5rem;
            border-radius: 4px;
        }

        .btn {
            display: inline-block;
            padding: 10px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: transform 0.2s, opacity 0.2s;
            cursor: pointer;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .btn-admin {
            background-color: #ffc107;
            color: #212529;
            border: none;
        }

        .btn-logout {
            color: white;
            background-color: var(--danger);
            font-size: 0.9rem;
        }

        .welcome-msg {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            background: rgba(255,255,255,0.2);
        }
    </style>
</head>
<body>

    <header>
        <div>
            <strong>GBA Advos</strong> 
            <span class="badge"><?= htmlspecialchars($_SESSION['rol']) ?></span>
        </div>
        <a href="logout.php" class="btn btn-logout" id="logoutBtn">Tancar sessió</a>
    </header>

    <div class="container">
        <div class="card">
            <h1 id="greeting">Hola, <?= htmlspecialchars($_SESSION['nom_usuari']) ?>!</h1>
            
            <?php if ($_SESSION['rol'] === 'admin'): ?>
                <div class="admin-section">
                    <h2>Accés a panell d'administrador</h2>
                    <p>Com a administrador, tens permisos per gestionar la base de dades d'usuaris i rols.</p>
                    <br>
                    <a href="admin_usuarios.php" class="btn btn-admin">ACCESO</a>
                </div>
            <?php else: ?>
                <div class="user-section">
                    <h3>Panell d'Advocat</h3>
                    <p>Aquí es tu sistema internos donde apareceran tus casos y clientes.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const greeting = document.getElementById('greeting');
        const hora = new Date().getHours();
        let salutacio = "Hola";

        if (hora < 12) salutacio = "Bon dia";
        else if (hora < 20) salutacio = "Bona tarda";
        else salutacio = "Bon vespre";

        greeting.innerHTML = `${salutacio}, <?= htmlspecialchars($_SESSION['nom_usuari']) ?>!`;

        document.getElementById('logoutBtn').addEventListener('click', function(e) {
            if (!confirm('Segur que vols tancar la sessió?')) {
                e.preventDefault();
            }
        });
    </script>

</body>
</html>
