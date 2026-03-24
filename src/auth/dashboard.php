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
    <title>GBA ADVOS | Panel de Control</title>
    <style>
        
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');

        :root {
            --granate: #800020;
            --granate-hover: #a00028;
            --gris-fosc: #1a1a1a;
            --gris-mig: #333333;
            --gris-clar: #f8f9fa;
            --blanc: #ffffff;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--gris-clar);
            color: var(--gris-fosc);
            margin: 0;
            line-height: 1.6;
        }

        
        header {
            background-color: var(--gris-fosc);
            padding: 1rem 10%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .logo {
            color: var(--blanc); 
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo span {
            color: var(--granate);
        }

        
        .btn-logout {
            background-color: transparent;
            color: var(--blanc);
            border: 1px solid rgba(255,255,255,0.3);
            padding: 8px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            background-color: var(--granate);
            border-color: var(--granate);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(128, 0, 32, 0.3);
        }

        
        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .hero-card {
            background: var(--blanc);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border-left: 6px solid var(--granate);
        }

        .badge {
            display: inline-block;
            background: #eee;
            color: var(--gris-mig);
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        h1 { font-weight: 600; margin-bottom: 10px; }
        
            .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .btn-action {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
            background: var(--granate);
            color: var(--blanc);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.2s;
        }

        .btn-action:hover {
            background: var(--granate-hover);
        }

        .btn-secondary {
            background: var(--gris-mig);
        }

        
        .contact-list {
            margin-top: 40px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .contact-item {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #eee;
        }

	footer {
            text-align: center;
            padding: 40px;
            font-size: 0.8rem;
            color: #888;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">GBA ADVOS</div>
    <a href="logout.php" class="btn-logout" id="logoutLink"><b>Tancar sessió</b></a>
</header>

<div class="container">
    <div class="hero-card">
        <div class="badge"><?= $_SESSION['rol'] ?></div>
        <h1>Hola, <?= htmlspecialchars($_SESSION['nom_usuari']) ?></h1>
        <p>Sistema de gestió interna de GBA Advocats associats.</p>

        <hr style="margin: 30px 0; border: 0; border-top: 1px solid #f0f0f0;">

        <?php if ($_SESSION['rol'] === 'admin'): ?>
            <h3>Gestió de Sistema</h3>
            <div class="action-grid">
                <a href="admin_usuarios.php" class="btn-action">ACCESO USUARIOS</a>
                <a href="#" class="btn-action btn-secondary">CONFIGURACIÓ</a>
            </div>

        <?php elseif ($_SESSION['rol'] === 'abogado'): ?>
            <h3>Expedients i Licitacions</h3>
            <div class="action-grid">
                <a href="casos.php" class="btn-action">GESTIÓ DE CASOS</a>
                <a href="clientes.php" class="btn-action btn-secondary">LLISTA DE CLIENTS</a>
            </div>

        <?php else: ?>
            <h3>Estat de Procediments</h3>
            <div class="action-grid">
                <a href="mis_casos.php" class="btn-action">ELS MEUS CASOS</a>
            </div>

            <h3 style="margin-top: 50px;">Contacte Directe amb Advocats</h3>
            <div class="contact-list">
                <div class="contact-item">
                    <strong>Marc Salgado</strong><br>
                    <small>Penal | 150€/h</small><br>
                    msalgado@gba.cat
                </div>
                <div class="contact-item">
                    <strong>Elena Ruiz</strong><br>
                    <small>Civil | 120€/h</small><br>
                    eruiz@gba.cat
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer>
    &copy; 2026 GBA ADVOS - Bufet Jurídic d'Alta Especialització
</footer>

<script>
    document.getElementById('logoutLink').addEventListener('click', function(e) {
        if(!confirm('Segur que vols tancar la sessió ara?')) e.preventDefault();
    });
</script>

</body>
</html>
