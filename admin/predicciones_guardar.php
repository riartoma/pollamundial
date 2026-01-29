<?php
session_start();
include '../config.php';
$pdo = conectarDB();

$usuario_id = $_SESSION['usuario_id'];
$partido_id = $_POST['partido_id'];
$local      = $_POST['pred_local'];
$visitante  = $_POST['pred_visitante'];

// Evitar doble predicción
$check = $pdo->prepare(
    "SELECT id FROM predicciones WHERE usuario_id=? AND partido_id=?"
);
$check->execute([$usuario_id, $partido_id]);

if($check->fetch()){
    die("Ya realizaste una predicción para este partido");
}

$sql = "INSERT INTO predicciones
(usuario_id, partido_id, pred_local, pred_visitante)
VALUES (?,?,?,?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $usuario_id,
    $partido_id,
    $local,
    $visitante
]);

header("Location: predicciones_form.php");
