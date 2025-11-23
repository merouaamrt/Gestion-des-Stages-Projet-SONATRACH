<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once 'models/Notification.php';
require_once 'config/database.php';

// 🔐 Sécurité : récupération des infos utilisateur
$prenom = $_SESSION['prenom'] ?? 'Stagiaire';
$id_utilisateur = $_SESSION['id_utilisateur'] ?? null;

$id_stagiaire = null;
if ($id_utilisateur) {
    $db = Database::connect();
    $stmt = $db->prepare("SELECT id_stagiaire FROM stagiaire WHERE id_utilisateur = ?");
    $stmt->execute([$id_utilisateur]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($res) {
        $id_stagiaire = $res['id_stagiaire'];
        $_SESSION['id_stagiaire'] = $id_stagiaire;
    }
}

// 📬 Récupération des notifications
$notifications = Notification::getPourStagiaire($id_stagiaire);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">

<div class="container bg-white rounded shadow p-4">
    <h2 class="text-center mb-4">🎓 Bienvenue, <?= htmlspecialchars($prenom) ?> !</h2>

    <?php if (!empty($notifications)) : ?>
        <div class="alert alert-info">
            <h5>🔔 Notifications</h5>
            <ul class="mb-0">
                <?php foreach ($notifications as $notif) : ?>
                    <li class="mb-2">
                        <?= htmlspecialchars($notif['message'] ?? $notif['contenu'] ?? '<em>Message non défini</em>') ?><br>
                        <small class="text-muted">
                            <?= isset($notif['date_creation']) 
                                ? date('d/m/Y H:i', strtotime($notif['date_creation']))
                                : (isset($notif['date_notification']) 
                                    ? date('d/m/Y H:i', strtotime($notif['date_notification']))
                                    : '<em>Date inconnue</em>') ?>
                        </small>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php else: ?>
        <div class="alert alert-secondary">Aucune notification pour le moment.</div>
    <?php endif; ?>

    <h4 class="text-center mb-4">Que souhaitez-vous faire ?</h4>

    <div class="d-grid gap-3 col-md-6 mx-auto">
        <a href="index.php?page=themes" class="btn btn-outline-primary btn-lg">
            📋 Consulter les thèmes disponibles
        </a>
        <a href="index.php?page=proposer_theme" class="btn btn-outline-success btn-lg">
            ➕ Proposer un thème
        </a>
        <a href="index.php?page=logout" class="btn btn-outline-danger btn-lg">
            🔓 Se déconnecter
        </a>
    </div>
</div>

</body>
</html>
