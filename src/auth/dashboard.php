<?php
session_start();
require_once '../../configDB.php';

if (!isset($_SESSION['usuari_id'])) {
    header('Location: login.php');
    exit;
}

$rol = $_SESSION['rol'];
$nom = $_SESSION['nom_usuari'];
$user_id = $_SESSION['usuari_id'];

$mis_casos = [];
$todos_los_abogados = [];

try {
    // 1. LÓGICA PARA ABOGADOS: Solo sus propios casos
    if ($rol === 'abogado') {
        $stmt = $pdo->prepare("SELECT c.*, u.nombre as cliente_nombre 
                               FROM casos c 
                               JOIN usuarios u ON c.cliente_id = u.id 
                               WHERE c.abogado_id = ? 
                               ORDER BY c.fecha_creacion DESC");
        $stmt->execute([$user_id]);
        $mis_casos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. LÓGICA PARA CLIENTES: Sus casos y el directorio de abogados
    if ($rol === 'cliente') {
        // Sus casos específicos
        $stmt = $pdo->prepare("SELECT c.*, u.nombre as abogado_nombre 
                               FROM casos c 
                               JOIN usuarios u ON c.abogado_id = u.id 
                               WHERE c.cliente_id = ? 
                               ORDER BY c.fecha_creacion DESC");
        $stmt->execute([$user_id]);
        $mis_casos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. DIRECTORIO GLOBAL DE ABOGADOS (Para Admins y Clientes)
    // Se actualiza automáticamente al leer de la tabla 'usuarios' donde rol = 'abogado'
    if ($rol === 'admin' || $rol === 'cliente') {
        $stmt = $pdo->prepare("SELECT id, nombre, email, especialidad FROM usuarios WHERE rol = 'abogado' ORDER BY nombre ASC");
        $stmt->execute();
        $todos_los_abogados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    $error_msg = "Error de connexió amb el sistema central.";
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>GBA ADVOS | Corporate Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --porsche-blue: #004a99;
            --negro: #0a0a0a;
            --gris-card: #141414;
            --blanco: #ffffff;
            --border: rgba(255,255,255,0.08);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--negro); color: var(--blanco); line-height: 1.6; }

        header {
            padding: 20px 5%; background: rgba(0,0,0,0.95);
            display: flex; justify-content: space-between; align-items: center;
            border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 100;
        }
        .logo { font-weight: 700; letter-spacing: 5px; text-transform: uppercase; font-size: 1.1rem; }
        .logo span { color: var(--porsche-blue); }
        
        .btn-logout { 
            color: var(--blanco); text-decoration: none; font-size: 0.65rem; 
            border: 1px solid #333; padding: 10px 20px; font-weight: 700;
            letter-spacing: 2px; transition: 0.3s;
        }
        .btn-logout:hover { background: #ff4d4d; border-color: #ff4d4d; }

        .container { padding: 60px 8%; max-width: 1600px; margin: 0 auto; }
        .welcome { margin-bottom: 40px; border-left: 4px solid var(--porsche-blue); padding-left: 20px; }
        .welcome span { color: var(--porsche-blue); text-transform: uppercase; font-size: 0.7rem; letter-spacing: 3px; font-weight: 700; }
        .welcome h1 { font-size: 2.5rem; font-weight: 300; }

        .dashboard-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        @media (max-width: 1100px) { .dashboard-grid { grid-template-columns: 1fr; } }

        .card { background: var(--gris-card); padding: 35px; border: 1px solid var(--border); height: 100%; }
        .card h3 { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 3px; color: #555; margin-bottom: 25px; border-bottom: 1px solid #222; padding-bottom: 10px; }

        /* LISTADOS */
        .list-item { 
            padding: 20px; background: rgba(255,255,255,0.02); 
            border-left: 2px solid var(--porsche-blue); margin-bottom: 12px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .item-info h4 { font-size: 1rem; font-weight: 600; margin-bottom: 4px; }
        .item-info p { font-size: 0.8rem; color: #777; }
        
        .badge { font-size: 0.6rem; padding: 4px 10px; background: #222; border-radius: 2px; text-transform: uppercase; letter-spacing: 1px; }

        /* BOTONES PORSCHE */
        .btn-action {
            display: block; width: 100%; padding: 18px; background: var(--blanco);
            color: var(--negro); text-align: center; text-decoration: none; font-weight: 700;
            text-transform: uppercase; letter-spacing: 2px; font-size: 0.7rem; 
            margin-top: 20px; transition: 0.4s; border: none;
        }
        .btn-action:hover { background: var(--porsche-blue); color: var(--blanco); }

        .admin-link { background: transparent; border: 1px solid #333; color: white; margin-top: 10px; }
    </style>
</head>
<body>

<header>
    <div class="logo">GBA<span>.</span>ADVOS</div>
    <a href="logout.php" class="btn-logout">CERRAR SESIÓN</a>
</header>

<div class="container">
    <div class="welcome">
        <span>Àrea de Gestió | <?= strtoupper($rol) ?></span>
        <h1>Benvingut, <?= $nom ?></h1>
    </div>

    <div class="dashboard-grid">
        
        <!-- COLUMNA IZQUIERDA: CASOS (Para Abogados y Clientes) -->
        <div class="column">
            <?php if ($rol !== 'admin'): ?>
                <div class="card">
                    <h3>Expedients Actius</h3>
                    <?php if (empty($mis_casos)): ?>
                        <p style="color: #444;">No s'han trobat casos assignats al seu perfil.</p>
                    <?php else: ?>
                        <?php foreach ($mis_casos as $caso): ?>
                            <div class="list-item">
                                <div class="item-info">
                                    <h4><?= htmlspecialchars($caso['titulo']) ?></h4>
                                    <p>ID: <?= htmlspecialchars($caso['num_expedient']) ?> | <?= ($rol === 'abogado') ? 'Client: ' . $caso['cliente_nombre'] : 'Advocat: ' . $caso['abogado_nombre'] ?></p>
                                </div>
                                <span class="badge"><?= htmlspecialchars($caso['estado']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if ($rol === 'abogado'): ?>
                        <a href="crear_caso.php" class="btn-action">Registrar Nou Cas</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- VISTA ESPECIAL ADMIN: ACCESO DB -->
                <div class="card">
                    <h3>Infraestructura de Dades</h3>
                    <p style="font-size: 0.9rem; color: #888; margin-bottom: 20px;">Control total del motor MySQL GBA_ADVOS. Accediu per a manteniment de taules o auditories.</p>
                    <a href="http://localhost/phpmyadmin/index.php?db=PHP_GBA" target="_blank" class="btn-action">Obrir phpMyAdmin</a>
                    <a href="#" class="btn-action admin-link">Registres del Servidor</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- COLUMNA DERECHA: DIRECTORIO DE ABOGADOS (Para Clientes y Admin) -->
        <div class="column">
            <?php if ($rol === 'cliente' || $rol === 'admin'): ?>
                <div class="card">
                    <h3>Plantilla d'Advocats Actius</h3>
                    <p style="font-size: 0.75rem; color: #555; margin-bottom: 15px;">Aquesta llista s'actualitza en temps real amb les noves incorporacions de la firma.</p>
                    
                    <?php foreach ($todos_los_abogados as $abogado): ?>
                        <div class="list-item">
                            <div class="item-info">
                                <h4><?= htmlspecialchars($abogado['nombre']) ?></h4>
                                <p><?= htmlspecialchars($abogado['especialidad'] ?? 'Especialista Jurídic') ?></p>
                                <p style="color: var(--porsche-blue); margin-top: 5px;"><?= htmlspecialchars($abogado['email']) ?></p>
                            </div>
                            <span class="badge" style="color: #00cc66;">ACTIU</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- VISTA ESPECIAL ABOGADO: AGENDA -->
                <div class="card">
                    <h3>Agenda de Judicis i Cites</h3>
                    <div class="list-item">
                        <div class="item-info">
                            <h4>Reunió de Proves</h4>
                            <p>Demà - 09:00h | Sala 3</p>
                        </div>
                    </div>
                    <div class="list-item">
                        <div class="item-info">
                            <h4>Vista Oral #4402</h4>
                            <p>02 Maig - 11:30h | Jutjat Civil</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<footer style="padding: 40px; text-align: center; color: #222; font-size: 0.6rem; letter-spacing: 2px;">
    GBA ADVOS CORPORATE PERFORMANCE &copy; 2026
</footer>

</body>
</html>
