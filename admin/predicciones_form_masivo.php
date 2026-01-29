<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php?error=1");
    exit;
}

include '../config.php';
$pdo = conectarDB();

/* Mapa de banderas */
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

/* Traer TODOS los partidos */
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
<title>Predicciones del Torneo</title>

<style>
* { box-sizing:border-box; font-family:Arial, Helvetica, sans-serif; }
body { background:#f4f6f8; padding:40px; }
.card {
    max-width: 900px;
    margin: auto;
    background: #fff;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0,0,0,.1);
}
.partido {
    display: grid;
    grid-template-columns: 1fr auto 60px 60px 1fr;
    align-items: center;
    gap: 10px;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}
.equipo {
    display: flex;
    align-items: center;
    gap: 6px;
}
img { width: 26px; }
input[type=number] {
    width: 60px;
    padding: 6px;
    text-align: center;
}
button {
    margin-top: 25px;
    width: 100%;
    padding: 14px;
    background: #222;
    color: #fff;
    border: none;
    font-size: 16px;
}
button:hover { background:#000; }
h2 { text-align:center; }
</style>
</head>

<body>

<div class="card">
<h2>Predicciones – Todo el Torneo</h2>

<form method="POST" action="predicciones_guardar_masivo.php">

<?php foreach($partidos as $p): ?>
<div class="partido">

    <div class="equipo">
        <img src="https://flagcdn.com/w40/<?= banderaPorEquipo($p['local']) ?>.png">
        <?= htmlspecialchars($p['local']) ?>
    </div>

    <strong>VS</strong>

    <input type="number" name="pred[<?= $p['id'] ?>][local]" min="0" required>
    <input type="number" name="pred[<?= $p['id'] ?>][visitante]" min="0" required>

    <div class="equipo">
        <img src="https://flagcdn.com/w40/<?= banderaPorEquipo($p['visitante']) ?>.png">
        <?= htmlspecialchars($p['visitante']) ?>
    </div>

</div>
<?php endforeach; ?>

<button type="submit">Guardar TODAS las predicciones</button>

</form>
</div>

</body>
</html>
