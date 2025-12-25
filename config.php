<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');  // Cambia
define('DB_PASS', '');  // Cambia
define('DB_NAME', 'apuestas_torneo');

function conectarDB() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}
?>