<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // Vérification de l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $utilisateur = $stmt->fetch();

    if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
        // Stockage des infos utilisateur dans la session
        $_SESSION['utilisateur_id'] = $utilisateur['id'];
        $_SESSION['utilisateur_nom'] = $utilisateur['nom'];
        $_SESSION['utilisateur_prenom'] = $utilisateur['prenom'];
        $_SESSION['utilisateur_email'] = $utilisateur['email'];

        // Vérification des sessions
        //var_dump($_SESSION); die();

        header('Location: dashboard.php');
        exit;
    } else {
        $erreur = "Email ou mot de passe incorrect.";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Connexion</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Connexion</h1>
    <?php if (isset($erreur)): ?>
        <p style="color: red;"><?= $erreur ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
        <button type="submit">Se connecter</button>
    </form>
    <p>Pas encore inscrit ? <a href="inscription.php">Inscrivez-vous ici</a></p>
</body>
</html>