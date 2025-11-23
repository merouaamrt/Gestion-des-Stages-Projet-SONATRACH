<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'models/Theme.php';
require_once 'models/Stagiaire.php';

$id_utilisateur = $_SESSION['id_utilisateur'] ?? null;

if (!Stagiaire::profilComplet($id_utilisateur)) {
    header("Location: index.php?page=completer_profil");
    exit;
}

$q = $_GET['q'] ?? '';
$themes = !empty($q) ? Theme::rechercherAvecPrefixe($q) : Theme::getAllPourStagiaire();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>📋 Thèmes disponibles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-title { font-weight: 600; font-size: 1.2rem; }
        .btn-toggle { font-size: 0.9rem; }
        .hint-box { font-size: 0.9rem; }
    </style>
    <script>
        function toggleDetails(id) {
            const detail = document.getElementById('detail-' + id);
            detail.classList.toggle('d-none');
        }
    </script>
</head>
<body class="bg-light p-4">

<div class="container">
    <h2 class="mb-4 text-center">📋 Liste des thèmes disponibles</h2>
    <form method="get" action="index.php" class="mb-3">
        <input type="hidden" name="page" value="themes">
        <input type="text" name="q" class="form-control" placeholder="🔍 Recherche : d:informatique ou c:hydrocarbures" value="<?= htmlspecialchars($q) ?>">
    </form> 
    <div class="alert alert-info hint-box">
        <strong>💡 Astuce de recherche :</strong><br>
        Utilisez des préfixes pour affiner :
        <ul class="mb-1">
            <li><code>c:hydrocarbures</code> — par <strong>catalogue thématique</strong></li>
            <li><code>d:informatique</code> — par <strong>domaine</strong></li>
        </ul>
        <span class="text-muted small">Autres exemples : c:systèmes, d:cybersécurité, d:réseaux</span>
    </div>
    <?php if (empty($themes)): ?>
        <div class="alert alert-warning text-center">
            ⚠️ Aucun thème trouvé pour votre recherche.
        </div>
    <?php endif; ?>
    <div class="row row-cols-1 row-cols-md-2 g-4">
        <?php foreach ($themes as $theme): ?>
            <div class="col">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($theme['titre']) ?></h5>
                        <p><strong>Domaine :</strong> <?= htmlspecialchars($theme['domaine']) ?></p>
                        <p><strong>Date :</strong> <?= htmlspecialchars($theme['date_proposition']) ?></p>

                        <button onclick="toggleDetails(<?= $theme['id_theme'] ?>)" class="btn btn-outline-primary btn-toggle">
                            📄 Voir les détails
                        </button>

                        <div id="detail-<?= $theme['id_theme'] ?>" class="mt-3 p-3 border rounded d-none bg-light">
                            <p><strong>Description :</strong> <?= nl2br(htmlspecialchars($theme['description'])) ?></p>
                            <p><strong>Catalogue thématique :</strong> <?= htmlspecialchars($theme['catalogue'] ?? '---') ?></p>
                            <p><strong>Tuteur référent :</strong>
                                <?= htmlspecialchars(($theme['prenom_tuteur'] ?? '') . ' ' . ($theme['nom_tuteur'] ?? '')) ?>
                            </p>

                            <form method="post" action="index.php?page=candidater_theme" class="mt-3">
                                <input type="hidden" name="id_theme" value="<?= $theme['id_theme'] ?>">
                                <button type="submit" class="btn btn-success">✅ Postuler</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="mt-4 text-center">
        <a href="index.php?page=dashboard" class="btn btn-secondary">← Retour au tableau de bord</a>
    </div>
</div>

</body>
</html>

