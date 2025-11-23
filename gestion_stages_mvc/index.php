<?php


session_start();

require_once 'controllers/AuthController.php';
require_once 'controllers/ThemeController.php';
require_once 'controllers/AdminController.php';
require_once 'controllers/TuteurController.php';
require_once 'models/Theme.php';


$page = $_GET['page'] ?? 'login';

$publicPages = ['login', 'login_handler', 'register', 'register_handler'];

if (!in_array($page, $publicPages) && empty($_SESSION['id_utilisateur'])) {
    header('Location: index.php?page=login');
    exit;
}

switch ($page) {
    case 'login':
        (new AuthController())->login();
        break;
    case 'login_handler':
        (new AuthController())->handleLogin();
        break;
    case 'logout':
        (new AuthController())->logout();
        break;
    case 'register':
        (new AuthController())->register();
        break;
    case 'register_handler':
        (new AuthController())->handleRegister();
        break;

    case 'dashboard':
        require 'views/stagiaire/dashboard.php';
        break;
    case 'themes':
        (new ThemeController())->index();
        break;
    case 'proposer_theme':
        require 'views/stagiaire/proposer_theme.php';
        break;
    case 'proposer_theme_handler':
        (new ThemeController())->handleProposer();
        break;
    case 'completer_profil':
        require 'views/stagiaire/completer_profil.php';
        break;
    case 'profil_handler':
        (new AuthController())->handleProfilCompletion();
        break;
    case 'candidater_theme':
        (new ThemeController())->postuler();
        break;

    case 'tuteur_dashboard':
        (new TuteurController())->dashboard();
        break;
    case 'ajouter_tuteur':
        (new TuteurController())->createForm();
        break;
    case 'ajouter_tuteur_handler':
        (new TuteurController())->handleCreate();
        break;
    
    case 'ajouter_theme_stagiaire':
        (new TuteurController())->addThemeWithStagiaireForm();
        break;
    case 'ajouter_theme_stagiaire_handler':
        (new TuteurController())->handleThemeWithStagiaireCreate();
        break;
     case 'admin_dashboard':
        require 'views/admin/admin_dashboard.php';
        break;
    case 'accepter_theme_stagiaire':
    (new TuteurController())->accepterThemeStagiaire();
    break;
    case 'voir_theme':
        (new TuteurController())->voirTheme();
        break;
    case 'repondre_stagiaire':
        (new TuteurController())->repondreStagiaire();
        break;
    case 'tuteur_candidatures':
        (new TuteurController())->voirCandidaturesRecues();
        break;
    case 'liste_tuteurs':
    require 'views/admin/liste_tuteurs.php';
    exit;

    case 'themes_stagiaires':
        // Correction ici : utiliser le contrôleur comme pour les autres cas
        $controller = new TuteurController();

        // Forcer le filtre origine = stagiaire
        $_GET['origine'] = 'stagiaire';

        // Réutilise dashboard() du TuteurController
        $controller->dashboard();
        break;

    default:
        echo "<h2 style='color:red;'>❌ Page introuvable : " . htmlspecialchars($page) . "</h2>";
        break;
}
