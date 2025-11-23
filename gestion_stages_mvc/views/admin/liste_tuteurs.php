<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if ($_SESSION['type'] !== 'admin') {
    header("Location: index.php?page=login");
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$db = Database::connect();

//jointure entre tuteur et utilisateur
$sql = "
    SELECT 
        u.nom, u.prenom, u.email, u.identifiant,
        t.catalogue, t.departement, t.telephone_professionnel
    FROM tuteur t
    INNER JOIN utilisateur u ON t.id_utilisateur = u.id_utilisateur
    ORDER BY u.nom ASC
";

$tuteurs = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des tuteurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">
<div class="container bg-white shadow rounded p-4">
    <h2 class="mb-4 text-primary">📋 Liste des tuteurs</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Identifiant</th>
                <th>Catalogue</th>
                <th>Département</th>
                <th>Téléphone</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($tuteurs) > 0): ?>
            <?php foreach ($tuteurs as $t): ?>
                <tr>
                    <td><?= htmlspecialchars($t['nom']) ?></td>
                    <td><?= htmlspecialchars($t['prenom']) ?></td>
                    <td><?= htmlspecialchars($t['email']) ?></td>
                    <td><?= htmlspecialchars($t['identifiant']) ?></td>
                    <td><?= htmlspecialchars($t['catalogue'] ?? '') ?></td>
                    <td><?= htmlspecialchars($t['departement'] ?? '') ?></td>
                    <td><?= htmlspecialchars($t['telephone_professionnel'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7" class="text-center">Aucun tuteur trouvé.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
