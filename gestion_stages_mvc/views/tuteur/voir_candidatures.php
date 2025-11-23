<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$tuteurId = $_SESSION ['id_utilisateur'] ?? null;

if (!isset($_GET['id'])) {
    echo "ID du thème manquant.";
    exit;
}

$id_theme = (int) $_GET['id'];

require_once './models/Theme.php';
require_once './models/Candidature.php';
require_once './models/Stagiaire.php';

$theme = Theme::getById($id_theme);
$candidatures = Candidature::getCandidaturesByTheme($id_theme);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Candidatures reçues</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card + .card { margin-top: 1rem; }
    </style>
</head>
<body class="bg-light p-4">

<div class="container bg-white p-4 rounded shadow">
    <h4>📌 Candidatures pour le thème : <strong><?= htmlspecialchars($theme['titre'] ?? '-') ?></strong></h4>

    <?php if (empty($candidatures)) : ?>
        <div class="alert alert-info mt-4">Aucune candidature pour ce thème.</div>
    <?php else : ?>
        <?php foreach ($candidatures as $candidature): ?>
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong><?= htmlspecialchars(($candidature['prenom'] ?? '-') . ' ' . ($candidature['nom'] ?? '-')) ?></strong>
                    <small><?= htmlspecialchars($candidature['date_candidature'] ?? '-') ?></small>
                </div>
                <div class="card-body">
    <p><strong>Université :</strong> <?= htmlspecialchars($candidature['universite']) ?></p>
    <p><strong>Niveau :</strong> <?= htmlspecialchars($candidature['niveau_etude']) ?></p>
    <p><strong>Téléphone :</strong> <?= htmlspecialchars($candidature['telephone']) ?></p>
    <p><strong>Expériences :</strong> <?= nl2br(htmlspecialchars($candidature['experiences'])) ?></p>
    <p><strong>Compétences :</strong> <?= nl2br(htmlspecialchars($candidature['competences'])) ?></p>
<p><strong>Message libre :</strong> <?= $candidature['message_libre'] ? nl2br(htmlspecialchars($candidature['message_libre'])) : '<em>Aucun message fourni</em>' ?></p>


</div>

                    <form method="POST" action="index.php?page=tuteur_candidatures" class="mt-3">
                        <input type="hidden" name="id_candidature" value="<?= (int)$candidature['id_candidature'] ?>">
                        <input type="hidden" name="id_theme" value="<?= (int)$id_theme ?>">
                        <div class="mb-2">
                            <label><strong>Réponse :</strong></label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="statut" value="acceptee" required>
                                <label class="form-check-label text-success">✅ Accepter</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="statut" value="refusee" required>
                                <label class="form-check-label text-danger">❌ Refuser</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Message au stagiaire (optionnel) :</label>
                            <textarea name="message_tuteur" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">📨 Envoyer la réponse</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="text-end mt-4">
        <a href="index.php?page=tuteur_candidatures" class="btn btn-secondary">⬅ Retour</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
