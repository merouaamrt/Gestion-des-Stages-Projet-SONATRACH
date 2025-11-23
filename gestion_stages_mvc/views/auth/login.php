<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>🔐 Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

<div class="card shadow p-4" style="min-width: 380px;">
    <h3 class="text-center mb-4">🔐 Connexion</h3>

    <?php if (!empty($erreur)) : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="post" action="index.php?page=login_handler">
        <div class="mb-3">
            <label class="form-label">Type d'utilisateur :</label>
            <select name="type_connexion" class="form-select" onchange="toggleLoginFields(this.value)">
                <option value="stagiaire">Stagiaire</option>
                <option value="tuteur">Tuteur</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <div id="emailField" class="mb-3">
            <label class="form-label">Email :</label>
            <input type="email" name="email" class="form-control">
        </div>

        <div id="identifiantField" class="mb-3 d-none">
            <label class="form-label">Identifiant :</label>
            <input type="text" name="identifiant" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Mot de passe :</label>
            <input type="password" name="mot_de_passe" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Se connecter</button>
    </form>

    <div class="text-center mt-3">
        👤 Pas encore de compte ?
        <a href="index.php?page=register">Créer un compte stagiaire</a>
    </div>
</div>

<script>
    function toggleLoginFields(type) {
        document.getElementById('emailField').classList.toggle('d-none', type === 'tuteur');
        document.getElementById('identifiantField').classList.toggle('d-none', type !== 'tuteur');
    }
</script>

</body>
</html>

