<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>📬 Candidatures reçues</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container bg-white p-4 rounded shadow">

    <h4>📬 Candidatures reçues</h4>

   
    <form method="GET" class="mb-3 p-3 border rounded bg-light d-flex align-items-end gap-3 flex-wrap">
        <input type="hidden" name="page" value="tuteur_candidatures">

        <div class="flex-fill">
            <label class="form-label">🔍 Recherche (titre ou d:domaine)</label>
            <input type="text" name="filtre" class="form-control" value="<?= htmlspecialchars($_GET['filtre'] ?? '') ?>">
        </div>

        <div>
            <label class="form-label">Statut</label>
            <select name="statut" class="form-select">
                <option value="">Tous</option>
                <option value="Libre" <?= ($_GET['statut'] ?? '') === 'Libre' ? 'selected' : '' ?>>Libre</option>
                <option value="Pris" <?= ($_GET['statut'] ?? '') === 'Pris' ? 'selected' : '' ?>>Pris</option>
            </select>
        </div>

        <div class="d-flex flex-column">
            <label class="form-label">Thèmes à afficher</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tous" id="mes_themes" value="0"
                    <?= (!isset($_GET['tous']) || $_GET['tous'] !== '1') ? 'checked' : '' ?>>
                <label class="form-check-label" for="mes_themes">Mes thèmes</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tous" id="tous_themes" value="1"
                    <?= (isset($_GET['tous']) && $_GET['tous'] === '1') ? 'checked' : '' ?>>
                <label class="form-check-label" for="tous_themes">Tous les thèmes</label>
            </div>
        </div>

        <div>
            <button type="submit" class="btn btn-primary">🔍 Filtrer</button>
        </div>
    </form>

   
    <table class="table table-striped table-bordered mt-3">
        <thead class="table-dark">
            <tr>
                <th>Titre</th>
                <th>Domaine</th>
                <th>Proposé par</th>
                <th>Statut</th>
                <th>Candidatures</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($themes)) : ?>
                <?php foreach ($themes as $theme): ?>
                    <tr>
                        <td><?= htmlspecialchars($theme['titre']) ?></td>
                        <td><?= htmlspecialchars($theme['domaine']) ?></td>
                        <td>
                            <?= htmlspecialchars(trim(($theme['prenom'] ?? '') . ' ' . ($theme['nom'] ?? ''))) ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= $theme['statut'] === 'Pris' ? 'danger' : 'success' ?>">
                                <?= htmlspecialchars($theme['statut']) ?>
                            </span>
                        </td>
                        <td>
    <?= $theme['statut'] === 'Pris' ? '❌' : ((int)($theme['nb_candidatures'] ?? 0)) ?>
</td>

                        <td>
     <a href="index.php?page=voir_candidatures&id=<?= urlencode($theme['id_theme']) ?>"
   class="btn btn-sm btn-info">Voir</a>


                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center text-muted">Aucun thème trouvé.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="text-end mt-3">
        <a href="index.php?page=tuteur_dashboard" class="btn btn-secondary">⬅ Retour</a>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
