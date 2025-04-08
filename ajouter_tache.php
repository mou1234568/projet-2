<?php
require 'auth.php';
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $date_limite = $_POST['date_limite'];
    $utilisateur_id = $_SESSION['utilisateur_id'];

    $stmt = $pdo->prepare("INSERT INTO taches (utilisateur_id, titre, description, date_limite) VALUES (?, ?, ?, ?)");
    $stmt->execute([$utilisateur_id, $titre, $description, $date_limite]);
    header('Location: dashboard.php');
}
?>

<form method="POST">
<link rel="stylesheet" href="css/style.css">
    <input type="text" name="titre" placeholder="Titre" required>
    <textarea name="description" placeholder="Description"></textarea>
    <input type="date" name="date_limite" required>
    <button type="submit">Ajouter</button>
</form>