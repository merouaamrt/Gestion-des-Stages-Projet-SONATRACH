<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$tuteurId = $_SESSION['id_utilisateur'] ?? null;
$prenom    = $_SESSION['prenom'] ?? '';
$nom       = $_SESSION['nom'] ?? '';

$filtreTitre  = $_GET['filtre'] ?? '';
$filtreStatut = $_GET['statut'] ?? '';
$filtreOrigine = $_GET['origine'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>🎓 Tableau de bord - Tuteur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">

<div class="container bg-white p-4 rounded shadow">

    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>👋 Bienvenue</h4>
        <div class="dropdown text-end">
            <button class="btn btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                👤 <?= htmlspecialchars(trim($prenom . ' ' . $nom)) ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="index.php?page=tuteur_dashboard">📄 Mes thèmes publiés</a></li>
                <li><a class="dropdown-item" href="index.php?page=tuteur_candidatures" class="list-group-item"> 📬 Candidatures reçues</a></li>
                <li><a class="dropdown-item" href="index.php?page=themes_stagiaires">💡 Thèmes proposés par les stagiaires</a></li>
                <li><a class="dropdown-item" href="index.php?page=ajouter_theme_stagiaire">➕ Ajouter un nouveau thème</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="index.php?page=logout">🚪 Déconnexion</a></li>
            </ul>
        </div>
    </div>

    <!-- Barre de recherche et filtres -->
    <form method="get" action="index.php" class="row g-2 align-items-center mb-4">
        <input type="hidden" name="page" value="tuteur_dashboard">

        <div class="col-md-6">
            <input type="text" class="form-control" name="filtre"
                   placeholder="Recherche (titre, d:domaine)" 
                   value="<?= htmlspecialchars($filtreTitre) ?>">
        </div>

        <div class="col-md-2">
            <select name="statut" class="form-select">
                <option value="">Statut : Tous</option>
                <option value="Libre" <?= $filtreStatut === 'Libre' ? 'selected' : '' ?>>Libre</option>
                <option value="Pris" <?= $filtreStatut === 'Pris' ? 'selected' : '' ?>>Pris</option>
            </select>
        </div>

        <div class="col-md-2">
            <select name="origine" class="form-select">
                <option value="">Origine : Tous</option>
                <option value="tuteur" <?= $filtreOrigine === 'tuteur' ? 'selected' : '' ?>>Tuteur</option>
                <option value="stagiaire" <?= $filtreOrigine === 'stagiaire' ? 'selected' : '' ?>>Stagiaire</option>
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100" type="submit">Appliquer</button>
        </div>
    </form>

    <!-- Tableau des thèmes -->
    <h5>🗂️ Liste de tous les thèmes disponibles</h5>
    <table class="table table-striped table-bordered mt-3">
        <thead class="table-dark">
        <tr>
            <th>Titre</th>
            <th>Domaine</th>
            <th>Proposé par</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($themes)) : ?>
            <?php foreach ($themes as $theme) : ?>
                <tr>
                    <td><?= htmlspecialchars($theme['titre']) ?></td>
                    <td><?= htmlspecialchars($theme['domaine']) ?></td>
                    <td><?= htmlspecialchars(trim(($theme['prenom'] ?? '') . ' ' . ($theme['nom'] ?? ''))) ?></td>
                    <td>
                        <?php if ($theme['statut'] === 'Libre') : ?>
                            <span class="badge bg-success">Libre</span>
                        <?php elseif ($theme['statut'] === 'Pris') : ?>
                            <span class="badge bg-danger">Pris</span>
                        <?php else : ?>
                            <?= htmlspecialchars($theme['statut']) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="index.php?page=voir_theme&id=<?= urlencode($theme['id_theme']) ?>" class="btn btn-sm btn-info">Voir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="5" class="text-center text-muted">Aucun thème trouvé.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
