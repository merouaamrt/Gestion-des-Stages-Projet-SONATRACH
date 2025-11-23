<?php
require_once 'models/Tuteur.php';
require_once 'models/Theme.php';
require_once 'models/Stagiaire.php';
require_once 'models/Candidature.php';
require_once 'models/Notification.php';
require_once 'config/database.php';

class TuteurController {

    public function createForm() {
        require 'views/admin/ajouter_tuteur.php';
    }

    public function handleCreate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();

            $nom         = $_POST['nom'] ?? '';
            $prenom      = $_POST['prenom'] ?? '';
            $identifiant = $_POST['identifiant'] ?? '';
            $email       = $_POST['email'] ?? '';
            $mot_de_passe = $_POST['mot_de_passe'] ?? '';
            $telephone   = $_POST['telephone_professionnel'] ?? '';
            $departement = $_POST['departement'] ?? '';
            $catalogue   = $_POST['catalogue'] ?? '';

            if (empty($nom) || empty($prenom) || empty($email) || empty($identifiant) || empty($mot_de_passe)) {
                $_SESSION['ajout_tuteur_erreur'] = "Tous les champs obligatoires doivent être remplis.";
                header("Location: index.php?page=ajouter_tuteur");
                exit;
            }

            $resultat = Tuteur::ajouter([
                'nom' => $nom,
                'prenom' => $prenom,
                'identifiant' => $identifiant,
                'email' => $email,
                'mot_de_passe' => $mot_de_passe,
                'telephone' => $telephone,
                'departement' => $departement,
                'catalogue' => $catalogue
            ]);

            $_SESSION[$resultat ? 'ajout_tuteur_success' : 'ajout_tuteur_erreur'] = $resultat
                ? "Tuteur ajouté avec succès."
                : "Erreur lors de l'ajout du tuteur.";

            header("Location: index.php?page=ajouter_tuteur");
            exit;
        }
    }

    public function list() {
        $tuteurs = Tuteur::getAll();
        require 'views/admin/liste_tuteurs.php';
    }

    public function addThemeWithStagiaireForm() {
        require 'views/tuteur/ajouter_theme_stagiaire.php';
    }

    public function handleThemeWithStagiaireCreate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $hasStagiaire = !empty($_POST['stagiaire_nom']) && !empty($_POST['stagiaire_prenom']);
            $statut = trim($hasStagiaire ? 'Pris' : 'Libre');

            if (!in_array($statut, ['Libre', 'Pris'])) {
                die("❌ Erreur : statut invalide = " . htmlspecialchars($statut));
            }

            $id_tuteur = $this->getTuteurIdFromSession();
            if (!$id_tuteur) {
                die("❌ Aucun tuteur trouvé pour cet utilisateur.");
            }

           
            $theme = [
                'titre' => $_POST['titre'] ?? '',
                'domaine' => $_POST['domaine'] ?? '',
                'description' => $_POST['description'] ?? '',
                'catalogue' => $_POST['catalogue'] ?? '',
                'propose_par' => $id_tuteur,
                'statut' => $statut
            ];

            $theme_id = Theme::ajouterEtRetournerId($theme);

            if (!$theme_id) {
                echo "<p class='text-danger'>❌ Erreur lors de la création du thème.</p>";
                return;
            }

            
            if ($hasStagiaire) {
                $stagiaire = [
                    'nom' => $_POST['stagiaire_nom'],
                    'prenom' => $_POST['stagiaire_prenom'],
                    'email' => $_POST['stagiaire_email'],
                    'telephone' => $_POST['stagiaire_telephone'] ?? '',
                    'universite' => $_POST['stagiaire_universite'] ?? '',
                    'niveau' => $_POST['stagiaire_niveau'] ?? '',
                ];

                $stagiaire_id = Stagiaire::ajouterEtRetournerId($stagiaire);
                if (!$stagiaire_id) {
                    echo "<p class='text-danger'>❌ Erreur lors de l'ajout du stagiaire.</p>";
                    return;
                }

                
                $type_stage = $_POST['type_stage'] ?? '';
                $info_spec = '';

                switch ($type_stage) {
                    case 'pfe':
                        $info_spec = $_POST['pfe_niveau'] ?? '';
                        break;
                    case 'ete':
                        $info_spec = ($_POST['ete_annee'] ?? '') . ' - ' . ($_POST['ete_universite'] ?? '');
                        break;
                    case 'induction':
                        $info_spec = ($_POST['induction_duree'] ?? '') . ' - ' . ($_POST['induction_universite'] ?? '');
                        break;
                    case 'apprenti':
                        $info_spec = ($_POST['apprenti_duree'] ?? '') . ' - ' . ($_POST['apprenti_universite'] ?? '');
                        break;
                }

                Candidature::ajouter([
                    'id_stagiaire' => $stagiaire_id,
                    'id_theme' => $theme_id,
                    'type_stage' => $type_stage,
                    'info_complementaire' => $info_spec,
                    'statut' => 'en attente'
                ]);
            }

            
            header('Location: index.php?page=tuteur_dashboard&ajout=ok');
            exit;
        }
    }

public function dashboard()
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    $idTuteur = $_SESSION['id_utilisateur'] ?? null;

    if (!$idTuteur) {
        header("Location: index.php?page=login");
        exit;
    }

    $filtre = $_GET['filtre'] ?? '';
    $statut = $_GET['statut'] ?? '';
    $origine = $_GET['origine'] ?? '';

    
    $themes = Theme::rechercheAvanceePourTuteur($idTuteur, $filtre, $statut, $origine);

    require 'views/tuteur/dashboard.php';
}

    private function getTuteurIdFromSession() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $id_utilisateur = $_SESSION['id_utilisateur'] ?? null;
        if (!$id_utilisateur) return null;

        $db = Database::connect();
        $stmt = $db->prepare("SELECT id_tuteur FROM tuteur WHERE id_utilisateur = ?");
        $stmt->execute([$id_utilisateur]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res['id_tuteur'] ?? null;
    }

    public function voirTheme() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $id = isset($_GET['id']) ? intval($_GET['id']) : null;
        if (!$id) {
            echo "<div class='alert alert-danger m-3'>⛔ ID du thème manquant.</div>";
            return;
        }

        $theme = Theme::getById($id);
        if (!$theme) {
            echo "<div class='alert alert-warning m-3'>⛔ Thème introuvable.</div>";
            return;
        }

        require 'views/tuteur/theme_details.php';
    }

    public function accepterThemeStagiaire() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_theme'])) {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $id_theme = (int) $_POST['id_theme'];
            $id_tuteur = $this->getTuteurIdFromSession();

            if (!$id_tuteur || !$id_theme) {
                die("⛔ Données invalides");
            }
            $theme = Theme::getById($id_theme);
            if ($theme && $theme['origine'] === 'stagiaire' && !empty($theme['id_stagiaire'])) {
                
Notification::ajouter(
    $id_theme,
    "🎉 Un tuteur a accepté votre proposition de thème : " . $theme['titre']
);



            }

            header('Location: index.php?page=tuteur_dashboard&accepte=ok');
            exit;
        }
    }

    public function repondreStagiaire() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $id_theme = $_POST['theme_id'] ?? null;

            if (!$id_theme) {
                die("⛔ Paramètres manquants.");
            }

            
Notification::ajouter(
    $id_theme,
    "Un tuteur a répondu à votre thème."
);


            header('Location: index.php?page=tuteur_dashboard&message=envoye');
            exit;
        } else {
            echo "<p class='text-danger'>Méthode non autorisée.</p>";
        }
    }


public function voirCandidaturesRecues() {
    if (session_status() === PHP_SESSION_NONE) session_start();

    $idTuteur = $_SESSION['id_utilisateur'];
    $filtre   = $_GET['filtre'] ?? '';
    $statut   = $_GET['statut'] ?? '';
    $tous     = $_GET['tous'] ?? '0';

    
    $themes = Theme::rechercheAvanceePourTuteur(
        $idTuteur,
        $filtre,
        $statut,
        '' 
    );

    
    if ($tous === '1') {
        $themes = Theme::filtrerPourTuteur($filtre, $statut, '');
    }

    require 'views/tuteur/tuteur_candidatures.php';
}
public function traiterCandidature() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once 'models/Candidature.php';
        require_once 'models/Theme.php';
        require_once 'models/Notification.php';

        $idCandidature = $_POST['id_candidature'];
        $statut = $_POST['statut'];
        $message = $_POST['message_tuteur'] ?? '';

        
        Candidature::mettreAJourStatut($idCandidature, $statut, $message);

        
        $candidature = Candidature::getById($idCandidature);
        $theme = Theme::getById($candidature['id_theme']);
        $idStagiaire = $candidature['id_stagiaire'];

        
        $prenomTuteur = $_SESSION['prenom'] ?? 'Le tuteur';
        $nomTuteur = $_SESSION['nom'] ?? '';

        
        $msg = "📢 Votre candidature au thème « " . $theme['titre'] . " » a été " .
               ($statut === 'acceptee' ? "✅ acceptée" : "❌ refusée") .
               " par le tuteur " . $prenomTuteur . " " . $nomTuteur . ".";

      
        Notification::ajouterPourStagiaire($idStagiaire, $msg, 'candidature');

        header('Location: index.php?page=tuteur_candidatures');
        exit;
    }
}

}




















