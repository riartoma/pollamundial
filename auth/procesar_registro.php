<?php
include '../config.php';
$pdo = conectarDB();

$nombre = $_POST['nombre'];
$email  = $_POST['email'];
$pass   = md5($_POST['password']);

$stmt = $pdo->prepare("INSERT INTO usuarios(nombre,email,password) VALUES (?,?,?)");
$stmt->execute([$nombre,$email,$pass]);

header("Location: login.php");

