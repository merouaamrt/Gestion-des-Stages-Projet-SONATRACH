<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$erreur = $_SESSION['ajout_tuteur_erreur'] ?? null;
$success = $_SESSION['ajout_tuteur_success'] ?? null;
unset($_SESSION['ajout_tuteur_erreur'], $_SESSION['ajout_tuteur_success']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un tuteur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">

<div class="container bg-white p-4 rounded shadow">
    <h2>👨‍🏫 Ajouter un nouveau tuteur</h2>

    <?php if ($erreur): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" action="index.php?page=ajouter_tuteur_handler">
        <div class="row mb-3">
            <div class="col-md-6">
                <label>Nom :</label>
                <input type="text" name="nom" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label>Prénom :</label>
                <input type="text" name="prenom" class="form-control" required>
            </div>
        </div>

        <div class="mb-3">
            <label>Identifiant :</label>
            <input type="text" name="identifiant" class="form-control" required>

        </div>

        <div class="mb-3">
            <label>Email :</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Mot de passe :</label>
            <input type="password" name="mot_de_passe" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Catalogue thématique :</label>
            <select name="catalogue" id="catalogue" class="form-select" onchange="updateDepartements()" required>
                <option value="">-- Choisir --</option>
                <option value="Sciences de la nature">Sciences de la nature</option>
                <option value="Systèmes d'information">Systèmes d'information</option>
                <option value="Économie">Économie</option>
                <option value="Hydrocarbures">Hydrocarbures</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Département :</label>
            <select name="departement" id="departement" class="form-select" required>
                <option value="">-- Choisissez un département --</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Téléphone professionnel :</label>
            <input type="text" name="telephone" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">✅ Ajouter</button>
        <a href="index.php?page=admin_dashboard" class="btn btn-secondary">← Retour</a>
    </form>
</div>

<script>
    const departementsParCatalogue = {
        "Sciences de la nature": ["Biologie", "Chimie", "Physique"],
        "Systèmes d'information": ["Développement Web", "IA", "Réseaux"],
        "Économie": ["Finance", "Comptabilité"],
        "Hydrocarbures": ["Forage", "Raffinage", "Sécurité industrielle"]
    };

    function updateDepartements() {
        const catalogue = document.getElementById("catalogue").value;
        const departement = document.getElementById("departement");
        departement.innerHTML = '<option value="">-- Choisissez un département --</option>';

        if (departementsParCatalogue[catalogue]) {
            departementsParCatalogue[catalogue].forEach(dep => {
                const opt = document.createElement("option");
                opt.value = dep;
                opt.textContent = dep;
                departement.appendChild(opt);
            });
        }
    }
</script>

</body>
</html>




