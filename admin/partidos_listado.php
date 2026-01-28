<?php
include '../config.php';
$pdo = conectarDB();

function banderaPorEquipo($nombre) {
    $mapa = [
        'Colombia'=>'co','Argentina'=>'ar','Brasil'=>'br','Uruguay'=>'uy',
        'Chile'=>'cl','Perú'=>'pe','Ecuador'=>'ec','Paraguay'=>'py',
        'Bolivia'=>'bo','Venezuela'=>'ve','México'=>'mx','Estados Unidos'=>'us',
        'Canadá'=>'ca','España'=>'es','Francia'=>'fr','Alemania'=>'de',
        'Italia'=>'it','Inglaterra'=>'gb','Portugal'=>'pt', 'Japón'=>'jp', 'Australia'=>'au'
        , 'Senegal'=>'sn', 'Egipto'=>'eg', 'Marruecos'=>'mr', 'Países Bajos'=>'cd', 'Bélgica'=>'bg'
        , 'Nigeria'=>'ng', 'Corea del Sur'=>'sc', 'Irán'=>'ir'
    ];
    return $mapa[$nombre] ?? 'un';
}

$sql = "
SELECT 
    p.id,
    p.fecha,
    el.nombre AS equipo_local,
    ev.nombre AS equipo_visitante
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
<title>Partidos</title>

<style>
body {
    font-family: Arial;
    background: #f4f6f8;
    padding: 40px;
}
.card {
    background: #fff;
    padding: 25px;
    border-radius: 8px;
    max-width: 800px;
    margin: auto;
}
.partido {
    display: grid;
    grid-template-columns: 1fr auto 1fr auto;
    align-items: center;
    gap: 10px;
    padding: 12px 0;
    border-bottom: 1px solid #eee;
}
.equipo {
    display: flex;
    align-items: center;
    gap: 8px;
}
img {
    width: 32px;
}
.fecha {
    font-size: 13px;
    color: #666;
}
</style>
</head>

<body>
<div class="card">
<h2>Listado de Partidos</h2>

<?php if(!$partidos): ?>
<p>No hay partidos registrados.</p>
<?php endif; ?>

<?php foreach($partidos as $p): ?>
<div class="partido">

    <div class="equipo">
        <img src="https://flagcdn.com/w40/<?= banderaPorEquipo($p['equipo_local']) ?>.png">
        <?= htmlspecialchars($p['equipo_local']) ?>
    </div>

    <strong>VS</strong>

    <div class="equipo">
        <img src="https://flagcdn.com/w40/<?= banderaPorEquipo($p['equipo_visitante']) ?>.png">
        <?= htmlspecialchars($p['equipo_visitante']) ?>
    </div>

    <div class="fecha">
        <?= htmlspecialchars($p['fecha']) ?>
    </div>

</div>
<?php endforeach; ?>

</div>
</body>
</html>
