<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php?error=1");
    exit;
}

include '../config.php';
$pdo = conectarDB();

/* banderas */
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

/* TODOS los partidos */
$sql = "
SELECT 
    p.id,
    p.fecha,
    el.nombre AS local,
    ev.nombre AS visitante
FROM partidos p
JOIN equipos el ON p.equipo_local_id = el.id
JOIN equipos ev ON p.equipo_visitante_id = ev.id
ORDER BY p.id
";
$datos = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

/* indexar por id */
$partidos = [];
foreach ($datos as $p) {
    $partidos[$p['id']] = $p;
}

/* ESTRUCTURA MANUAL DEL TORNEO */
$estructuraTorneo = [
    'Fase de Grupos' => [
        'Grupo A' => [1,2,3,4,5,6],
        'Grupo B' => [7,8,9,10,11,12],
        'Grupo C' => [13,14,15,16,17,18],
        'Grupo D' => [19,20,21,22,23,24]
    ],
    'Octavos de Final' => [
        'Octavos' => [25,26,27,28,29,30,31,32]
    ],
    'Cuartos de Final' => [
        'Cuartos' => [33,34,35,36]
    ],
    'Semifinales' => [
        'Semifinal' => [37,38]
    ],
    'Final' => [
        'Final' => [39]
    ]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Predicciones del Torneo</title>

<style>
*{box-sizing:border-box;font-family:Arial}
body{background:#f4f6f8;padding:40px}
.card{max-width:1000px;margin:auto;background:#fff;padding:25px;border-radius:8px}
.fase h3{background:#222;color:#fff;padding:10px;border-radius:4px}
.grupo h4{background:#eee;padding:8px;border-left:5px solid #222}
.partido{
    display:grid;
    grid-template-columns:1fr auto 60px 60px 1fr;
    gap:10px;align-items:center;
    padding:10px 0;border-bottom:1px solid #eee
}
.equipo{display:flex;align-items:center;gap:6px}
img{width:26px}
input{width:60px;text-align:center;padding:6px}
button{margin-top:30px;width:100%;padding:14px;background:#222;color:#fff;border:none;font-size:16px}
</style>
</head>

<body>
<div class="card">
<h2 style="text-align:center">Predicciones del Torneo</h2>

<form method="POST" action="predicciones_guardar_masivo.php">

<?php foreach($estructuraTorneo as $fase=>$grupos): ?>
<div class="fase">
<h3><?= $fase ?></h3>

<?php foreach($grupos as $grupo=>$ids): ?>
<div class="grupo">
<h4><?= $grupo ?></h4>

<?php foreach($ids as $id):
    if(!isset($partidos[$id])) continue;
    $p = $partidos[$id];
?>
<div class="partido">
    <div class="equipo">
        <img src="https://flagcdn.com/w40/<?= banderaPorEquipo($p['local']) ?>.png">
        <?= $p['local'] ?>
    </div>

    <strong>VS</strong>

    <input type="number" name="pred[<?= $id ?>][local]" min="0" required>
    <input type="number" name="pred[<?= $id ?>][visitante]" min="0" required>

    <div class="equipo">
        <img src="https://flagcdn.com/w40/<?= banderaPorEquipo($p['visitante']) ?>.png">
        <?= $p['visitante'] ?>
    </div>
</div>
<?php endforeach; ?>

</div>
<?php endforeach; ?>
</div>
<?php endforeach; ?>

<button type="submit">Guardar TODAS las predicciones</button>
</form>
</div>
</body>
</html>
