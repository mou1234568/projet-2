<?php
$pdo = new PDO("mysql:host=localhost;dbname=gestion_taches;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
session_start();
?>