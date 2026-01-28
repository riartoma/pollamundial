<?php
include 'config.php';
$pdo = conectarDB();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Fase Eliminatoria - Simulación</title>
        <link rel="stylesheet" href="style.css">

    
</head>
<body>
    <h1>Simulación Completa de la Fase Eliminatoria</h1>

<?php
// ========================
// 1. CALCULAR CLASIFICACIÓN DE GRUPOS Y CLASIFICADOS
// ========================

$grupos = range('A', 'L');
$clasificados_directos = [];  // 1° y 2° de cada grupo
$terceros = [];               // Todos los terceros para seleccionar los 8 mejores

foreach ($grupos as $grupo) {
    // (Reutilizamos lógica similar a clasificaciones_grupos.php)
    $stmt = $pdo->prepare("SELECT id, nombre FROM equipos WHERE grupo = ?");
    $stmt->execute([$grupo]);
    $equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $estadisticas = [];
    foreach ($equipos as $e) {
        $estadisticas[$e['id']] = ['nombre' => $e['nombre'], 'pts' => 0, 'dg' => 0, 'gf' => 0, 'pj' => 0];
    }

    $stmt = $pdo->prepare("
        SELECT p.equipo_local_id, p.equipo_visitante_id, r.goles_local, r.goles_visitante
        FROM partidos p
        JOIN equipos el ON p.equipo_local_id = el.id
        JOIN equipos ev ON p.equipo_visitante_id = ev.id
        JOIN resultados_reales r ON p.id = r.partido_id
        WHERE el.grupo = ? 
    ");
    $stmt->execute([$grupo]);

    while ($p = $stmt->fetch()) {
        $l = $p['equipo_local_id']; $v = $p['equipo_visitante_id'];
        $gl = (int)$p['goles_local']; $gv = (int)$p['goles_visitante'];

        // Local
        $estadisticas[$l]['pj']++;
        $estadisticas[$l]['gf'] += $gl; $estadisticas[$l]['dg'] += $gl - $gv;
        if ($gl > $gv) $estadisticas[$l]['pts'] += 3;
        elseif ($gl == $gv) $estadisticas[$l]['pts'] += 1;

        // Visitante
        $estadisticas[$v]['pj']++;
        $estadisticas[$v]['gf'] += $gv; $estadisticas[$v]['dg'] += $gv - $gl;
        if ($gv > $gl) $estadisticas[$v]['pts'] += 3;
        elseif ($gv == $gl) $estadisticas[$v]['pts'] += 1;
    }

    // Ordenar grupo
    uasort($estadisticas, function($a, $b) {
        if ($b['pts'] != $a['pts']) return $b['pts'] - $a['pts'];
        if ($b['dg'] != $a['dg']) return $b['dg'] - $a['dg'];
        if ($b['gf'] != $a['gf']) return $b['gf'] - $a['gf'];
        return 0;
    });

    $pos = 1;
    foreach ($estadisticas as $id => $data) {
        if ($pos == 1) $clasificados_directos[] = ['equipo' => $data['nombre'], 'grupo' => $grupo, 'pos' => '1°'];
        if ($pos == 2) $clasificados_directos[] = ['equipo' => $data['nombre'], 'grupo' => $grupo, 'pos' => '2°'];
        if ($pos == 3) $terceros[] = ['equipo' => $data['nombre'], 'grupo' => $grupo, 'pts' => $data['pts'], 'dg' => $data['dg'], 'gf' => $data['gf']];
        $pos++;
    }
}

// Seleccionar los 8 mejores terceros
usort($terceros, function($a, $b) {
    if ($b['pts'] != $a['pts']) return $b['pts'] - $a['pts'];
    if ($b['dg'] != $a['dg']) return $b['dg'] - $a['dg'];
    if ($b['gf'] != $a['gf']) return $b['gf'] - $a['gf'];
    return 0;
});
$mejores_terceros = array_slice($terceros, 0, 8);

// Todos los clasificados a octavos (32)
$clasificados_octavos = array_merge($clasificados_directos, $mejores_terceros);
shuffle($clasificados_octavos); // Mezclar para cruces aleatorios pero realistas

echo "<div class='clasificados'>
        <h2>Equipos Clasificados a Octavos de Final (32)</h2>
        <strong>24 directos (1° y 2°)</strong> + <strong>8 mejores terceros</strong><br><br>";
foreach ($clasificados_octavos as $c) {
    $tipo = (isset($c['pos']) ? $c['pos'] . ' Grupo ' . $c['grupo'] : '3° Grupo ' . $c['grupo']);
    echo "<strong>{$c['equipo']}</strong> ($tipo) &nbsp; ";
}
echo "</div>";

// ========================
// 2. FUNCIÓN PARA SIMULAR PARTIDO ELIMINATORIO
// ========================
function simularPartidoEliminatorio($eq1, $eq2) {
    // Goles en 90 minutos
    $g1 = mt_rand(0, 4);
    $g2 = mt_rand(0, 4);

    // 30% probabilidad de prórroga si empate
    if ($g1 == $g2 && mt_rand(1,100) <= 30) {
        $extra1 = mt_rand(0, 2);
        $extra2 = mt_rand(0, 2);
        $g1 += $extra1;
        $g2 += $extra2;
    }

    // Si sigue empatado → penales
    if ($g1 == $g2) {
        $pen1 = mt_rand(3, 6);
        $pen2 = mt_rand(3, 6);
        $ganador = $pen1 > $pen2 ? $eq1 : $eq2;
        $resultado = "$eq1 $g1 - $g2 $eq2 (pen. $pen1-$pen2)";
    } else {
        $ganador = $g1 > $g2 ? $eq1 : $eq2;
        $resultado = "$eq1 $g1 - $g2 $eq2";
    }

    return ['ganador' => $ganador, 'resultado' => $resultado];
}

// ========================
// 3. SIMULAR TODAS LAS RONDAS
// ========================

$rondas = ['Octavos de final', 'Cuartos de final', 'Semifinales', 'Tercer puesto', 'Final'];
$equipos_actuales = array_column($clasificados_octavos, 'equipo'); // Solo nombres
$historial_rondas = [];

foreach ($rondas as $idx => $nombre_ronda) {
    $partidos_ronda = [];
    $ganadores = [];

    // Emparejar equipos
    for ($i = 0; $i < count($equipos_actuales); $i += 2) {
        if ($i + 1 >= count($equipos_actuales)) break;
        $eq1 = $equipos_actuales[$i];
        $eq2 = $equipos_actuales[$i + 1];

        $partido = simularPartidoEliminatorio($eq1, $eq2);
        $partidos_ronda[] = $partido;
        $ganadores[] = $partido['ganador'];
    }

    $historial_rondas[$nombre_ronda] = $partidos_ronda;
    $equipos_actuales = $ganadores;

    // Para el tercer puesto (solo 1 partido entre perdedores de semis)
    if ($nombre_ronda === 'Semifinales') {
        $perdedores_semis = [];
        foreach ($partidos_ronda as $p) {
            $gan = $p['ganador'];
            $perdedores_semis[] = ($gan === explode(' ', $p['resultado'])[0] ? explode(' ', $p['resultado'])[count(explode(' ', $p['resultado'])) - 1] : explode(' ', $p['resultado'])[0]);
        }
        // Simular tercer puesto
        $partido_tercer = simularPartidoEliminatorio($perdedores_semis[0], $perdedores_semis[1]);
        $historial_rondas['Tercer puesto'] = [$partido_tercer];
    }

    if ($nombre_ronda === 'Final') break;
}

// ========================
// 4. MOSTRAR EL BRACKET
// ========================

foreach ($historial_rondas as $ronda => $partidos) {
    echo "<div class='fase'><h2>$ronda</h2><div class='ronda'>";
    foreach ($partidos as $p) {
        echo "<div class='partido'>
                <div>{$p['resultado']}</div>
                <div class='ganador'>→ {$p['ganador']}</div>
              </div>";
    }
    echo "</div></div>";
}

// Campeón
$campeon = end($historial_rondas['Final'])['ganador'] ?? 'N/A';
echo "<h1 style='text-align:center; color:gold; background:#333; padding:20px; border-radius:10px;'>
        ¡CAMPEÓN: $campeon!
      </h1>";
?>

    <p style="text-align:center;">
        <a href="clasificaciones_grupos.php">← Volver a Clasificaciones de Grupos</a> |
        <a href="fase_eliminatoria.php">Volver a Simular</a>
    </p>
</body>
</html>