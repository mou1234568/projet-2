<?php
require 'auth.php';
require 'db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM taches WHERE id = ?");
$stmt->execute([$id]);
header('Location: dashboard.php');
?>