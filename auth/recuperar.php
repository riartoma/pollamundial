<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Recuperar contraseña</title>
<style>
<?php include '../partidos.php'; ?>
</style>
</head>
<body>

<div class="card" style="max-width:400px;margin:auto">
<h2>Recuperar contraseña</h2>

<form method="POST" action="procesar_recuperar.php">
    <label>Email</label>
    <input type="email" name="email" required>
    <button>Enviar</button>
</form>
</div>

</body>
</html>
