<?php
include 'config.php';
$pdo = conectarDB();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clasificaciones de Grupos</title>

    <style>
        body {
            font-family: Calibri, Arial, sans-serif;
            background: linear-gradient(135deg, #1e293b, #0f172a);
            margin: 0;
            padding: 40px;
            color: #fff;
        }

        h1, h2 {
            text-align: center;
            color: #38bdf8;
        }

        .card {
            background: #1f2937;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 20px rgba(0,0,0,.35);
        }

        .grupos {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 25px;
        }

        .grupo {
            background: #111827;
            border-radius: 10px;
            overflow: hidden;
        }

        .grupo h2 {
            background: #0f172a;
            margin: 0;
            padding: 12px;
            font-size: 1.4em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }

        th {
            background: #0f172a;
            color: #38bdf8;
            padding: 10px;
            font-size: 14px;
        }

        td {
            padding: 10px;
            text-align: center;
        }

        tr:nth-child(even) {
            background: #1e293b;
        }

        /* Colores de clasificación (adaptado al formato Mundial 2026: 1° y 2° clasifican directamente, mejores terceros también, pero aquí destacamos top 2) */
        tr:nth-child(1) td { background: #166534; font-weight: bold; } /* 1° clasificado */
        tr:nth-child(2) td { background: #374151; font-weight: bold; } /* 2° clasificado */
        tr:nth-child(3) td { background: #7f1d1d; } /* 3° (posible mejor tercero) */

        .pts {
            font-weight: bold;
            color: #38bdf8;
            font-size: 1.1em;
        }

        .equipo {
            text-align: left !important;
            padding-left: 15px;
        }

        .footer-links {
            text-align: center;
            margin-top: 40px;
            font-size: 1.1em;
        }

        .footer-links a {
            color: #38bdf8;
            text-decoration: none;
            margin: 0 15px;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<?php include 'menu.php'; ?>

<h1><a href="index.php" style="color:#fff; text-decoration:none;">Volver al index</a>
    ⚽ Clasificaciones de la Fase de Grupos</h1>

<div class="card">
    <div class="grupos">

<?php
$grupos = range('A', 'L');

foreach ($grupos as $grupo) {
    // Obtener todos los equipos del grupo
    $stmt = $pdo->prepare("SELECT id, nombre FROM equipos WHERE grupo = ? ORDER BY nombre");
    $stmt->execute([$grupo]);
    $equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Si no hay equipos en el grupo, saltar
    if (empty($equipos)) continue;

    // Inicializar estadísticas
    $clasificacion = [];
    foreach ($equipos as $eq) {
        $clasificacion[$eq['id']] = [
            'nombre' => $eq['nombre'],
            'pj' => 0, 'pg' => 0, 'pe' => 0, 'pp' => 0,
            'gf' => 0, 'gc' => 0, 'dg' => 0, 'pts' => 0
        ];
    }

    // Obtener todos los partidos del grupo (ya jugados con resultado real)
    $stmt = $pdo->prepare("
        SELECT p.equipo_local_id, p.equipo_visitante_id, r.goles_local, r.goles_visitante
        FROM partidos p
        JOIN equipos el ON p.equipo_local_id = el.id
        JOIN equipos ev ON p.equipo_visitante_id = ev.id
        LEFT JOIN resultados_reales r ON p.id = r.partido_id
        WHERE (el.grupo = ? OR ev.grupo = ?)
    ");
    $stmt->execute([$grupo, $grupo]);
    $partidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calcular estadísticas solo si hay resultado real
    foreach ($partidos as $part) {
        if ($part['goles_local'] === null || $part['goles_visitante'] === null) continue;

        $local_id = $part['equipo_local_id'];
        $visit_id = $part['equipo_visitante_id'];
        $gl = (int)$part['goles_local'];
        $gv = (int)$part['goles_visitante'];

        // Local
        $clasificacion[$local_id]['pj']++;
        $clasificacion[$local_id]['gf'] += $gl;
        $clasificacion[$local_id]['gc'] += $gv;
        if ($gl > $gv) {
            $clasificacion[$local_id]['pg']++;
            $clasificacion[$local_id]['pts'] += 3;
        } elseif ($gl == $gv) {
            $clasificacion[$local_id]['pe']++;
            $clasificacion[$local_id]['pts'] += 1;
        } else {
            $clasificacion[$local_id]['pp']++;
        }

        // Visitante
        $clasificacion[$visit_id]['pj']++;
        $clasificacion[$visit_id]['gf'] += $gv;
        $clasificacion[$visit_id]['gc'] += $gl;
        if ($gv > $gl) {
            $clasificacion[$visit_id]['pg']++;
            $clasificacion[$visit_id]['pts'] += 3;
        } elseif ($gv == $gl) {
            $clasificacion[$visit_id]['pe']++;
            $clasificacion[$visit_id]['pts'] += 1;
        } else {
            $clasificacion[$visit_id]['pp']++;
        }
    }

    // Calcular diferencia de goles
    foreach ($clasificacion as $id => &$data) {
        $data['dg'] = $data['gf'] - $data['gc'];
    }

    // Convertir a array indexado para ordenar
    $tabla = array_values($clasificacion);

    // Ordenar: puntos desc, luego diff goles desc, luego goles a favor desc, luego nombre
    usort($tabla, function($a, $b) {
        if ($b['pts'] != $a['pts']) return $b['pts'] - $a['pts'];
        if ($b['dg'] != $a['dg']) return $b['dg'] - $a['dg'];
        if ($b['gf'] != $a['gf']) return $b['gf'] - $a['gf'];
        return strcmp($a['nombre'], $b['nombre']);
    });

    // Mostrar tabla del grupo
    echo "<div class='grupo'>";
    echo "<h2>Grupo $grupo</h2>";
    echo "<table>
            <tr>
                <th>Pos</th>
                <th style='text-align:left;'>Equipo</th>
                <th>PJ</th>
                <th>PG</th>
                <th>PE</th>
                <th>PP</th>
                <th>GF</th>
                <th>GC</th>
                <th>DG</th>
                <th class='pts'>PTS</th>
            </tr>";

    if (empty($tabla)) {
        echo "<tr><td colspan='10'>No hay resultados registrados aún para este grupo.</td></tr>";
    } else {
        foreach ($tabla as $pos => $eq) {
            $posicion = $pos + 1;
            echo "<tr>
                    <td>$posicion</td>
                    <td class='equipo'>{$eq['nombre']}</td>
                    <td>{$eq['pj']}</td>
                    <td>{$eq['pg']}</td>
                    <td>{$eq['pe']}</td>
                    <td>{$eq['pp']}</td>
                    <td>{$eq['gf']}</td>
                    <td>{$eq['gc']}</td>
                    <td>{$eq['dg']}</td>
                    <td class='pts'>{$eq['pts']}</td>
                  </tr>";
        }
    }

    echo "</table></div>";
}
?>

    </div>

    <div class="footer-links">
        <a href="comparacion.php">Ver puntuaciones de usuarios</a> |
        <a href="resultados_reales.php">Editar resultados reales</a>
    </div>
</div>

</body>
</html>