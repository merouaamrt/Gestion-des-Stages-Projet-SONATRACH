<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'models/Stagiaire.php';

$id_utilisateur = $_SESSION['id_utilisateur'] ?? null;

if (!Stagiaire::profilComplet($id_utilisateur)) {
    header("Location: index.php?page=completer_profil");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>📝 Proposer un thème</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">

<div class="container bg-white p-4 rounded shadow">
    <h2 class="mb-4">📝 Proposer un thème</h2>

    <form method="post" action="index.php?page=proposer_theme_handler">

        <div class="mb-3">
            <label for="titre" class="form-label">Titre :</label>
            <input type="text" id="titre" name="titre" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description :</label>
            <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
        </div>

        <div class="mb-3">
            <label for="catalogue" class="form-label">Catalogue thématique :</label>
            <select class="form-select" name="catalogue" id="catalogue" onchange="updateDomaines()" required>
                <option value="">-- Sélectionner --</option>
                <option value="Sciences de la nature">Sciences de la nature</option>
                <option value="Systèmes d'information">Systèmes d'information</option>
                <option value="Économie">Économie</option>
                <option value="Hydrocarbures">Hydrocarbures</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="domaine" class="form-label">Domaine :</label>
            <select id="domaine" name="domaine" class="form-select" required>
                <option value="">-- Choisir un catalogue d’abord --</option>
            </select>
        </div>

        <input type="hidden" name="origine" value="stagiaire">

        <div class="text-end">
            <button type="submit" class="btn btn-success">✅ Envoyer</button>
            <a href="index.php?page=dashboard" class="btn btn-secondary">← Retour</a>
        </div>
    </form>
</div>

<!-- ✅ SCRIPT de mise à jour des domaines -->
<script>
const domaines = {
    "Sciences de la nature": ["Biologie", "Chimie", "Physique", "Géologie"],
    "Systèmes d'information": ["Développement Web", "Intelligence Artificielle", "Réseaux", "Cybersécurité"],
    "Économie": ["Finance", "Comptabilité", "Marketing", "Gestion"],
    "Hydrocarbures": ["Forage", "Raffinage", "Sécurité industrielle", "Géosciences"]
};

function updateDomaines() {
    const catalogue = document.getElementById('catalogue').value;
    const domaineSelect = document.getElementById('domaine');

    domaineSelect.innerHTML = '';

    if (domaines[catalogue]) {
        domaines[catalogue].forEach(domaine => {
            const option = document.createElement('option');
            option.value = domaine;
            option.text = domaine;
            domaineSelect.appendChild(option);
        });
        domaineSelect.disabled = false;
    } else {
        const option = document.createElement('option');
        option.text = '-- Choisir un catalogue d’abord --';
        domaineSelect.appendChild(option);
        domaineSelect.disabled = true;
    }
}
</script>

</body>
</html>
