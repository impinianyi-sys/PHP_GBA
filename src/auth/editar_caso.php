<?php
session_start();
require_once '../../configDB.php';

// Seguridad: Solo abogados pueden entrar
if (!isset($_SESSION['usuari_id']) || $_SESSION['rol'] !== 'abogado') {
    header('Location: login.php');
    exit;
}

$error = "";
$success = "";
$es_edicion = false;
$datos = [];

// 1. CARREGAR DADES SI EXISTEIX UN ID A LA URL
if (isset($_GET['id'])) {
    $es_edicion = true;
    $stmt = $pdo->prepare("SELECT * FROM casos WHERE id = ? AND abogado_id = ?");
    $stmt->execute([$_GET['id'], $_SESSION['usuari_id']]);
    $datos = $stmt->fetch();

    if (!$datos) {
        die("Error: Cas no trobat o no tens permís per editar-lo.");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recollida de dades
    $titol = $_POST['titulo'];
    $expedient = $_POST['num_expedient'];
    $area = ($_POST['area_dret'] === 'Altres') ? $_POST['area_altres'] : $_POST['area_dret'];
    $data_obertura = $_POST['data_obertura'];
    $estat = $_POST['estado'];
    
    // Dades Client
    $client_id = $_POST['cliente_id'];
    $dni_tipus = $_POST['client_dni_tipus'];
    $dni_num = $_POST['client_dni_num'];
    $rol_client = ($_POST['client_rol'] === 'Altres') ? $_POST['rol_altres'] : $_POST['client_rol'];
    
    // Contrapart
    $adversari = $_POST['adversari_nom'];
    $adversari_advocat = $_POST['adversari_advocat'] ?? null;

    try {
        if ($es_edicion) {
            // LÒGICA DE UPDATE (CRUD)
            $sql = "UPDATE casos SET 
                    titulo = ?, area_dret = ?, fecha_creacion = ?, estado = ?, 
                    cliente_id = ?, client_dni_tipus = ?, client_dni_num = ?, 
                    client_rol = ?, adversari_nom = ?, adversari_advocat = ?
                    WHERE id = ? AND abogado_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $titol, $area, $data_obertura, $estat, 
                $client_id, $dni_tipus, $dni_num, 
                $rol_client, $adversari, $adversari_advocat, 
                $_GET['id'], $_SESSION['usuari_id']
            ]);
            $success = "Expedient actualitzat correctament.";
            
            // Refresquem les dades per mostrar-les al formulari actualitzades
            $stmt = $pdo->prepare("SELECT * FROM casos WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $datos = $stmt->fetch();
        } else {
            // LÒGICA DE INSERT (Original)
            $sql = "INSERT INTO casos (num_expedient, titulo, area_dret, fecha_creacion, estado, abogado_id, cliente_id, client_dni_tipus, client_dni_num, client_rol, adversari_nom, adversari_advocat) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $expedient, $titol, $area, $data_obertura, $estat, 
                $_SESSION['usuari_id'], $client_id, $dni_tipus, $dni_num, $rol_client, 
                $adversari, $adversari_advocat
            ]);
            $success = "Cas registrat correctament.";
        }
    } catch (PDOException $e) {
        $error = "Error al processar: " . $e->getMessage();
    }
}

// Obtenir llista de clients
$clients = $pdo->query("SELECT id, nombre FROM usuarios WHERE rol = 'cliente'")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>GBA | <?= $es_edicion ? "Editar" : "Nou" ?> Expedient</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root { --porsche-blue: #004a99; --negro: #0a0a0a; --blanco: #ffffff; }
        body { font-family: 'Inter', sans-serif; background: var(--negro); color: var(--blanco); padding: 40px; }
        .form-container { max-width: 900px; margin: 0 auto; background: #111; padding: 50px; border: 1px solid #222; }
        h2 { font-weight: 300; letter-spacing: 5px; text-transform: uppercase; margin-bottom: 40px; color: var(--porsche-blue); }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .section-title { grid-column: span 2; font-size: 0.7rem; letter-spacing: 3px; color: #555; text-transform: uppercase; border-bottom: 1px solid #222; padding-bottom: 10px; }
        .input-group { display: flex; flex-direction: column; }
        label { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px; color: #888; }
        input, select, textarea { background: transparent; border: none; border-bottom: 1px solid #333; padding: 10px 0; color: white; outline: none; }
        input:focus, select:focus { border-bottom-color: var(--porsche-blue); }
        .button-group { grid-column: span 2; display: flex; gap: 20px; margin-top: 30px; }
        .btn-save { flex: 2; background: white; color: black; border: none; padding: 20px; font-weight: 700; text-transform: uppercase; letter-spacing: 3px; cursor: pointer; transition: 0.3s; }
        .btn-save:hover { background: var(--porsche-blue); color: white; }
        .btn-back { flex: 1; display: flex; align-items: center; justify-content: center; background: transparent; color: white; border: 1px solid #333; text-decoration: none; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; }
        .other-input { display: none; margin-top: 10px; }
        .alert { grid-column: span 2; padding: 20px; margin-bottom: 20px; font-size: 0.9rem; border: 1px solid; text-align: center; }
        .success { background: rgba(0, 74, 153, 0.1); color: #4da3ff; border-color: var(--porsche-blue); }
        .error { background: rgba(255, 0, 0, 0.1); color: #ff4d4d; border-color: #ff4d4d; }
        .success a { color: white; font-weight: 700; text-decoration: underline; margin-left: 10px; }
    </style>
</head>
<body>

<div class="form-container">
    <h2><?= $es_edicion ? "Modificació d'Expedient" : "Registre d'Expedient" ?></h2>

    <?php if($success): ?>
        <div class="alert success"><?= $success ?> | <a href="dashboard.php">TORNAR AL PANELL</a></div>
    <?php endif; ?>

    <form method="POST" class="grid">
        <div class="section-title">Informació del Procediment</div>
        <div class="input-group">
            <label>Títol del Cas</label>
            <input type="text" name="titulo" value="<?= $es_edicion ? htmlspecialchars($datos['titulo']) : '' ?>" required>
        </div>
        <div class="input-group">
            <label>Num. Expedient Intern</label>
            <input type="text" name="num_expedient" value="<?= $es_edicion ? $datos['num_expedient'] : "GBA-" . date("Y") . "-" . rand(1000, 9999) ?>" readonly>
        </div>

        <div class="input-group">
            <label>Àrea del Dret</label>
            <select name="area_dret">
                <?php $areas = ['Civil', 'Penal', 'Laboral', 'Mercantil', 'Família']; ?>
                <?php foreach($areas as $a): ?>
                    <option value="<?= $a ?>" <?= ($es_edicion && $datos['area_dret'] == $a) ? 'selected' : '' ?>><?= $a ?></option>
                <?php endforeach; ?>
                <option value="Altres">Altres...</option>
            </select>
        </div>

        <div class="input-group">
            <label>Estat</label>
            <select name="estado">
                <?php $estados = ['Prospecte', 'En negociació', 'Judicialitzat']; ?>
                <?php foreach($estados as $e): ?>
                    <option value="<?= $e ?>" <?= ($es_edicion && $datos['estado'] == $e) ? 'selected' : '' ?>><?= $e ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="section-title">Dades del Client i Contrapart</div>
        <div class="input-group">
            <label>Seleccionar Client</label>
            <select name="cliente_id" required>
                <?php foreach($clients as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($es_edicion && $datos['cliente_id'] == $c['id']) ? 'selected' : '' ?>><?= $c['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="input-group">
            <label>Número Identificació Client</label>
            <input type="text" name="client_dni_num" value="<?= $es_edicion ? $datos['client_dni_num'] : '' ?>" required>
        </div>

        <div class="input-group">
            <label>Nom de l'Adversari</label>
            <input type="text" name="adversari_nom" value="<?= $es_edicion ? htmlspecialchars($datos['adversari_nom']) : '' ?>" required>
        </div>

        <div class="input-group">
            <label>Data d'Obertura</label>
            <input type="date" name="data_obertura" value="<?= $es_edicion ? $datos['fecha_creacion'] : date('Y-m-d') ?>">
        </div>

        <div class="button-group">
            <button type="submit" class="btn-save"><?= $es_edicion ? "Guardar Canvis" : "Registrar Expedient" ?></button>
            <a href="dashboard.php" class="btn-back">Cancel·lar</a>
        </div>
    </form>
</div>

</body>
</html>
