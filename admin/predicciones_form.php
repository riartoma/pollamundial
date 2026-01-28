<?php
session_start();
include '../config.php';
$pdo = conectarDB();

$partidos = $pdo->query("SELECT * FROM partidos ORDER BY fecha")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Predicciones</title>
<style>
<?php include '../partidos.php'; ?>
</style>
</head>
<body>

<div class="card" style="max-width:600px;margin:auto">
<h2>Ingresar Predicción</h2>

<form method="POST" action="predicciones_guardar.php">

<label>Partido</label>
<select name="partido_id" required>
<?php foreach($partidos as $p): ?>
<option value="<?= $p['id'] ?>">
<?= $p['equipo_local'] ?> vs <?= $p['equipo_visitante'] ?>
</option>
<?php endforeach; ?>
</select>

<label>Marcador Local</label>
<input type="number" name="pred_local" required>

<label>Marcador Visitante</label>
<input type="number" name="pred_visitante" required>

<button>Guardar Predicción</button>
</form>
</div>

</body>
</html>
