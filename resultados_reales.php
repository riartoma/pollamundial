<?php
include 'config.php';
$pdo = conectarDB();

/* =========================
   GUARDAR / ACTUALIZAR RESULTADO REAL
========================= */
$mensaje = "";
if (isset($_POST['guardar'])) {
    $partido_id = $_POST['partido_id'];
    $g_local    = $_POST['goles_local'];
    $g_visit    = $_POST['goles_visitante'];

    $stmt = $pdo->prepare(
        "INSERT INTO resultados_reales (partido_id, goles_local, goles_visitante)
         VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE goles_local=?, goles_visitante=?"
    );
    $stmt->execute([
        $partido_id, $g_local, $g_visit,
        $g_local, $g_visit
    ]);

    $mensaje = "✅ Resultado real guardado correctamente";
}

/* ==========
   PARTIDOS
========== */
$partidos = $pdo->query(
    "SELECT p.id, el.nombre AS local, ev.nombre AS visitante
     FROM partidos p
     JOIN equipos el ON p.equipo_local_id = el.id
     JOIN equipos ev ON p.equipo_visitante_id = ev.id
     ORDER BY p.id"
)->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Resultados Reales</title>

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

form {
    display: grid;
    gap: 15px;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    align-items: end;
}

label {
    font-size: 14px;
}

select, input[type="number"] {
    width: 100%;
    padding: 8px;
    border-radius: 6px;
    border: none;
    margin-top: 5px;
}

button {
    background: #38bdf8;
    color: #0f172a;
    border: none;
    padding: 12px;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
}

button:hover {
    background: #0ea5e9;
}

.mensaje {
    text-align: center;
    margin-top: 10px;
    font-weight: bold;
    color: #22c55e;
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

<?php include 'menu.php'; ?>

<h1>Volver al index</a>
  ⚽ Resultados Reales  </h1>





<div class="card">
<h2>Agregar / Editar Resultado Real</h2>

<form method="POST">
    <label>
        Partido
        <select name="partido_id" required>
            <?php foreach ($partidos as $p): ?>
                <option value="<?= $p['id'] ?>">
                    <?= htmlspecialchars($p['local']) ?> vs <?= htmlspecialchars($p['visitante']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>
        Goles Local
        <input type="number" name="goles_local" min="0" value="0" required>
    </label>

    <label>
        Goles Visitante
        <input type="number" name="goles_visitante" min="0" value="0" required>
    </label>

    <button type="submit" name="guardar">Guardar Resultado</button>
</form>

<?php if (!empty($mensaje)): ?>
    <div class="mensaje"><?= $mensaje ?></div>
<?php endif; ?>
</div>

<div class="card">
<h2>📋 Resultados Reales Registrados</h2>

<table>
<tr>
    <th>Partido</th>
    <th>Resultado Real</th>
</tr>

<?php
$stmt = $pdo->query(
    "SELECT el.nombre AS local,
            ev.nombre AS visitante,
            r.goles_local,
            r.goles_visitante
     FROM resultados_reales r
     JOIN partidos p ON r.partido_id = p.id
     JOIN equipos el ON p.equipo_local_id = el.id
     JOIN equipos ev ON p.equipo_visitante_id = ev.id
     ORDER BY p.id"
);

while ($row = $stmt->fetch()) {
    echo "<tr>
            <td>" . htmlspecialchars($row['local']) . " vs " . htmlspecialchars($row['visitante']) . "</td>
            <td><strong>{$row['goles_local']} - {$row['goles_visitante']}</strong></td>
          </tr>";
}

/* Opcional: mostrar partidos sin resultado aún */
if ($stmt->rowCount() === 0) {
    echo "<tr><td colspan='2'>Aún no hay resultados reales registrados.</td></tr>";
}
?>
</table>
</div>

</body>
</html>