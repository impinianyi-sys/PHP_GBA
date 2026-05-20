<?php
session_start();
require_once '../../configDB.php';

if (!isset($_SESSION['usuari_id']) || $_SESSION['rol'] !== 'abogado') {
    header('Location: login.php');
    exit;
}

$error = "";
$success = "";
$caso = [];

// 1. Verificación de seguridad: Comprobar el ID y la autoría del caso
if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$id_caso = (int)$_GET['id'];
$abogado_id = $_SESSION['usuari_id'];

// Cargar los datos actuales del expediente
$stmt = $pdo->prepare("SELECT * FROM casos WHERE id = ? AND abogado_id = ?");
$stmt->execute([$id_caso, $abogado_id]);
$caso = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$caso) {
    die("Error crític: Expedient no trobat o no tens permisos d'edició.");
}

// 2. Procesar la actualización si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titol = htmlspecialchars($_POST['titulo']);
    $area = $_POST['area_dret'];
    $descripcion = htmlspecialchars($_POST['descripcion']);
    $estat = $_POST['estado'];
    $client_id = $_POST['cliente_id'];
    $dni_tipus = $_POST['client_dni_tipus'];
    $dni_num = htmlspecialchars($_POST['client_dni_num']);
    $rol_client = $_POST['client_rol'];
    $adversari = htmlspecialchars($_POST['adversari_nom']);
    $adversari_advocat = htmlspecialchars($_POST['adversari_advocat']) ?? null;

    try {
        $sql = "UPDATE casos SET 
                titulo = ?, area_dret = ?, descripcion = ?, estado = ?, 
                cliente_id = ?, client_dni_tipus = ?, client_dni_num = ?, 
                client_rol = ?, adversari_nom = ?, adversari_advocat = ?
                WHERE id = ? AND abogado_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $titol, $area, $descripcion, $estat,
            $client_id, $dni_tipus, $dni_num, $rol_client,
            $adversari, $adversari_advocat, $id_caso, $abogado_id
        ]);
        
        $success = "Expedient actualitzat correctament.";
        
        // Recargar datos actualizados
        $stmt = $pdo->prepare("SELECT * FROM casos WHERE id = ?");
        $stmt->execute([$id_caso]);
        $caso = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error al guardar els canvis: " . $e->getMessage();
    }
}

$clients = $pdo->query("SELECT id, nombre FROM usuarios WHERE rol = 'cliente'")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>GBA | Modificar Expedient</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root { --porsche-blue: #004a99; --negro: #0a0a0a; --blanco: #ffffff; --gris: #111; }
        body { font-family: 'Inter', sans-serif; background: var(--negro); color: var(--blanco); padding: 40px; }
        .form-container { max-width: 900px; margin: 0 auto; background: var(--gris); padding: 50px; border: 1px solid #222; border-radius: 4px; }
        h2 { font-weight: 300; letter-spacing: 5px; text-transform: uppercase; margin-bottom: 40px; color: var(--porsche-blue); }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .section-title { grid-column: span 2; font-size: 0.7rem; letter-spacing: 3px; color: #555; text-transform: uppercase; border-bottom: 1px solid #222; padding-bottom: 10px; }
        .input-group { display: flex; flex-direction: column; }
        .full-width { grid-column: span 2; }
        label { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px; color: #888; }
        input, select, textarea { background: transparent; border: none; border-bottom: 1px solid #333; padding: 10px 0; color: white; outline: none; font-size: 0.9rem; }
        input:focus, select:focus, textarea:focus { border-bottom-color: var(--porsche-blue); }
        textarea { height: 80px; resize: none; }
        
        .button-group { grid-column: span 2; display: flex; gap: 20px; margin-top: 30px; }
        .btn-save { flex: 2; background: white; color: black; border: none; padding: 20px; font-weight: 700; text-transform: uppercase; letter-spacing: 3px; cursor: pointer; transition: 0.3s; }
        .btn-save:hover { background: var(--porsche-blue); color: white; }
        .btn-back { flex: 1; display: flex; align-items: center; justify-content: center; background: transparent; color: white; border: 1px solid #333; text-decoration: none; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; }
        
        .alert { grid-column: span 2; padding: 20px; margin-bottom: 20px; font-size: 0.9rem; border: 1px solid; text-align: center; }
        .success { background: rgba(0, 74, 153, 0.1); color: #4da3ff; border-color: var(--porsche-blue); }
        .error { background: rgba(255, 0, 0, 0.1); color: #ff4d4d; border-color: #ff4d4d; }
        .success a { color: white; font-weight: 700; text-decoration: underline; margin-left: 10px; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Modificació de l'Expedient (<?= $caso['num_expedient'] ?>)</h2>

    <?php if($success): ?>
        <div class="alert success"><?= $success ?> | <a href="dashboard.php">TORNAR AL PANELL</a></div>
    <?php endif; ?>
    <?php if($error): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="grid">
        <div class="section-title">Informació del Procediment</div>
        <div class="input-group">
            <label>Títol del Cas</label>
            <input type="text" name="titulo" value="<?= htmlspecialchars($caso['titulo']) ?>" required>
        </div>
        <div class="input-group">
            <label>Àrea del Dret</label>
            <select name="area_dret" required>
                <?php $areas = ['Civil', 'Penal', 'Laboral', 'Mercantil', 'Família']; ?>
                <?php foreach($areas as $a): ?>
                    <option value="<?= $a ?>" <?= $caso['area_dret'] === $a ? 'selected' : '' ?>><?= $a ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="input-group full-width">
            <label>Estat Actual del Procediment</label>
            <select name="estado">
                <option value="Prospecte" <?= $caso['estado'] === 'Prospecte' ? 'selected' : '' ?>>Prospecte</option>
                <option value="En negociació" <?= $caso['estado'] === 'En negociació' ? 'selected' : '' ?>>En negociació</option>
                <option value="Judicialitzat" <?= $caso['estado'] === 'Judicialitzat' ? 'selected' : '' ?>>Judicialitzat</option>
            </select>
        </div>
        <div class="input-group full-width">
            <label>Descripció / Resum Actualitzat</label>
            <textarea name="descripcion" required><?= htmlspecialchars($caso['descripcion']) ?></textarea>
        </div>

        <div class="section-title">Identificació del Client</div>
        <div class="input-group">
            <label>Client Assignat</label>
            <select name="cliente_id" required>
                <?php foreach($clients as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $caso['cliente_id'] == $c['id'] ? 'selected' : '' ?>><?= $c['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="input-group">
            <label>Tipus Identificació</label>
            <select name="client_dni_tipus">
                <option value="DNI" <?= $caso['client_dni_tipus'] === 'DNI' ? 'selected' : '' ?>>DNI</option>
                <option value="NIE" <?= $caso['client_dni_tipus'] === 'NIE' ? 'selected' : '' ?>>NIE</option>
                <option value="Pasaporte" <?= $caso['client_dni_tipus'] === 'Pasaporte' ? 'selected' : '' ?>>Passaport</option>
            </select>
        </div>
        <div class="input-group">
            <label>Número de Document</label>
            <input type="text" name="client_dni_num" value="<?= htmlspecialchars($caso['client_dni_num']) ?>" required>
        </div>
        <div class="input-group">
            <label>Rol Procedimental</label>
            <select name="client_rol">
                <option value="Demandant" <?= $caso['client_rol'] === 'Demandant' ? 'selected' : '' ?>>Demandant</option>
                <option value="Demandat" <?= $caso['client_rol'] === 'Demandat' ? 'selected' : '' ?>>Demandat</option>
                <option value="Recurrent" <?= $caso['client_rol'] === 'Recurrent' ? 'selected' : '' ?>>Recurrent</option>
                <option value="Testimoni" <?= $caso['client_rol'] === 'Testimoni' ? 'selected' : '' ?>>Testimoni</option>
            </select>
        </div>

        <div class="section-title">Dades de la Contrapart</div>
        <div class="input-group">
            <label>Nom de l'Adversari</label>
            <input type="text" name="adversari_nom" value="<?= htmlspecialchars($caso['adversari_nom']) ?>" required>
        </div>
        <div class="input-group">
            <label>Advocat de la Contrapart</label>
            <input type="text" name="adversari_advocat" value="<?= htmlspecialchars($caso['adversari_advocat']) ?>">
        </div>

        <div class="button-group">
            <button type="submit" class="btn-save">Guardar Canvis</button>
            <a href="dashboard.php" class="btn-back">Cancel·lar</a>
        </div>
    </form>
</div>

</body>
</html>
