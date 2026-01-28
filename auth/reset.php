<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nueva contraseña</title>
<style>
<?php include '../partidos.php'; ?>
</style>
</head>
<body>

<div class="card" style="max-width:400px;margin:auto">
<h2>Nueva contraseña</h2>

<form method="POST" action="procesar_reset.php">
    <input type="password" name="password" required>
    <button>Actualizar</button>
</form>
</div>

</body>
</html>
