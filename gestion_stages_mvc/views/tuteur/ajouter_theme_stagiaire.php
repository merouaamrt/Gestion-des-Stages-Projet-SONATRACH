<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un nouveau thème</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light p-4">

<div class="container bg-white p-4 rounded shadow">
    <h2>📄 Ajouter un nouveau thème</h2>

    <form method="POST" action="index.php?page=ajouter_theme_stagiaire_handler">

        <!-- Champ caché pour savoir si un stagiaire est ajouté -->
        <input type="hidden" name="has_stagiaire" id="has_stagiaire" value="0">

        <!-- 🧠 Infos sur le thème -->
        <h4>🧠 Infos sur le thème</h4>

        <div class="mb-3">
            <label>Catalogue</label>
            <select name="catalogue" id="catalogue" class="form-select" onchange="updateDomaines()" required>
                <option value="">-- Choisir --</option>
                <option value="Sciences de la nature">Sciences de la nature</option>
                <option value="Systèmes d'information">Systèmes d'information</option>
                <option value="Économie">Économie</option>
                <option value="Hydrocarbures">Hydrocarbures</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Domaine</label>
            <select name="domaine" id="domaine" class="form-select" required>
                <option value="">-- Choisir un domaine --</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Titre du thème</label>
            <input type="text" name="titre" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label>Date de création</label>
            <input type="date" name="date_creation" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>

        <!-- 👤 Ajouter stagiaire -->
        <div class="my-3">
            <button type="button" class="btn btn-outline-primary" onclick="toggleStagiaire()">+ Ajouter un stagiaire</button>
        </div>

        <div id="bloc-stagiaire" style="display: none;">

            <h4>👤 Infos sur le stagiaire</h4>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Nom</label>
                    <input type="text" name="stagiaire_nom" class="form-control">
                </div>
                <div class="col-md-6">
                    <label>Prénom</label>
                    <input type="text" name="stagiaire_prenom" class="form-control">
                </div>
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="stagiaire_email" class="form-control">
            </div>

            <div class="mb-3">
                <label>Téléphone</label>
                <input type="text" name="stagiaire_telephone" class="form-control">
            </div>

            <div class="mb-3">
                <label>Université</label>
                <input type="text" name="stagiaire_universite" class="form-control">
            </div>

            <div class="mb-3">
                <label>Niveau d'étude</label>
                <input type="text" name="stagiaire_niveau" class="form-control">
            </div>

            <!-- 🎓 Type de stage -->
            <h4>🎓 Type de stage</h4>
            <div class="mb-3">
                <label>Type</label>
                <select name="type_stage" class="form-select" id="type_stage" onchange="toggleStageFields()">
                    <option value="">-- Sélectionner --</option>
                    <option value="pfe">Stage fin d’étude</option>
                    <option value="ete">Stage d’été</option>
                    <option value="induction">Stage d’induction</option>
                    <option value="apprenti">Stage apprenti</option>
                </select>
            </div>

            <div class="mb-3" id="pfe_niveau_field" style="display: none;">
                <label>Niveau</label>
                <select name="pfe_niveau" class="form-select">
                    <option value="">-- Choisir le niveau --</option>
                    <option value="Technicien">Technicien</option>
                    <option value="Supérieur">Supérieur</option>
                    <option value="Master">Master</option>
                    <option value="Doctorat">Doctorat</option>
                    <option value="Magister">Magister</option>
                </select>
            </div>

            <div class="mb-3" id="ete_infos" style="display: none;">
                <label>Année en cours</label>
                <input type="text" name="ete_annee" class="form-control mb-2">
                <label>Université</label>
                <input type="text" name="ete_universite" class="form-control">
            </div>

            <div class="mb-3" id="induction_infos" style="display: none;">
                <label>Durée</label>
                <input type="text" name="induction_duree" class="form-control mb-2">
                <label>Université</label>
                <input type="text" name="induction_universite" class="form-control">
            </div>

            <div class="mb-3" id="apprenti_infos" style="display: none;">
                <label>Durée</label>
                <input type="text" name="apprenti_duree" class="form-control mb-2">
                <label>Université</label>
                <input type="text" name="apprenti_universite" class="form-control">
            </div>
        </div>

        <button type="submit" class="btn btn-success mt-4">✅ Ajouter</button>
        <a href="index.php?page=tuteur_dashboard" class="btn btn-secondary mt-4">← Retour</a>
    </form>
</div>

<script>
    const domainesParCatalogue = {
        "Sciences de la nature": ["Biologie", "Chimie", "Physique"],
        "Systèmes d'information": ["Développement Web", "IA", "Réseaux"],
        "Économie": ["Finance", "Comptabilité"],
        "Hydrocarbures": ["Forage", "Raffinage", "Sécurité industrielle"]
    };

    function updateDomaines() {
        const catalogue = document.getElementById("catalogue").value;
        const domaine = document.getElementById("domaine");
        domaine.innerHTML = '<option value="">-- Choisir un domaine --</option>';

        if (domainesParCatalogue[catalogue]) {
            domainesParCatalogue[catalogue].forEach(dom => {
                const opt = document.createElement("option");
                opt.value = dom;
                opt.textContent = dom;
                domaine.appendChild(opt);
            });
        }
    }

    function toggleStagiaire() {
        const bloc = document.getElementById("bloc-stagiaire");
        const hasInput = document.getElementById("has_stagiaire");

        if (bloc.style.display === "none") {
            bloc.style.display = "block";
            hasInput.value = "1";
        } else {
            bloc.style.display = "none";
            hasInput.value = "0";
        }
    }

    function toggleStageFields() {
        const type = document.getElementById("type_stage").value;
        document.getElementById("pfe_niveau_field").style.display = (type === 'pfe') ? 'block' : 'none';
        document.getElementById("ete_infos").style.display = (type === 'ete') ? 'block' : 'none';
        document.getElementById("induction_infos").style.display = (type === 'induction') ? 'block' : 'none';
        document.getElementById("apprenti_infos").style.display = (type === 'apprenti') ? 'block' : 'none';
    }
</script>

</body>
</html>
