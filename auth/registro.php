<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registro</title>
<style>
<?php include '../style.css'; ?>
</style>
</head>
<body>

<div class="card" style="max-width:400px;margin:auto">
<h2>Registro</h2>

<form method="POST" action="procesar_registro.php">
    <label>Nombre</label>
    <input type="text" name="nombre" required>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Contraseña</label>
    <input type="password" name="password" required>

    <button>Registrar</button>
</form>
</div>

</body>
</html>
