<?php if (!isset($theme)) { echo "Thème introuvable."; return; } ?>

<h2>📄 Détail du thème : <?= htmlspecialchars($theme['titre']) ?></h2>

<p><strong>Description :</strong> <?= nl2br(htmlspecialchars($theme['description'])) ?></p>
<p><strong>Catalogue :</strong> <?= htmlspecialchars($theme['catalogue'] ?? '') ?></p>
<p><strong>Domaine :</strong> <?= htmlspecialchars($theme['domaine']) ?></p>
<p><strong>Statut :</strong> <?= htmlspecialchars($theme['statut']) ?></p>
<p><strong>Date de proposition :</strong> <?= htmlspecialchars($theme['date_proposition']) ?></p>

<?php if (!empty($theme['tuteur_nom'])): ?>
    <p><strong>Tuteur référent :</strong> <?= htmlspecialchars($theme['tuteur_prenom'] . ' ' . $theme['tuteur_nom']) ?></p>
<?php endif; ?>


<form method="post" action="index.php?page=candidater_theme">
    <input type="hidden" name="id_theme" value="<?= $theme['id_theme'] ?>">
    <button type="submit">📩 Postuler à ce thème</button>
</form>

<br>
<a href="index.php?page=themes">← Retour à la liste des thèmes</a>


