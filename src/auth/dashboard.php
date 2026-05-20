<?php
session_start();
require_once '../../configDB.php';

// 1. FILTRO DE SEGURIDAD GENERAL
if (!isset($_SESSION['usuari_id'])) {
    header('Location: login.php');
    exit;
}

$rol = $_SESSION['rol'];
$nom_usuari = $_SESSION['nom_usuari'];
$user_id = $_SESSION['usuari_id'];

// Inicializamos contenedores de datos para evitar errores en el HTML
$casos = [];
$abogados = [];
$clientes = [];

try {
    // ==================== LÓGICA DE EXTRACCIÓN DE DATOS SEGÚN EL ROL ====================
    
    if ($rol === 'admin') {
        // El ADMIN lo ve todo: todos los casos y todos los abogados para poder borrarlos
        $casos = $pdo->query("SELECT * FROM casos ORDER BY fecha_creacion DESC")->fetchAll(PDO::FETCH_ASSOC);
        $abogados = $pdo->query("SELECT id, nombre, email FROM usuarios WHERE rol = 'abogado'")->fetchAll(PDO::FETCH_ASSOC);
        
    } elseif ($rol === 'abogado') {
        // El ABOGADO solo ve sus casos y la lista de clientes registrados para su información
        $stmt = $pdo->prepare("SELECT * FROM casos WHERE abogado_id = ? ORDER BY fecha_creacion DESC");
        $stmt->execute([$user_id]);
        $casos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $clientes = $pdo->query("SELECT id, nombre, email FROM usuarios WHERE rol = 'cliente'")->fetchAll(PDO::FETCH_ASSOC);
        
    } elseif ($rol === 'cliente') {
        // El CLIENTE solo lee sus propios casos asignados (Operación Read Estricta)
        $stmt = $pdo->prepare("SELECT * FROM casos WHERE cliente_id = ? ORDER BY fecha_creacion DESC");
        $stmt->execute([$user_id]);
        $casos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // El cliente también ve los abogados disponibles del bufete por si quiere consultar
        $abogados = $pdo->query("SELECT nombre, email FROM usuarios WHERE rol = 'abogado'")->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    die("Error crític en el motor de dades del Dashboard: " . $e->getMessage());
}

// Captura de notificaciones de éxito de acciones CRUD (vienen de eliminar_caso.php o eliminar_abogado.php)
$mensaje_status = "";
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'deleted') $mensaje_status = "L'expedient s'ha eliminat correctament de la base de dades.";
    if ($_GET['status'] === 'user_deleted') $mensaje_status = "El compte de l'advocat s'ha donat de baixa correctament.";
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>GBA ADVOS | Panell Principal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --porsche-blue: #004a99; --negro: #0a0a0a; --blanco: #ffffff; --gris: #111; --rojo: #ff4d4d; --oro: #d4af37; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--negro); color: var(--blanco); padding: 40px; }
        
        /* Cabecera Estilo Porsche */
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #222; padding-bottom: 20px; margin-bottom: 40px; }
        .logo { font-weight: 700; letter-spacing: 5px; font-size: 1.4rem; text-transform: uppercase; }
        .logo span { color: var(--porsche-blue); }
        .user-badge { font-size: 0.8rem; color: #888; }
        .user-badge strong { color: var(--blanco); }
        .role-tag { font-size: 0.65rem; font-weight: 700; padding: 3px 6px; border-radius: 2px; margin-left: 5px; text-transform: uppercase; vertical-align: middle; }
        .role-admin { background: rgba(212, 175, 55, 0.1); color: var(--oro); border: 1px solid var(--oro); }
        .role-abogado { background: rgba(0, 74, 153, 0.1); color: #4da3ff; border: 1px solid var(--porsche-blue); }
        .role-cliente { background: rgba(40, 167, 69, 0.1); color: #28a745; border: 1px solid #28a745; }
        
        .btn-logout { color: var(--rojo); text-decoration: none; font-weight: 700; margin-left: 20px; border: 1px solid var(--rojo); padding: 6px 12px; font-size: 0.7rem; letter-spacing: 1px; text-transform: uppercase; transition: 0.3s; }
        .btn-logout:hover { background: var(--rojo); color: white; }

        /* Estructura en Grid */
        .dashboard-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 40px; }
        .main-panel { display: flex; flex-direction: column; gap: 30px; }
        .card { background: var(--gris); border: 1px solid #222; padding: 30px; border-radius: 4px; position: relative; }
        .card h3 { font-weight: 300; letter-spacing: 2px; margin-bottom: 25px; text-transform: uppercase; font-size: 1.1rem; border-left: 3px solid var(--porsche-blue); padding-left: 10px; }
        
        /* Botones de acción generales */
        .btn-action { display: inline-block; background: var(--blanco); color: var(--negro); text-decoration: none; padding: 12px 24px; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; border: none; transition: 0.3s; cursor: pointer; }
        .btn-action:hover { background: var(--porsche-blue); color: var(--blanco); }
        .btn-admin-db { background: var(--oro); color: black; }
        .btn-admin-db:hover { background: white; color: black; }

        /* Listas y Tablas Dark */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #161616; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 2px; color: #555; padding: 15px 20px; text-align: left; border-bottom: 1px solid #222; }
        td { padding: 15px 20px; font-size: 0.85rem; border-bottom: 1px solid #1a1a1a; color: #ccc; }
        tr:hover td { color: #fff; background: #151515; }

        .item-row { display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid #1a1a1a; }
        .item-row:last-child { border: none; }

        /* Botones de operaciones de fila (CRUD) */
        .btn-crud { text-decoration: none; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; padding: 5px 10px; border: 1px solid; margin-left: 8px; transition: 0.3s; display: inline-block; }
        .btn-crud-edit { color: #4da3ff; border-color: #4da3ff; }
        .btn-crud-edit:hover { background: #4da3ff; color: black; }
        .btn-crud-delete { color: var(--rojo); border-color: var(--rojo); }
        .btn-crud-delete:hover { background: var(--rojo); color: white; }

        .alert-success { background: rgba(0, 74, 153, 0.1); color: #4da3ff; border: 1px solid var(--porsche-blue); padding: 15px; text-align: center; font-size: 0.85rem; margin-bottom: 30px; }
        .no-data { color: #444; font-size: 0.85rem; padding: 20px 0; }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo">GBA<span>.</span>ADVOS</div>
        <div class="user-badge">
            Usuari: <strong><?= htmlspecialchars($nom_usuari) ?></strong> 
            <span class="role-tag role-<?= $rol ?>"><?= $rol ?></span>
            <a href="logout.php" class="btn-logout">Tancar Sessió</a>
        </div>
    </div>

    <?php if ($mensaje_status): ?>
        <div class="alert-success"><?= $mensaje_status ?></div>
    <?php endif; ?>

    <div class="dashboard-grid">
        
        <div class="main-panel">

            <?php if ($rol === 'admin'): ?>
                <div class="card" style="border-color: rgba(212, 175, 55, 0.3);">
                    <h3 style="border-left-color: var(--oro); color: var(--oro);">Herramientas Estructurales de Infraestructura</h3>
                    <p style="font-size: 0.85rem; color: #888; margin-bottom: 20px;">Acceso restringido directo al motor de datos relacional para auditorías de la base de datos PHP_GBA.</p>
                    <a href="http://localhost/phpmyadmin/index.php?route=/database/structure&server=1&db=PHP_GBA" target="_blank" class="btn-action btn-admin-db">Abrir Estructura en phpMyAdmin</a>
                </div>
            <?php endif; ?>

            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3><?= $rol === 'cliente' ? 'Els Meus Expedients Contractats' : 'Gestió Global d\'Expedients' ?></h3>
                    <?php if ($rol === 'abogado'): ?>
                        <a href="crear_caso.php" class="btn-action" style="padding: 8px 16px; font-size: 0.65rem;">+ Crear Nou Cas</a>
                    <?php endif; ?>
                </div>

                <?php if (count($casos) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Ref. Expedient</th>
                                <th>Títol del Procediment</th>
                                <th>Àrea</th>
                                <th>Estat</th>
                                <?php if ($rol !== 'cliente'): ?>
                                    <th style="text-align: right;">Accions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($casos as $c): ?>
                                <tr>
                                    <td style="font-weight: 600; color: #fff;"><?= htmlspecialchars($c['num_expedient']) ?></td>
                                    <td><?= htmlspecialchars($c['titulo']) ?></td>
                                    <td><?= htmlspecialchars($c['area_dret']) ?></td>
                                    <td><span style="color: #4da3ff; font-size: 0.8rem;"><?= htmlspecialchars($c['estado']) ?></span></td>
                                    
                                    <?php if ($rol !== 'cliente'): ?>
                                        <td style="text-align: right;">
                                            <?php if ($rol === 'abogado'): ?>
                                                <a href="editar_caso.php?id=<?= $c['id'] ?>" class="btn-crud btn-crud-edit">Editar</a>
                                                <a href="eliminar_caso.php?id=<?= $c['id'] ?>" class="btn-crud btn-crud-delete" onclick="return confirm('Segur que vols eliminar permanentment aquest cas?');">Eliminar</a>
                                            <?php elseif ($rol === 'admin'): ?>
                                                <a href="eliminar_caso.php?id=<?= $c['id'] ?>" class="btn-crud btn-crud-delete" onclick="return confirm('ADMIN: Segur que vols purgar aquest cas del sistema?');">Purgar Cas</a>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="no-data">No s'han trobat expedients judicials en aquest apartat.</p>
                <?php endif; ?>
            </div>

            <?php if ($rol === 'admin' || $rol === 'cliente'): ?>
                <div class="card">
                    <h3><?= $rol === 'admin' ? 'Panell de Comptes de Lletrats' : 'Advocats de la Firma Disponibles' ?></h3>
                    <?php if (count($abogados) > 0): ?>
                        <?php foreach ($abogados as $ab): ?>
                            <div class="item-row">
                                <div>
                                    <strong><?= htmlspecialchars($ab['nombre']) ?></strong><br>
                                    <span style="font-size: 0.8rem; color: #666;"><?= htmlspecialchars($ab['email']) ?></span>
                                </div>
                                <div>
                                    <?php if ($rol === 'admin'): ?>
                                        <a href="eliminar_abogado.php?id=<?= $ab['id'] ?>" class="btn-crud btn-crud-delete" onclick="return confirm('ADMIN: Segur que vols revocar l\'accés i eliminar el compte d\'aquest advocat?');">Donar de Baixa</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-data">No hi ha advocats registrats en el sistema.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>

        <div class="sidebar">
            
            <div class="card">
                <h3><?= $rol === 'abogado' ? 'La Meva Agenda de Judicis' : 'Notificacions de la Firma' ?></h3>
                <p style="font-size: 0.8rem; color: #555; line-height: 1.6;">
                    <?php if ($rol === 'abogado'): ?>
                        - **Avui 10:00h**: Vista prèvia al Jutjat del Social de Barcelona (Exp. GBA-2026-1102).<br>
                        - **Demà 12:30h**: Reunió de mediació amb la contrapart en sala de juntes.
                    <?php else: ?>
                        Benvingut al sistema central de comunicacions de GBA ADVOS. El canal de dades entre el navegador i el nostre servidor es troba completament xifrat sota protocols actius SSL/HTTPS per a la seva seguretat.
                    <?php endif; ?>
                </p>
            </div>

            <?php if ($rol === 'abogado'): ?>
                <div class="card">
                    <h3>Clients de la Firma</h3>
                    <?php if (count($clientes) > 0): ?>
                        <?php foreach ($clientes as $cli): ?>
                            <div style="padding: 10px 0; border-bottom: 1px solid #1c1c1c; font-size: 0.85rem;">
                                <strong><?= htmlspecialchars($cli['nombre']) ?></strong><br>
                                <span style="color: #555; font-size: 0.75rem;"><?= htmlspecialchars($cli['email']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-data">No hi ha clients en la base de dades.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

</body>
</html>
