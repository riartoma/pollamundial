<?php
include 'config.php';  // Tu archivo de conexión

$pdo = conectarDB();

// Definir cabezas de serie por grupo (equipos "fuertes" que tendrán más probabilidad de ganar)
// Usa los nombres exactos que insertaste antes
$cabezas_de_serie = [
    'A' => 'Argentina',
    'B' => 'Brasil',
    'C' => 'Francia',
    'D' => 'España',
    'E' => 'Inglaterra',
    'F' => 'Portugal',
    'G' => 'Bélgica',
    'H' => 'Italia',
    'I' => 'Dinamarca',
    'J' => 'Polonia',
    'K' => 'Noruega',
    'L' => 'Turquía'
];

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <title>Generar Resultados Simulados</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 40px auto; padding: 20px; background: #f9f9f9; }
        h1, h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background: #eee; }
        .ganador { font-weight: bold; color: green; }
        .empate { color: orange; }
    </style>
</head>
<body>
    <h1>Generando resultados simulados para todos los partidos</h1>";

$total_partidos = 0;
$insertados = 0;

// Primero obtenemos todos los partidos sin resultado real aún
$stmt = $pdo->query("
    SELECT p.id, el.nombre AS local, ev.nombre AS visitante, el.grupo
    FROM partidos p
    JOIN equipos el ON p.equipo_local_id = el.id
    JOIN equipos ev ON p.equipo_visitante_id = ev.id
    LEFT JOIN resultados_reales r ON p.id = r.partido_id
    WHERE r.partido_id IS NULL
    ORDER BY p.fecha, p.id
");

echo "<table>
        <tr><th>Partido</th><th>Resultado Simulado</th><th>Notas</th></tr>";

while ($partido = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $total_partidos++;
    $grupo = $partido['grupo'];
    $local = $partido['local'];
    $visitante = $partido['visitante'];
    
    // Determinar si cada equipo es cabeza de serie
    $local_fuerte = ($local === $cabezas_de_serie[$grupo]);
    $visitante_fuerte = ($visitante === $cabezas_de_serie[$grupo]);
    
    // Probabilidades base
    $prob_local_gana = 0.35;
    $prob_empate = 0.30;
    $prob_visitante_gana = 0.35;
    
    // Ajuste por fuerza
    if ($local_fuerte && !$visitante_fuerte) {
        $prob_local_gana += 0.15;
        $prob_visitante_gana -= 0.10;
        $prob_empate -= 0.05;
    } elseif ($visitante_fuerte && !$local_fuerte) {
        $prob_visitante_gana += 0.15;
        $prob_local_gana -= 0.10;
        $prob_empate -= 0.05;
    } elseif ($local_fuerte && $visitante_fuerte) {
        // Dos fuertes: más empate
        $prob_empate += 0.10;
        $prob_local_gana -= 0.05;
        $prob_visitante_gana -= 0.05;
    }
    
    // Normalizar (por si acaso)
    $total_prob = $prob_local_gana + $prob_empate + $prob_visitante_gana;
    $prob_local_gana /= $total_prob;
    $prob_empate /= $total_prob;
    
    $rand = mt_rand() / mt_getrandmax();
    
    if ($rand < $prob_local_gana) {
        // Local gana
        $goles_local = mt_rand(1, 4);
        $goles_visitante = mt_rand(0, min(3, $goles_local - 1));
        if ($goles_local == 1) $goles_visitante = 0; // 1-0 común
        $nota = "Victoria local";
        $clase = "ganador";
    } elseif ($rand < $prob_local_gana + $prob_empate) {
        // Empate
        $opciones_empate = [0,1,2];
        $goles = $opciones_empate[array_rand($opciones_empate)];
        $goles_local = $goles;
        $goles_visitante = $goles;
        $nota = "Empate";
        $clase = "empate";
    } else {
        // Visitante gana
        $goles_visitante = mt_rand(1, 4);
        $goles_local = mt_rand(0, min(3, $goles_visitante - 1));
        if ($goles_visitante == 1) $goles_local = 0;
        $nota = "Victoria visitante";
        $clase = "ganador";
    }
    
    // Pequeña probabilidad de goleada sorpresa (independiente de fuerza)
    if (mt_rand(1, 100) <= 5) { // 5% chance
        $goles_local = mt_rand(4, 5);
        $goles_visitante = mt_rand(0, 2);
        $nota .= " (goleada sorpresa)";
    }
    
    // Insertar en resultados_reales (usando UPSERT)
    $insert = $pdo->prepare("
        INSERT INTO resultados_reales (partido_id, goles_local, goles_visitante)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE goles_local = ?, goles_visitante = ?
    ");
    $insert->execute([
        $partido['id'], $goles_local, $goles_visitante,
        $goles_local, $goles_visitante
    ]);
    
    $insertados++;
    
    echo "<tr>
            <td>{$local} vs {$visitante}</td>
            <td class='$clase'>{$goles_local} - {$goles_visitante}</td>
            <td>{$nota}</td>
          </tr>";
}

echo "</table>
      <h2>¡Listo! Se generaron resultados simulados para $insertados de $total_partidos partidos.</h2>
      <p>Ahora puedes ir a <a href='comparacion.php'>comparacion.php</a> y pulsar \"Calcular para Todos\" para ver los puntos de cada usuario.</p>
      <p><strong>Nota:</strong> Si ya tenías algunos resultados reales, este script no los sobrescribe gracias al ON DUPLICATE KEY.</p>
</body>
</html>";
?>