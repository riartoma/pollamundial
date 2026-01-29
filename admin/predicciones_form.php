<?php
session_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ahora puedes verificar si la sesión está activa y usar sus variables
if (session_status() === PHP_SESSION_ACTIVE) {
    $_SESSION['usuario_id'] = 2; 
    echo "Soy el usuario: "; 
    echo $_SESSION['usuario_id'];
    
    // Puedes acceder a las variables de sesión aquí, por ejemplo:
    // $_SESSION['usuario'] = 'Juan';
} else {
    echo "sesión inactiva";
    header("Location: login.php?error=1");

}
include '../config.php';
$pdo = conectarDB();

function banderaPorEquipo($nombre) {
    $mapa = [
        'Colombia'=>'co','Argentina'=>'ar','Brasil'=>'br','Uruguay'=>'uy',
        'Chile'=>'cl','Perú'=>'pe','Ecuador'=>'ec','Paraguay'=>'py',
        'Bolivia'=>'bo','Venezuela'=>'ve','México'=>'mx','Estados Unidos'=>'us',
        'Canadá'=>'ca','España'=>'es','Francia'=>'fr','Alemania'=>'de',
        'Italia'=>'it','Inglaterra'=>'gb','Portugal'=>'pt'
    ];
    return $mapa[$nombre] ?? 'un';
}

$sql = "
SELECT 
    p.id,
    p.fecha,
    el.nombre AS local,
    ev.nombre AS visitante
FROM partidos p
JOIN equipos el ON p.equipo_local_id = el.id
JOIN equipos ev ON p.equipo_visitante_id = ev.id
ORDER BY p.fecha
";

$partidos = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Predicciones</title>

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
    max-width: 600px;
    margin: auto;
    background: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0,0,0,.1);
}
label {
    font-weight: bold;
    display: block;
}
select, input, button {
    width: 100%;
    padding: 10px;
    margin: 8px 0 15px;
}
button {
    background: #222;
    color: #fff;
    border: none;
}
.partido-option {
    display: flex;
    gap: 5px;
}
img {
    width: 20px;
    vertical-align: middle;
}
</style>

</head>
<body>

<div class="card">
<h2>Ingresar Predicción</h2>

<form method="POST" action="predicciones_guardar.php">

<label>Partido</label>
<select name="partido_id" required>
    <option value="">Seleccione un partido</option>
    <?php foreach($partidos as $p): ?>
        <option value="<?= $p['id'] ?>">
            <?= $p['local'] ?> vs <?= $p['visitante'] ?> (<?= $p['fecha'] ?>)
        </option>
    <?php endforeach; ?>
</select>

<label>Marcador Local</label>
<input type="number" name="pred_local" min="0" required>

<label>Marcador Visitante</label>
<input type="number" name="pred_visitante" min="0" required>

<button type="submit">Guardar Predicción</button>

</form>
</div>

</body>
</html>
