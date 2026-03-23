<?php

try {
	$pdo = new PDO (
		"mysql:host=localhost;dbname=PHP_GBA;charset=utf8mb4",
		"admin_GBA",
		"Asdqwe123.",
     [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       
        PDO::ATTR_EMULATE_PREPARES   => false,                  
    ]);

   // echo "Connexió exitosa";

} catch (PDOException $e) {
    
    error_log("Error de conexión: " . $e->getMessage()); 
    die("No s'ha pogut connectar a la base de dades"); 
}
?>
