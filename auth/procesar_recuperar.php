<?php
session_start();
include '../config.php';
$pdo = conectarDB();

$email = $_POST['email'];

$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email=?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if($user){
    $_SESSION['reset_id'] = $user['id'];
    header("Location: reset.php");
} else {
    header("Location: recuperar.php?error=1");
}

