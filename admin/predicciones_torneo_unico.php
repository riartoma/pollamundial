<?php
/* ================= SESIÓN ================= */
session_start();
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['usuario_id'] = 2; // solo para pruebas
}

/* ================= CONEXIÓN DB ================= */
$host = "localhost";
$db   = "apuestas_torneo";
$user = "root";
$pass = "";
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    die("Error de conexión");
}

/* ================= BANDERAS ================= */
function bandera($equipo){
    $mapa = [
        'Colombia'=>'co','Argentina'=>'ar','Brasil'=>'br','Uruguay'=>'uy',
        'Chile'=>'cl','Perú'=>'pe','Ecuador'=>'ec','Paraguay'=>'py',
        'Bolivia'=>'bo','Venezuela'=>'ve','México'=>'mx','Estados Unidos'=>'us',
        'Canadá'=>'ca','España'=>'es','Francia'=>'fr','Alemania'=>'de',
        'Italia'=>'it','Inglaterra'=>'gb','Portugal'=>'pt'
    ];
    return $mapa[$equipo] ?? 'un';
}

/* ================= PARTIDOS ================= */
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

$rows = $pdo->query($sql)->fetchAll();
$partidos = [];
foreach($rows as $r){ $partidos[$r['id']] = $r; }

/* ================= ESTRUCTURA TORNEO ================= */
$estructura = [
    'Fase de Grupos' => [
        'Grupo A' => [1,2,3,4,5,6],
        'Grupo B' => [7,8,9,10,11,12],
        'Grupo C' => [13,14,15,16,17,18],
        'Grupo D' => [19,20,21,22,23,24],
    ],
    'Octavos de Final' => [
        'Octavos' => [25,26,27,28,29,30,31,32]
    ],
    'Cuartos de Final' => [
        'Cuartos' => [33,34,35,36]
    ],
    'Semifinales' => [
        'Semifinales' => [37,38]
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
*{box-sizing:border-box;font-family:Arial,Helvetica,sans-serif}
body{background:#f4f6f8;padding:30px}
.container{
    max-width:1100px;
    margin:auto;
    background:#fff;
    padding:25px;
    border-radius:10px;
    box-shadow:0 8px 20px rgba(0,0,0,.12)
}
h2{text-align:center;margin-bottom:30px}

.fase{
    margin-bottom:40px;
    border:2px solid #111;
    border-radius:10px;
}
.fase h3{
    background:#111;
    color:#fff;
    padding:12px;
    margin:0;
    border-radius:8px 8px 0 0;
    text-align:center;
}

.grupo{
    background:#f9fafb;
    margin:20px;
    padding:15px;
    border-left:6px solid #111;
    border-radius:6px;
}
.grupo h4{margin-bottom:15px}

.partido{
    display:grid;
    grid-template-columns:1fr auto 60px 60px auto 1fr;
    gap:10px;
    align-items:center;
    padding:10px;
    margin-bottom:8px;
    background:#fff;
    border-radius:6px;
    border:1px solid #ddd;
}
.partido:nth-child(even){background:#f1f1f1}

.equipo{
    display:flex;
    align-items:center;
    gap:8px;
    font-weight:bold;
}

.vs{font-weight:bold;color:#555}

img{width:26px}

input{
    width:60px;
    padding:6px;
    text-align:center;
}

button{
    margin-top:30px;
    width:100%;
    padding:16px;
    font-size:18px;
    font-weight:bold;
    background:#111;
    color:#fff;
    border:none;
    border-radius:8px;
    cursor:pointer;
}
button:hover{background:#000}
</style>
</head>

<body>
<div class="container">
<h2>Predicciones del Torneo</h2>

<form method="POST" action="predicciones_guardar_masivo.php">

<?php foreach($estructura as $fase=>$grupos): ?>
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
        <img src="https://flagcdn.com/w40/<?= bandera($p['local']) ?>.png">
        <?= $p['local'] ?>
    </div>

    <div class="vs">VS</div>

    <input type="number" name="pred[<?= $id ?>][local]" min="0" required>
    <input type="number" name="pred[<?= $id ?>][visitante]" min="0" required>

    <div class="vs">VS</div>

    <div class="equipo">
        <img src="https://flagcdn.com/w40/<?= bandera($p['visitante']) ?>.png">
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
