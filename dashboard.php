<?php
require 'auth.php';
require 'db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$utilisateur_id = $_SESSION['utilisateur_id'];
$filtre = isset($_GET['filtre']) ? $_GET['filtre'] : 'toutes';
$recherche = isset($_GET['recherche']) ? $_GET['recherche'] : '';

// Gérer la modification du statut
if (isset($_POST['modifier_statut'])) {
    $tache_id = $_POST['tache_id'];
    $nouveau_statut = $_POST['nouveau_statut'];

    // Vérifier la longueur du statut
    if (strlen($nouveau_statut) > 50) {
        echo "Erreur : La longueur du statut dépasse la limite autorisée.";
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE taches SET statut = ? WHERE id = ? AND utilisateur_id = ?");
        $stmt->execute([$nouveau_statut, $tache_id, $utilisateur_id]);

        // Construire l'URL de redirection avec les paramètres de filtre et de recherche
        $query_string = http_build_query([
            'filtre' => $filtre,
            'recherche' => $recherche
        ]);
        header("Location: dashboard.php?" . $query_string);
        exit;
    } catch (PDOException $e) {
        echo "Erreur lors de la mise à jour du statut de la tâche : " . $e->getMessage();
    }
}

$conditions = [];
$params = [$utilisateur_id];

$conditions[] = "utilisateur_id = ?";

if ($filtre == 'a_venir') {
    $conditions[] = "date_limite > CURDATE()";
} elseif ($filtre == 'en_retard') {
    $conditions[] = "date_limite < CURDATE() AND statut = 'En attente''en cours'";
} elseif ($filtre == 'terminées') {
    $conditions[] = "statut = 'Terminée'";
}

if (!empty($recherche)) {
    $conditions[] = "(categorie LIKE ? OR commentaire LIKE ?)";
    $params[] = "%$recherche%";
    $params[] = "%$recherche%";
}

$query = "SELECT * FROM taches WHERE " . implode(" AND ", $conditions);
$stmt = $pdo->prepare($query);

// Vérifier que le nombre de paramètres correspond au nombre de tokens dans la requête
$token_count = substr_count($query, '?');
if (count($params) !== $token_count) {
    echo "Erreur : Le nombre de paramètres ne correspond pas au nombre de tokens dans la requête.";
    exit;
}

try {
    $stmt->execute($params);
    $taches = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des tâches : " . $e->getMessage();
    $taches = []; // Initialiser $taches à un tableau vide en cas d'erreur
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<h1>Mes Tâches</h1>
<a href="ajouter_tache.php">Ajouter une tâche</a>
<form method="GET">
    <select name="filtre">
        <option value="toutes" <?= $filtre == 'toutes' ? 'selected' : '' ?>>Toutes</option>
        <option value="a_venir" <?= $filtre == 'a_venir' ? 'selected' : '' ?>>À venir</option>
        <option value="en_retard" <?= $filtre == 'en_retard' ? 'selected' : '' ?>>En retard</option>
        <option value="terminées" <?= $filtre == 'terminées' ? 'selected' : '' ?>>Terminées</option>
    </select>
    <input type="text" name="recherche" placeholder="Rechercher par catégorie ou commentaire" value="<?= htmlspecialchars($recherche) ?>">
    <button type="submit">Filtrer</button>
</form>
<table>
<tr>
<th>Titre</th>
<th>Description</th>
<th>Date Limite</th>
<th>Statut</th>
<th>Actions</th>
</tr>
<?php foreach ($taches as $tache): ?>
<tr>
<td><?= $tache['titre'] ?></td>
<td><?= $tache['description'] ?></td>
<td><?= $tache['date_limite'] ?></td>
<td>
    <form method="POST" style="display:inline;">
        <input type="hidden" name="tache_id" value="<?= $tache['id'] ?>">
        <select name="nouveau_statut" onchange="this.form.submit()">
            <option value="En attente" <?= $tache['statut'] == 'En attente' ? 'selected' : '' ?>>En attente</option>
            <option value="En cours" <?= $tache['statut'] == 'En cours' ? 'selected' : '' ?>>En cours</option>
            <option value="Terminée" <?= $tache['statut'] == 'Terminée' ? 'selected' : '' ?>>Terminée</option>
        </select>
        <input type="hidden" name="modifier_statut" value="1">
    </form>
</td>
<td>
<a href="modifier_tache.php?id=<?= $tache['id'] ?>">Modifier</a>
<a href="supprimer_tache.php?id=<?= $tache['id'] ?>">Supprimer</a>
<a href="terminer_tache.php?id=<?= $tache['id'] ?>">Terminer</a>
</td>
</tr>
<?php endforeach; ?>
</table>
<a href="deconnexion.php">Se déconnecter</a>
</body>
</html>