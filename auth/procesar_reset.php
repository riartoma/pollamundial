<?php
session_start();
include '../config.php';
$pdo = conectarDB();

$id   = $_SESSION['reset_id'];
$pass = md5($_POST['password']);

$stmt = $pdo->prepare("UPDATE usuarios SET password=? WHERE id=?");
$stmt->execute([$pass,$id]);

session_destroy();
header("Location: login.php");
