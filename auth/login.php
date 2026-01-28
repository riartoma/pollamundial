<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ingreso</title>
<style>
<?php include '../style.css'; ?>
</style>
</head>
<body>

<div class="card" style="max-width:400px;margin:auto">
<h2>Iniciar sesión</h2>

<form method="POST" action="procesar_login.php">
    <label>Email</label>
    <input type="email" name="email" required>

    <label>Contraseña</label>
    <input type="password" name="password" required>

    <button>Ingresar</button>
</form>

<p style="text-align:center">
<a href="registro.php">Registrarse</a> | 
<a href="recuperar.php">¿Olvidó su contraseña?</a>
</p>
</div>

</body>
</html>
