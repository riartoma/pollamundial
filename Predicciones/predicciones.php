<?php
session_start();
$_SESSION['usuario_id'] = 2;

/* ================= BANDERAS ================= */
function bandera($e){
    $m=['Colombia'=>'co','Argentina'=>'ar','Brasil'=>'br','Uruguay'=>'uy',
        'Chile'=>'cl','Perú'=>'pe','Ecuador'=>'ec','Paraguay'=>'py',
        'Bolivia'=>'bo','Venezuela'=>'ve','México'=>'mx','Estados Unidos'=>'us',
        'Canadá'=>'ca','España'=>'es','Francia'=>'fr','Alemania'=>'de',
        'Italia'=>'it','Inglaterra'=>'gb','Portugal'=>'pt'];
    return $m[$e] ?? 'un';
}

/* ================= GRUPOS (EJEMPLO) ================= */
$grupos = [
 'A'=>['Brasil','Italia','Canadá','Camerún'],
 'B'=>['España','México','Japón','Irán'],
 'C'=>['Argentina','EEUU','Egipto','Arabia Saudita'],
 'D'=>['Francia','Colombia','Nigeria','Corea'],
 // ... hasta L
];

/* ================= FASES ================= */
$fases = [
    '32avos' => 16,
    'Octavos' => 8,
    'Cuartos' => 4,
    'Semifinal' => 2,
    'Final' => 1
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Torneo 48 Selecciones</title>
<style>
body{background:#f4f6f8;font-family:Arial;padding:30px}
.card{max-width:1200px;margin:auto;background:#fff;padding:25px;border-radius:10px}
h2,h3{text-align:center}
.grupo{border:2px solid #222;margin-bottom:30px;padding:15px;border-radius:8px}
.partido{display:grid;grid-template-columns:1fr 60px 60px 1fr;gap:10px;margin:6px 0}
.equipo{display:flex;align-items:center;gap:6px;font-weight:bold}
img{width:24px}
select,input{padding:6px}
.fase{border-top:4px solid #222;margin-top:40px;padding-top:20px}
button{margin-top:40px;padding:16px;width:100%;background:#111;color:#fff;font-size:18px;border:none}
</style>
</head>
<body>

<div class="card">
<h2>Predicción Torneo 48 Selecciones</h2>

<form method="POST" action="guardar_torneo.php">

<!-- ================= FASE DE GRUPOS ================= -->
<h3>FASE DE GRUPOS</h3>

<?php foreach($grupos as $grupo=>$equipos): ?>
<div class="grupo">
<h4>Grupo <?= $grupo ?></h4>

<?php for($i=0;$i<count($equipos);$i++):
      for($j=$i+1;$j<count($equipos);$j++): ?>
<div class="partido">
    <div class="equipo">
        <img src="https://flagcdn.com/w40/<?= bandera($equipos[$i]) ?>.png">
        <?= $equipos[$i] ?>
    </div>
    <input type="number" name="g[<?= $grupo ?>][<?= $equipos[$i] ?>][<?= $equipos[$j] ?>]" min="0">
    <input type="number" name="g[<?= $grupo ?>][<?= $equipos[$j] ?>][<?= $equipos[$i] ?>]" min="0">
    <div class="equipo">
        <img src="https://flagcdn.com/w40/<?= bandera($equipos[$j]) ?>.png">
        <?= $equipos[$j] ?>
    </div>
</div>
<?php endfor; endfor; ?>

<h4>Posiciones</h4>
<?php for($p=1;$p<=4;$p++): ?>
<select name="pos[<?= $grupo ?>][<?= $p ?>]" required>
    <option value="">Seleccione <?= $p ?>°</option>
    <?php foreach($equipos as $e): ?>
        <option value="<?= $e ?>"><?= $e ?></option>
    <?php endforeach; ?>
</select>
<?php endfor; ?>
</div>
<?php endforeach; ?>

<!-- ================= FASES FINALES ================= -->
<?php foreach($fases as $fase=>$partidos): ?>
<div class="fase">
<h3><?= $fase ?></h3>

<?php for($i=1;$i<=$partidos;$i++): ?>
<div class="partido">
<select name="<?= $fase ?>[<?= $i ?>][local]" required>
    <option value="">Equipo clasificado</option>
</select>
<input type="number" name="<?= $fase ?>[<?= $i ?>][g_local]">
<input type="number" name="<?= $fase ?>[<?= $i ?>][g_visit]">
<select name="<?= $fase ?>[<?= $i ?>][visitante]" required>
    <option value="">Equipo clasificado</option>
</select>
</div>
<?php endfor; ?>

</div>
<?php endforeach; ?>

<button type="submit">Guardar torneo completo</button>
</form>
</div>

</body>
</html>
