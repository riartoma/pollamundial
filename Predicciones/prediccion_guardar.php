<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
include "config.php";
$pdo = conectarDB();

/* Verificar si ya existe */
$check = $pdo->prepare("SELECT id from predicciones1 WHERE usuario_id = ?");
$check->execute([$usuario_id]);

if ($check->fetch()) {
    die("Ya tienes una predicción registrada.");
}

/* Guardar todo el POST */
$data = json_encode($_POST, JSON_UNESCAPED_UNICODE);

$sql = "INSERT INTO predicciones1 (usuario_id, data) VALUES (?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$usuario_id, $data]);

header("Location: prediccion_ver.php");
exit;
