<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Création de votre compte stagiaire</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">

<div class="container bg-white shadow p-4 rounded" style="max-width: 600px;">
    <h2 class="mb-3 text-primary">👤 Création de votre compte stagiaire</h2>
    <p class="text-muted">Remplissez les champs suivants pour créer votre compte stagiaire.</p>

    <?php if (!empty($erreur)) echo "<div class='alert alert-danger'>$erreur</div>"; ?>

    <form method="post" action="index.php?page=register_handler">
        <fieldset class="mb-4">
            <legend class="fs-5 mb-3">Identifiants</legend>

            <div class="mb-3">
                <label for="email" class="form-label">Email :</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="telephone" class="form-label">Téléphone :</label>
                <input type="text" name="telephone" id="telephone" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="mot_de_passe" class="form-label">Mot de passe :</label>
                <input type="password" name="mot_de_passe" id="mot_de_passe" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="confirm_mot_de_passe" class="form-label">Confirmer votre mot de passe :</label>
                <input type="password" name="confirm_mot_de_passe" id="confirm_mot_de_passe" class="form-control" required>
            </div>
        </fieldset>

        <fieldset class="mb-4">
            <legend class="fs-5 mb-3">Informations</legend>

            <div class="mb-3">
                <label for="nom" class="form-label">Nom :</label>
                <input type="text" name="nom" id="nom" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="prenom" class="form-label">Prénom :</label>
                <input type="text" name="prenom" id="prenom" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="universite" class="form-label">Université :</label>
                <input type="text" name="universite" id="universite" class="form-control" required>
            </div>

            <input type="hidden" name="type" value="stagiaire">
        </fieldset>

        <div class="d-flex justify-content-between">
            <a href="index.php?page=login" class="btn btn-secondary">← Déjà inscrit ? Se connecter</a>
            <button type="submit" class="btn btn-primary">Créer mon compte</button>
        </div>
    </form>
</div>

</body>
</html>
