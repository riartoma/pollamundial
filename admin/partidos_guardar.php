<?php
include '../config.php';
$pdo = conectarDB();

$sql = "INSERT INTO partidos
(equipo_local, bandera_local, equipo_visitante, bandera_visitante, fecha, hora)
VALUES (?,?,?,?,?,?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $_POST['local'],
    $_POST['bandera_local'],
    $_POST['visitante'],
    $_POST['bandera_visitante'],
    $_POST['fecha'],
    $_POST['hora']
]);

header("Location: partidos_form.php");
