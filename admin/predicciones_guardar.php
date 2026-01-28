<?php
session_start();
include '../config.php';
$pdo = conectarDB();

$sql = "INSERT INTO predicciones
(usuario_id, partido_id, pred_local, pred_visitante)
VALUES (?,?,?,?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $_SESSION['usuario_id'],
    $_POST['partido_id'],
    $_POST['pred_local'],
    $_POST['pred_visitante']
]);

header("Location: ../partidos.php");
