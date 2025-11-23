if (!isset($themes)) {
    echo "<div class='alert alert-warning'>Aucun thème reçu.</div>";
    return;
}
?>
<div class="container mt-4">
    <h2 class="mb-4">🎓 Thèmes proposés par les stagiaires</h2>

    <form method="GET" class="row g-3 mb-4">
        <input type="hidden" name="page" value="themes_stagiaires">

        <div class="col-md-4">
            <input type="text" name="filtre" class="form-control" placeholder="🔍 Rechercher un mot-clé..." value="<?= htmlspecialchars($_GET['filtre'] ?? '') ?>">
        </div>

        <div class="col-md-3">
            <select name="statut" class="form-select">
                <option value="">Tous les statuts</option>
                <option value="libre" <?= ($_GET['statut'] ?? '') === 'libre' ? 'selected' : '' ?>>Libre</option>
                <option value="pris" <?= ($_GET['statut'] ?? '') === 'pris' ? 'selected' : '' ?>>Pris</option>
            </select>
        </div>

        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Filtrer</button>
        </div>
    </form>

    <?php if (empty($themes)): ?>
        <div class="alert alert-info">Aucun thème proposé par les stagiaires pour le moment.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Domaine</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($themes as $theme): ?>
                        <tr>
                            <td><?= htmlspecialchars($theme['titre']) ?></td>
                            <td><?= htmlspecialchars($theme['domaine']) ?></td>
                            <td>
                                <?php if (strtolower($theme['statut']) === 'libre'): ?>
                                    <span class="badge bg-success">Libre</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Pris</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="index.php?page=voir_theme&id=<?= $theme['id'] ?>" class="btn btn-outline-info btn-sm">👁️ Voir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
