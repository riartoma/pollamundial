<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

include "config.php";
$pdo = conectarDB();

$usuario_id = $_SESSION['usuario_id'];

$sql = "SELECT data, creada_en FROM predicciones1 WHERE usuario_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$usuario_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    header("Location: prediccion_form.php");
    exit;
}

$data = json_decode($row['data'], true);

/* bandera */
function bandera($e){
    $m=['Colombia'=>'co','Argentina'=>'ar','Brasil'=>'br','Uruguay'=>'uy',
        'Chile'=>'cl','Perú'=>'pe','Ecuador'=>'ec','Paraguay'=>'py',
        'Bolivia'=>'bo','Venezuela'=>'ve','México'=>'mx','Estados Unidos'=>'us',
        'Canadá'=>'ca','España'=>'es','Francia'=>'fr','Alemania'=>'de',
        'Italia'=>'it','Inglaterra'=>'gb','Portugal'=>'pt'];
    return $m[$e] ?? 'un';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mi Predicción</title>
<style>
body{background:#f4f6f8;font-family:Arial;padding:30px}
.card{max-width:1100px;margin:auto;background:#fff;padding:25px;border-radius:10px}
h2,h3{text-align:center}
.bloque{border:2px solid #222;margin:25px 0;border-radius:8px}
.bloque h3{background:#222;color:#fff;margin:0;padding:10px;border-radius:6px 6px 0 0}
.partido{display:grid;grid-template-columns:1fr auto auto auto auto 1fr;gap:8px;padding:10px;border-bottom:1px solid #eee}
.equipo{display:flex;align-items:center;gap:6px;font-weight:bold}
img{width:22px}
</style>
</head>
<body>

<div class="card">
<h2>Mi Predicción Final</h2>
<p style="text-align:center;color:#555">
Registrada el <?= $row['creada_en'] ?>
</p>

<?php foreach($data as $fase=>$contenido): ?>
<div class="bloque">
<h3><?= strtoupper($fase) ?></h3>

<?php foreach($contenido as $partido=>$p): ?>
<div class="partido">
<div class="equipo">
    <img src="https://flagcdn.com/w40/<?= bandera($p['local']) ?>.png">
    <?= $p['local'] ?>
</div>
<strong><?= $p['g_local'] ?></strong>
<span>VS</span>
<strong><?= $p['g_visit'] ?></strong>
<div class="equipo">
    <img src="https://flagcdn.com/w40/<?= bandera($p['visitante']) ?>.png">
    <?= $p['visitante'] ?>
</div>
</div>
<?php endforeach; ?>

</div>
<?php endforeach; ?>

</div>
</body>
</html>
