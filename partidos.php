<?php
include 'config.php';
$pdo = conectarDB();

/* =====================
   GUARDAR / ACTUALIZAR
===================== */
if (isset($_POST['guardar'])) {
    $id     = $_POST['id'] ?: null;
    $local  = $_POST['equipo_local_id'];
    $visit  = $_POST['equipo_visitante_id'];
    $fecha  = $_POST['fecha'];

    if ($id) {
        $stmt = $pdo->prepare(
            "UPDATE partidos 
             SET equipo_local_id=?, equipo_visitante_id=?, fecha=? 
             WHERE id=?"
        );
        $stmt->execute([$local, $visit, $fecha, $id]);
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fecha) 
             VALUES (?, ?, ?)"
        );
        $stmt->execute([$local, $visit, $fecha]);
    }

    header("Location: partidos.php");
    exit;
}

/* ==========
   ELIMINAR
========== */
if (isset($_GET['eliminar'])) {
    $stmt = $pdo->prepare("DELETE FROM partidos WHERE id=?");
    $stmt->execute([$_GET['eliminar']]);
    header("Location: partidos.php");
    exit;
}

/* ==========
   EDITAR
========== */
$editar = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM partidos WHERE id=?");
    $stmt->execute([$_GET['editar']]);
    $editar = $stmt->fetch();
}

/* ==========
   EQUIPOS
========== */
$equipos = $pdo->query("SELECT id, nombre FROM equipos")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Gestión de Partidos</title>

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

select, input[type="date"] {
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

a {
    color: #38bdf8;
    text-decoration: none;
    font-weight: bold;
}

a:hover {
    text-decoration: underline;
}

.acciones a {
    margin: 0 5px;
}
</style>
</head>

<body>

<h1>⚽ Gestión de Partidos</h1>

<div class="card">
<h2><?= $editar ? "Editar Partido" : "Nuevo Partido" ?></h2>

<form method="POST">
    <input type="hidden" name="id" value="<?= $editar['id'] ?? '' ?>">

    <label>
        Equipo Local
        <select name="equipo_local_id" required>
            <?php foreach ($equipos as $e): ?>
                <option value="<?= $e['id'] ?>"
                    <?= isset($editar) && $editar['equipo_local_id']==$e['id'] ? 'selected' : '' ?>>
                    <?= $e['nombre'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>
        Equipo Visitante
        <select name="equipo_visitante_id" required>
            <?php foreach ($equipos as $e): ?>
                <option value="<?= $e['id'] ?>"
                    <?= isset($editar) && $editar['equipo_visitante_id']==$e['id'] ? 'selected' : '' ?>>
                    <?= $e['nombre'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>
        Fecha
        <input type="date" name="fecha" required value="<?= $editar['fecha'] ?? '' ?>">
    </label>

    <button type="submit" name="guardar">
        <?= $editar ? 'Actualizar' : 'Guardar' ?>
    </button>
</form>
</div>

<div class="card">
<h2>📋 Lista de Partidos</h2>

<table>
<tr>
    <th>ID</th>
    <th>Local</th>
    <th>Visitante</th>
    <th>Fecha</th>
    <th>Acciones</th>
</tr>

<?php
$stmt = $pdo->query(
    "SELECT p.id, el.nombre AS local, ev.nombre AS visitante, p.fecha
     FROM partidos p
     JOIN equipos el ON p.equipo_local_id = el.id
     JOIN equipos ev ON p.equipo_visitante_id = ev.id"
);

while ($row = $stmt->fetch()) {
    echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['local']}</td>
            <td>{$row['visitante']}</td>
            <td>{$row['fecha']}</td>
            <td class='acciones'>
                <a href='?editar={$row['id']}'>Editar</a> |
                <a href='?eliminar={$row['id']}' 
                   onclick='return confirm(\"¿Eliminar este partido?\");'>
                   Eliminar
                </a>
            </td>
          </tr>";
}
?>
</table>
</div>

</body>
</html>
