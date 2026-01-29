<?php
include '../config.php';
$pdo = conectarDB();

$equipos = $pdo->query("SELECT id, nombre FROM equipos ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ingreso de Partidos</title>

<style>
* {
    box-sizing: border-box;
    font-family: Arial, Helvetica, sans-serif;
}
body {
    background: #f4f6f8;
    padding: 40px;
}
.card {
    max-width: 500px;
    margin: auto;
    background: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0,0,0,.1);
}
label {
    font-weight: bold;
}
select, input, button {
    width: 100%;
    padding: 10px;
    margin: 8px 0 15px;
}
button {
    background: #222;
    color: white;
    border: none;
}
button:hover {
    background: #000;
}
</style>
</head>

<body>
<div class="card">
<h2>Registrar Partido</h2>

<form method="POST" action="partidos_guardar.php">

<label>Equipo Local</label>
<select name="equipo_local_id" required>
    <option value="">Seleccione</option>
    <?php foreach($equipos as $e): ?>
        <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre']) ?></option>
    <?php endforeach; ?>
</select>

<label>Equipo Visitante</label>
<select name="equipo_visitante_id" required>
    <option value="">Seleccione</option>
    <?php foreach($equipos as $e): ?>
        <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre']) ?></option>
    <?php endforeach; ?>
</select>

<label>Fecha</label>
<input type="date" name="fecha" required>

<button type="submit">Guardar Partido</button>
</form>
</div>
</body>
</html>
