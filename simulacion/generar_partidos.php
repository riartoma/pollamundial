    $partidos_generados = 0;
    for ($i = 0; $i < 4; $i++) {
        for ($j = $i + 1; $j < 4; $j++) {
            // Alternar local/visitante según ronda para equilibrar
            if ($partidos_generados % 2 == 0) {
                $local_id = $equipos[$i]['id'];
                $visitante_id = $equipos[$j]['id'];
                $local_nombre = $equipos[$i]['nombre'];
                $visitante_nombre = $equipos[$j]['nombre'];
            } else {
                $local_id = $equipos[$j]['id'];
                $visitante_id = $equipos[$i]['id'];
                $local_nombre = $equipos[$j]['nombre'];
                $visitante_nombre = $equipos[$i]['nombre'];
            }
            
            $fecha_str = $fecha->format('Y-m-d');
            
            $insert = $pdo->prepare("
                INSERT IGNORE INTO partidos (equipo_local_id, equipo_visitante_id, fecha)
                VALUES (?, ?, ?)
            ");
            $insert->execute([$local_id, $visitante_id, $fecha_str]);
            
            echo "$local_nombre vs $visitante_nombre - $fecha_str<br>";
            
            $partidos_generados++;
            $fecha->add($intervalo);
        }
    }
    
    echo "<p>Generados $partidos_generados partidos para el Grupo $grupo (correcto: 6).</p><hr>";