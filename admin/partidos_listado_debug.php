<?php
include '../config.php';
$pdo = conectarDB();

$stmt = $pdo->query("SELECT * FROM partidos LIMIT 1");
$fila = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Debug Partidos</title>
<style>
body {
    font-family: Arial;
    background: #f4f6f8;
    padding: 40px;
}
.card {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    max-width: 600px;
    margin: auto;
}
pre {
    background: #eee;
    padding: 15px;
}
</style>
</head>
<body>

<div class="card">
<h2>DEBUG columnas de partidos</h2>

<?php if(!$fila): ?>
    <p>No hay datos en la tabla partidos.</p>
<?php else: ?>
    <pre><?php print_r($fila); ?></pre>
<?php endif; ?>
</div>

</body>
</html>
