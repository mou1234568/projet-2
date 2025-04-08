<?php
require 'auth.php';
require 'db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("UPDATE taches SET statut = 'Terminée' WHERE id = ?");
$stmt->execute([$id]);
header('Location: dashboard.php');
?>