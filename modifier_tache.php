<?php
require 'auth.php';
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $date_limite = $_POST['date_limite'];

    $stmt = $pdo->prepare("UPDATE taches SET titre = ?, description = ?, date_limite = ? WHERE id = ?");
    $stmt->execute([$titre, $description, $date_limite, $id]);
    header('Location: dashboard.php');
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM taches WHERE id = ?");
$stmt->execute([$id]);
$tache = $stmt->fetch();
?>

<form method="POST">
<link rel="stylesheet" href="css/style.css">
    <input type="hidden" name="id" value="<?= $tache['id'] ?>">
    <input type="text" name="titre" value="<?= $tache['titre'] ?>" required>
    <textarea name="description"><?= $tache['description'] ?></textarea>
    <input type="date" name="date_limite" value="<?= $tache['date_limite'] ?>" required>
    <button type="submit">Modifier</button>
</form>