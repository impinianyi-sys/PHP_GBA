<?php
session_start();
require_once '../../configDB.php';

if (!isset($_SESSION['usuari_id']) || $_SESSION['rol'] !== 'abogado') {
    header('Location: login.php');
    exit;
}

$error = "";
$success = "";

// Generar número de expediente automático: GBA-2026-XXXX
$num_auto = "GBA-" . date("Y") . "-" . rand(1000, 9999);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recollida de dades
    $titol = $_POST['titulo'];
    $expedient = $_POST['num_expedient'];
    $area = ($_POST['area_dret'] === 'Altres') ? $_POST['area_altres'] : $_POST['area_dret'];
    $data_obertura = $_POST['data_obertura'];
    $estat = $_POST['estado'];
    
    // Dades Client
    $client_id = $_POST['cliente_id']; // ID de l'usuari client ja registrat
    $dni_tipus = $_POST['client_dni_tipus'];
    $dni_num = $_POST['client_dni_num'];
    $rol_client = ($_POST['client_rol'] === 'Altres') ? $_POST['rol_altres'] : $_POST['client_rol'];
    
    // Contrapart
    $adversari = $_POST['adversari_nom'];
    $adversari_advocat = $_POST['adversari_advocat'] ?? null;

    try {
        $sql = "INSERT INTO casos (num_expedient, titulo, area_dret, fecha_creacion, estado, abogado_id, cliente_id, client_dni_tipus, client_dni_num, client_rol, adversari_nom, adversari_advocat) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $expedient, $titol, $area, $data_obertura, $estat, 
            $_SESSION['usuari_id'], $client_id, $dni_tipus, $dni_num, $rol_client, 
            $adversari, $adversari_advocat
        ]);
        $success = "Cas registrat correctament amb expedient $expedient";
    } catch (PDOException $e) {
        $error = "Error al registrar: " . $e->getMessage();
    }
}

// Obtenir llista de clients per al desplegable
$clients = $pdo->query("SELECT id, nombre FROM usuarios WHERE rol = 'cliente'")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>GBA | Nou Expedient</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root { --porsche-blue: #004a99; --negro: #0a0a0a; --blanco: #ffffff; }
        body { font-family: 'Inter', sans-serif; background: var(--negro); color: var(--blanco); padding: 40px; }
        .form-container { max-width: 900px; margin: 0 auto; background: #111; padding: 50px; border: 1px solid #222; }
        h2 { font-weight: 300; letter-spacing: 5px; text-transform: uppercase; margin-bottom: 40px; color: var(--porsche-blue); }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .section-title { grid-column: span 2; font-size: 0.7rem; letter-spacing: 3px; color: #555; text-transform: uppercase; border-bottom: 1px solid #222; padding-bottom: 10px; margin-top: 20px; }
        .input-group { display: flex; flex-direction: column; }
        label { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px; color: #888; }
        input, select, textarea { background: transparent; border: none; border-bottom: 1px solid #333; padding: 10px 0; color: white; outline: none; transition: 0.3s; font-size: 0.9rem; }
        input:focus, select:focus { border-bottom-color: var(--porsche-blue); }
        
        /* Botons */
        .button-group { grid-column: span 2; display: flex; gap: 20px; margin-top: 30px; }
        .btn-save { flex: 2; background: white; color: black; border: none; padding: 20px; font-weight: 700; text-transform: uppercase; letter-spacing: 3px; cursor: pointer; transition: 0.3s; }
        .btn-save:hover { background: var(--porsche-blue); color: white; }
        
        .btn-back { flex: 1; display: flex; align-items: center; justify-content: center; background: transparent; color: white; border: 1px solid #444; text-decoration: none; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; font-size: 0.7rem; transition: 0.3s; }
        .btn-back:hover { border-color: var(--blanco); background: #222; }

        .other-input { display: none; margin-top: 10px; }
        .alert { grid-column: span 2; padding: 20px; margin-bottom: 20px; font-size: 0.9rem; border: 1px solid; }
        .success { background: rgba(0, 74, 153, 0.1); color: #4da3ff; border-color: var(--porsche-blue); text-align: center; }
        .error { background: rgba(255, 0, 0, 0.1); color: #ff4d4d; border-color: #ff4d4d; }
        
        .success a { color: white; font-weight: 700; text-decoration: underline; margin-left: 10px; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Registre d'Expedient</h2>

    <?php if($success): ?>
        <div class="alert success">
            <?= $success ?> | <a href="dashboard.php">TORNAR AL PANELL</a>
        </div>
    <?php endif; ?>

    <?php if($error): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="grid">
        <!-- PART 1: DADES DEL CAS -->
        <div class="section-title">Informació del Procediment</div>
        <div class="input-group">
            <label>Títol del Cas</label>
            <input type="text" name="titulo" required>
        </div>
        <div class="input-group">
            <label>Num. Expedient Intern</label>
            <input type="text" name="num_expedient" value="<?= $num_auto ?>" readonly>
        </div>
        <div class="input-group">
            <label>Àrea del Dret</label>
            <select name="area_dret" onchange="toggleOther(this, 'area_altres_input')">
                <option value="Civil">Civil</option>
                <option value="Penal">Penal</option>
                <option value="Laboral">Laboral</option>
                <option value="Mercantil">Mercantil</option>
                <option value="Família">Família</option>
                <option value="Altres">Altres...</option>
            </select>
            <input type="text" name="area_altres" id="area_altres_input" class="other-input" placeholder="Especifiqueu àrea">
        </div>
        <div class="input-group">
            <label>Data d'Obertura</label>
            <input type="date" name="data_obertura" value="<?= date('Y-m-d') ?>">
        </div>
        <div class="input-group">
            <label>Estat Inicial</label>
            <select name="estado">
                <option value="Prospecte">Prospecte</option>
                <option value="En negociació">En negociació</option>
                <option value="Judicialitzat">Judicialitzat</option>
            </select>
        </div>

        <!-- PART 2: DADES DEL CLIENT -->
        <div class="section-title">Identificació del Client</div>
        <div class="input-group">
            <label>Seleccionar Client (Base de dades)</label>
            <select name="cliente_id" required>
                <?php foreach($clients as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= $c['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="input-group">
            <label>Tipus Identificació</label>
            <select name="client_dni_tipus">
                <option value="DNI">DNI</option>
                <option value="NIE">NIE</option>
                <option value="Passaport">Passaport</option>
            </select>
        </div>
        <div class="input-group">
            <label>Número Identificació</label>
            <input type="text" name="client_dni_num" required>
        </div>
        <div class="input-group">
            <label>Rol en el procediment</label>
            <select name="client_rol" onchange="toggleOther(this, 'rol_altres_input')">
                <option value="Demandant">Demandant</option>
                <option value="Demandat">Demandat</option>
                <option value="Recurrent">Recurrent</option>
                <option value="Altres">Altres...</option>
            </select>
            <input type="text" name="rol_altres" id="rol_altres_input" class="other-input" placeholder="Especifiqueu rol">
        </div>

        <!-- PART 3: CONTRAPART -->
        <div class="section-title">Dades de la Contrapart</div>
        <div class="input-group">
            <label>Nom de l'Adversari</label>
            <input type="text" name="adversari_nom" required>
        </div>
        <div class="input-group">
            <label>Advocat Contrapart (Opcional)</label>
            <input type="text" name="adversari_advocat">
        </div>

        <!-- PART 4: DADES ADVOCAT -->
        <div class="section-title">Advocat Responsable (Auto)</div>
        <div class="input-group">
            <label>Nom Complet</label>
            <input type="text" value="<?= $_SESSION['nom_usuari'] ?>" readonly>
        </div>
        <div class="input-group">
            <label>ID Professional</label>
            <input type="text" value="REF-<?= $_SESSION['usuari_id'] ?>" readonly>
        </div>

        <div class="button-group">
            <button type="submit" class="btn-save">Registrar Expedient</button>
            <a href="dashboard.php" class="btn-back">Tornar al Panell</a>
        </div>
    </form>
</div>

<script>
function toggleOther(select, inputId) {
    const input = document.getElementById(inputId);
    input.style.display = (select.value === 'Altres') ? 'block' : 'none';
}
</script>

</body>
</html>
