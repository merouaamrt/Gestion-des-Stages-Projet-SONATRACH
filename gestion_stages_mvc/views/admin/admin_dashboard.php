<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SESSION['type'] !== 'admin') {
    header("Location: index.php?page=login");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>🎛️ Tableau de bord admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">

<div class="container bg-white shadow rounded p-5">
    <h2 class="mb-4 text-primary">👋 Bienvenue, <?= htmlspecialchars($_SESSION['prenom']) ?> !</h2>
    <p class="lead">🎛️ Que souhaitez-vous faire ?</p>

    <div class="list-group">
        <a href="index.php?page=ajouter_tuteur" class="list-group-item list-group-item-action">
            ➕ Ajouter un tuteur
            
        </a>
        <a href="index.php?page=liste_tuteurs" class="list-group-item list-group-item-action">
    📋 Liste des tuteurs
</a>



        
        <a href="index.php?page=logout" class="list-group-item list-group-item-action text-danger">
            🔓 Se déconnecter
        </a>
    </div>
</div>

</body>
</html>
