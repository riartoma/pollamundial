<?php
session_start();
include '../config.php';
$pdo = conectarDB();

$email = $_POST['email'];
$pass  = md5($_POST['password']);

$stmt = $pdo->prepare("SELECT id,nombre FROM usuarios WHERE email=? AND password=?");
$stmt->execute([$email,$pass]);
$user = $stmt->fetch();

if($user){
    $_SESSION['usuario_id'] = $user['id'];
    $_SESSION['nombre'] = $user['nombre'];
    header("Location: ../index.php");
} else {
    header("Location: login.php?error=1");
}
