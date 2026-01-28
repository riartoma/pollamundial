<?php
include 'config.php';
session_start();

/* Usuario simulado (luego puedes quitarlo y usar login real) */
$_SESSION['usuario_id'] = 2;



if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ahora puedes verificar si la sesión está activa y usar sus variables
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "La sesión está activa.";
    // Puedes acceder a las variables de sesión aquí, por ejemplo:
    // $_SESSION['usuario'] = 'Juan';
} else {
    echo "La sesión no está activa o está deshabilitada.";
}

$pdo = conectarDB();

/* =========================
   GUARDAR / ACTUALIZAR
========================= */
if (isset($_POST['guardar'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $partido_id = $_POST['partido_id'];
    $g_local    = $_POST['goles_local'];
    $g_visit    = $_POST['goles_visitante'];

    $stmt = $pdo->prepare(
        "INSERT INTO predicciones (usuario_id, partido_id, goles_local, goles_visitante)
         VALUES (?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE goles_local=?, goles_visitante=?"
    );
    $stmt->execute([
        $usuario_id, $partido_id, $g_local, $g_visit,
        $g_local, $g_visit
    ]);

    $mensaje = "✅ Predicción guardada correctamente";
}

/* ==========
   PARTIDOS
========== */
$partidos = $pdo->query(
    "SELECT p.id, el.nombre AS local, ev.nombre AS visitante
     FROM partidos p
     JOIN equipos el ON p.equipo_local_id = el.id
     JOIN equipos ev ON p.equipo_visitante_id = ev.id"
)->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mis Predicciones</title>

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

<h1>🎯 Mis Predicciones</h1>

<div class="card">
<h2>Agregar / Editar Predicción</h2>

<form method="POST">
    <label>
        Partido
        <select name="partido_id" required>
            <?php foreach ($partidos as $p): ?>
                <option value="<?= $p['id'] ?>">
                    <?= $p['local'] ?> vs <?= $p['visitante'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>
        Goles Local
        <input type="number" name="goles_local" min="0" required>
    </label>

    <label>
        Goles Visitante
        <input type="number" name="goles_visitante" min="0" required>
    </label>

    <button type="submit" name="guardar">Guardar</button>
</form>

<?php if (!empty($mensaje)): ?>
    <div class="mensaje"><?= $mensaje ?></div>
<?php endif; ?>
</div>

<div class="card">
<h2>📋 Mis Predicciones Registradas</h2>

<table>
<tr>
    <th>Partido</th>
    <th>Predicción</th>
</tr>

<?php
$usuario_id = $_SESSION['usuario_id'];
$stmt = $pdo->prepare(
    "SELECT el.nombre AS local,
            ev.nombre AS visitante,
            pr.goles_local,
            pr.goles_visitante
     FROM predicciones pr
     JOIN partidos p ON pr.partido_id = p.id
     JOIN equipos el ON p.equipo_local_id = el.id
     JOIN equipos ev ON p.equipo_visitante_id = ev.id
     WHERE pr.usuario_id = ?"
);
$stmt->execute([$usuario_id]);

while ($row = $stmt->fetch()) {
    echo "<tr>
            <td>{$row['local']} vs {$row['visitante']}</td>
            <td><strong>{$row['goles_local']} - {$row['goles_visitante']}</strong></td>
          </tr>";
}
?>
</table>
</div>

</body>
</html>
