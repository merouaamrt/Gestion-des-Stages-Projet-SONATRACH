<?php
if (!isset($theme)) {
    echo "<p class='text-danger'>⛔ Données du thème manquantes</p>";
    return;
}

function h($value, $default = 'Non renseigné') {
    return htmlspecialchars($value ?? $default);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails du thème</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container bg-white p-4 rounded shadow">
    <h3 class="mb-4">📄 Détails du thème</h3>

    <table class="table table-bordered">
        <tr><th>Titre</th><td><?= h($theme['titre']) ?></td></tr>
        <tr><th>Domaine</th><td><?= h($theme['domaine']) ?></td></tr>
        <tr><th>Description</th><td><?= nl2br(h($theme['description'])) ?></td></tr>
        <tr><th>Catalogue</th><td><?= h($theme['catalogue']) ?></td></tr>
        <tr><th>Statut</th><td><?= h($theme['statut']) ?></td></tr>
        <tr><th>Origine</th><td><?= h($theme['origine']) ?></td></tr>

        <?php if ($theme['origine'] === 'stagiaire') : ?>
            <tr><th colspan="2" class="table-secondary text-center">📄 Informations sur le stagiaire</th></tr>
            <tr><th>Université</th><td><?= h($theme['stagiaire_universite']) ?></td></tr>
            <tr><th>Niveau d'étude</th><td><?= h($theme['stagiaire_niveau']) ?></td></tr>
            <tr><th>Téléphone</th><td><?= h($theme['stagiaire_telephone']) ?></td></tr>
            <tr><th>Email</th><td><?= h($theme['stagiaire_email']) ?></td></tr>
            <tr><th>Compétences</th><td><?= nl2br(h($theme['stagiaire_competences'])) ?></td></tr>
            <tr><th>Expériences</th><td><?= nl2br(h($theme['stagiaire_experiences'])) ?></td></tr>
            <tr><th>Motivation</th><td><?= nl2br(h($theme['stagiaire_motivation'])) ?></td></tr>
            <tr><th>Proposé par</th><td><?= h($theme['prenom_stagiaire'] . ' ' . $theme['nom_stagiaire']) ?></td></tr>
        <?php else : ?>
            <tr><th>Proposé par</th><td><?= h($theme['prenom_tuteur'] . ' ' . $theme['nom_tuteur']) ?></td></tr>
        <?php endif; ?>

        <tr>
            <th>Date de proposition</th>
            <td><?= isset($theme['date_proposition']) ? date('d/m/Y H:i', strtotime($theme['date_proposition'])) : 'Non précisée' ?></td>
        </tr>
    </table>

    <?php if ($theme['origine'] === 'stagiaire' && !empty($theme['id_stagiaire'])) : ?>
        <div class="mt-4 border p-4 bg-light rounded">
            <h5>✅ Accepter ce thème</h5>
            <p>Cette action enverra une notification à ce stagiaire dans sa session.</p>
            <form method="post" action="index.php?page=repondre_stagiaire">
                <input type="hidden" name="theme_id" value="<?= (int)$theme['id_theme'] ?>">
                <input type="hidden" name="stagiaire_id" value="<?= (int)$theme['id_stagiaire'] ?>">
                <button type="submit" class="btn btn-success">✅ Confirmer l'acceptation</button>
            </form>
        </div>
    <?php endif; ?>

    <div class="text-end mt-4">
        <a href="index.php?page=tuteur_dashboard" class="btn btn-secondary">⬅ Retour au tableau de bord</a>
    </div>
</div>
</body>
</html>
