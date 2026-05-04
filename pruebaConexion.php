
<?php
// Incluimos la configuración que creamos antes
require_once 'configDB.php';

try {
    // Si la variable $pdo existe y no es nula, la conexión es correcta [cite: 629]
    if (isset($pdo)) {
        echo "<h1 style='color: green;'>✅ Connexió exitosa!</h1>";
        echo "<p>S'ha establert la connexió amb la base de dades <strong>" . DB_NAME . "</strong>.</p>";
        
        // Prueba adicional: obtener la versión de MySQL [cite: 2522]
        $stmt = $pdo->query("SELECT VERSION() as version");
        $row = $stmt->fetch();
        echo "Versió del servidor: " . $row['version'];
    }
} catch (Exception $e) {
    // Si algo falla, se mostrará el mensaje de error [cite: 693, 2405]
    echo "<h1 style='color: red;'>❌ Error de connexió</h1>";
    echo "Detalls: " . $e->getMessage();
}
?>
