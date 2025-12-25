<?php
include 'config.php';

function calcularPuntos($pred_local, $pred_visit, $real_local, $real_visit) {
    if ($pred_local == $real_local && $pred_visit == $real_visit) return 5;

    $pred_ganador = $pred_local > $pred_visit ? 'local' : ($pred_local < $pred_visit ? 'visit' : 'empate');
    $real_ganador = $real_local > $real_visit ? 'local' : ($real_local < $real_visit ? 'visit' : 'empate');

    if ($pred_ganador != $real_ganador) return 0;
    if ($pred_ganador == 'empate') return 3;

    $pred_diff = abs($pred_local - $pred_visit);
    $real_diff = abs($real_local - $real_visit);
    $off = abs($pred_diff - $real_diff);

    if ($off == 0) return 4;
    if ($off == 1) return 3;
    if ($off == 2) return 2;
    return 1;
}

$pdo = conectarDB();

if (isset($_POST['calcular'])) {
    $pdo->query("DELETE FROM puntuaciones");
    $pdo->query("UPDATE usuarios SET puntos_total = 0");

    $stmt_real = $pdo->query("SELECT partido_id, goles_local, goles_visitante FROM resultados_reales");

    while ($real = $stmt_real->fetch()) {
        $stmt_pred = $pdo->prepare(
            "SELECT usuario_id, goles_local, goles_visitante
             FROM predicciones WHERE partido_id = ?"
        );
        $stmt_pred->execute([$real['partido_id']]);

        while ($pred = $stmt_pred->fetch()) {
            $puntos = calcularPuntos(
                $pred['goles_local'],
                $pred['goles_visitante'],
                $real['goles_local'],
                $real['goles_visitante']
            );

            $pdo->prepare(
                "INSERT INTO puntuaciones (usuario_id, partido_id, puntos)
                 VALUES (?, ?, ?)"
            )->execute([$pred['usuario_id'], $real['partido_id'], $puntos]);

            $pdo->prepare(
                "UPDATE usuarios SET puntos_total = puntos_total + ?
                 WHERE id = ?"
            )->execute([$puntos, $pred['usuario_id']]);
        }
    }

    $mensaje = "✅ Cálculo de puntos realizado correctamente";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Comparación por Usuario</title>

<style>
body {
    font-family: Calibri, Arial, sans-serif;
    background: linear-gradient(135deg, #1e293b, #0f172a);
    margin: 0;
    padding: 40px;
    color: #fff;
}

h1 {
    text-align: center;
    color: #38bdf8;
    margin-bottom: 30px;
}

.card {
    background: #1f2937;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 30px;
    box-shadow: 0 10px 20px rgba(0,0,0,.35);
}

.usuario {
    border-left: 6px solid #38bdf8;
}

h2 {
    color: #38bdf8;
    margin-top: 0;
}

button {
    background: #38bdf8;
    color: #0f172a;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
}

button:hover {
    background: #0ea5e9;
}

.mensaje {
    text-align: center;
    margin-top: 15px;
    color: #22c55e;
    font-weight: bold;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

th, td {
    padding: 10px;
    text-align: center;
}

th {
    background: #0f172a;
    color: #38bdf8;
}

tr:nth-child(even) {
    background: #111827;
}
</style>
</head>

<body>

<h1>👥 Comparación de Predicciones por Usuario</h1>

<div class="card" style="text-align:center;">
    <form method="POST">
        <button type="submit" name="calcular">Calcular Puntos</button>
    </form>

    <?php if (!empty($mensaje)): ?>
        <div class="mensaje"><?= $mensaje ?></div>
    <?php endif; ?>
</div>

<div class="card">
    <h2>🏆 Ranking General</h2>
    <table>
        <tr>
            <th>Posición</th>
            <th>Usuario</th>
            <th>Puntos</th>
        </tr>

        <?php
        $pos = 1;
        $stmt = $pdo->query(
            "SELECT nombre, puntos_total FROM usuarios ORDER BY puntos_total DESC"
        );
        while ($row = $stmt->fetch()) {
            echo "<tr>
                    <td>{$pos}</td>
                    <td>{$row['nombre']}</td>
                    <td><strong>{$row['puntos_total']}</strong></td>
                  </tr>";
            $pos++;
        }
        ?>
    </table>
</div>

<?php
$usuarios = $pdo->query(
    "SELECT id, nombre, puntos_total FROM usuarios ORDER BY puntos_total DESC"
);

while ($usuario = $usuarios->fetch()):
?>

<div class="card usuario">
    <h2>👤 <?= $usuario['nombre'] ?> — <?= $usuario['puntos_total'] ?> pts</h2>

    <table>
        <tr>
            <th>Partido</th>
            <th>Predicción</th>
            <th>Resultado Real</th>
            <th>Puntos</th>
        </tr>

        <?php
        $stmt = $pdo->prepare(
            "SELECT el.nombre AS local,
                    ev.nombre AS visitante,
                    pr.goles_local AS pred_l,
                    pr.goles_visitante AS pred_v,
                    r.goles_local AS real_l,
                    r.goles_visitante AS real_v,
                    pu.puntos
             FROM puntuaciones pu
             JOIN partidos p ON pu.partido_id = p.id
             JOIN equipos el ON p.equipo_local_id = el.id
             JOIN equipos ev ON p.equipo_visitante_id = ev.id
             JOIN predicciones pr ON pu.usuario_id = pr.usuario_id AND pu.partido_id = pr.partido_id
             JOIN resultados_reales r ON pu.partido_id = r.partido_id
             WHERE pu.usuario_id = ?"
        );
        $stmt->execute([$usuario['id']]);

        while ($row = $stmt->fetch()) {
            echo "<tr>
                    <td>{$row['local']} vs {$row['visitante']}</td>
                    <td>{$row['pred_l']} - {$row['pred_v']}</td>
                    <td>{$row['real_l']} - {$row['real_v']}</td>
                    <td><strong>{$row['puntos']}</strong></td>
                  </tr>";
        }
        ?>
    </table>
</div>

<?php endwhile; ?>



</body>
</html>
