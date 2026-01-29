<?php
include '../config.php';
$pdo = conectarDB();

if($_POST['equipo_local_id'] == $_POST['equipo_visitante_id']){
    die("El equipo local y visitante no pueden ser iguales");
}

$sql = "INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fecha)
        VALUES (?,?,?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $_POST['equipo_local_id'],
    $_POST['equipo_visitante_id'],
    $_POST['fecha']
]);

header("Location: partidos_form.php");
