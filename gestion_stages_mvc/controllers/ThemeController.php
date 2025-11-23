<?php
require_once 'models/Theme.php';
require_once 'models/Stagiaire.php';
require_once 'config/database.php';

class ThemeController {

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $id_utilisateur = $_SESSION['id_utilisateur'] ?? null;

       
        if (Stagiaire::estStagiaire($id_utilisateur) && !Stagiaire::profilComplet($id_utilisateur)) {
            header("Location: index.php?page=completer_profil");
            exit;
        }

        $q = $_GET['q'] ?? '';
        $themes = !empty($q)
            ? Theme::rechercherAvecPrefixe($q)
            : Theme::getAllPourStagiaire();

        require 'views/themes/index.php';
    }

    
   public function handleProposer() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $id_utilisateur = $_SESSION['id_utilisateur'] ?? null;

    if (!$id_utilisateur) {
        header("Location: index.php?page=login");
        exit;
    }

    
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $catalogue = trim($_POST['catalogue'] ?? '');
    $domaine = trim($_POST['domaine'] ?? '');

    if ($titre === '' || $description === '' || $domaine === '') {
        echo "<p style='color:red;'>Veuillez remplir tous les champs obligatoires.</p>";
        echo "<a href='index.php?page=proposer_theme'>← Retour</a>";
        return;
    }

    try {
        $db = Database::connect();

        
        $stmt = $db->prepare("SELECT id_stagiaire FROM stagiaire WHERE id_utilisateur = ?");
        $stmt->execute([$id_utilisateur]);
        $stagiaire = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$stagiaire) {
            
            $stmtInsert = $db->prepare("
                INSERT INTO stagiaire (id_utilisateur, universite, telephone, niveau_etude, profil_complet)
                VALUES (?, '', '', '', 0)
            ");
            $stmtInsert->execute([$id_utilisateur]);
            $id_stagiaire = $db->lastInsertId();
        } else {
            $id_stagiaire = $stagiaire['id_stagiaire'];
        }

        
        $stmt = $db->prepare("
            INSERT INTO theme
                (titre, description, domaine, catalogue, statut, origine, id_stagiaire, date_proposition)
            VALUES (?, ?, ?, ?, 'Libre', 'stagiaire', ?, NOW())
        ");
        $stmt->execute([
            $titre,
            $description,
            $domaine,
            $catalogue,
            $id_stagiaire
        ]);

        
        $id_theme = $db->lastInsertId();

        
        require_once 'models/Notification.php';
        Notification::ajouter(
            $id_theme,
            "Votre thème « $titre » a bien été proposé et attend validation."
        );

        $_SESSION['success_message'] = "Votre thème a été proposé avec succès et est en attente de validation par un tuteur.";
        header("Location: index.php?page=dashboard");
        exit;

    } catch (PDOException $e) {
        echo "<p style='color:red;'>Erreur lors de la proposition : " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
    public function postuler() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $id_utilisateur = $_SESSION['id_utilisateur'] ?? null;
        $id_theme = $_POST['id_theme'] ?? null;

        if (!$id_utilisateur || !$id_theme) {
            echo "<p style='color:red;'>❌ Données manquantes.</p>";
            return;
        }

        try {
            $db = Database::connect();

            
            $stmt = $db->prepare("SELECT id_stagiaire FROM stagiaire WHERE id_utilisateur = ?");
            $stmt->execute([$id_utilisateur]);
            $stagiaire = $stmt->fetch(PDO::FETCH_ASSOC);
            $id_stagiaire = $stagiaire['id_stagiaire'] ?? null;

            if (!$id_stagiaire) {
                echo "<p style='color:red;'>Stagiaire introuvable.</p>";
                return;
            }

            
            $check = $db->prepare("SELECT * FROM candidature WHERE id_stagiaire = ? AND id_theme = ?");
            $check->execute([$id_stagiaire, $id_theme]);
            if ($check->fetch()) {
                echo "<p class='text-warning'>⚠️ Vous avez déjà postulé pour ce thème.</p>";
                return;
            }

 
            $insert = $db->prepare("
                INSERT INTO candidature (id_stagiaire, id_theme, date_candidature, statut)
                VALUES (?, ?, NOW(), 'en attente')
            ");
            $insert->execute([$id_stagiaire, $id_theme]);

            echo "<p class='text-success'>📩 Votre candidature a été envoyée.</p>";

        } catch (PDOException $e) {
            echo "<p class='text-danger'>Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }

   
    public function ajouterThemeAvecStagiaire() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $titre = $_POST['titre'];
            $domaine = $_POST['domaine'];
            $catalogue = $_POST['catalogue'] ?? '';
            $description = $_POST['description'] ?? '';
            $type_stage = $_POST['type_stage'];

            $nom = trim($_POST['stagiaire_nom'] ?? '');
            $prenom = trim($_POST['stagiaire_prenom'] ?? '');
            $email = trim($_POST['stagiaire_email'] ?? '');
            $telephone = $_POST['stagiaire_telephone'] ?? '';
            $universite = $_POST['stagiaire_universite'] ?? '';
            $niveau = $_POST['stagiaire_niveau'] ?? '';

            $duree = '';
            $niveau_pfe = '';

            switch ($type_stage) {
                case 'pfe':
                    $niveau_pfe = $_POST['pfe_niveau'] ?? '';
                    break;
                case 'ete':
                    $duree = $_POST['ete_duree'] ?? '';
                    break;
                case 'induction':
                    $duree = $_POST['induction_duree'] ?? '';
                    break;
                case 'apprenti':
                    $duree = $_POST['apprenti_duree'] ?? '';
                    break;
            }

            try {
                $db = Database::connect();
                $db->beginTransaction();

                $id_stagiaire = null;
                $hasStagiaire = $nom && $prenom && $email;

                if ($hasStagiaire) {
                    
                    $stmtUser = $db->prepare("
                        INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, type)
                        VALUES (?, ?, ?, ?, 'stagiaire')
                    ");
                    $mot_de_passe = password_hash('stagiaire123', PASSWORD_DEFAULT);
                    $stmtUser->execute([$nom, $prenom, $email, $mot_de_passe]);
                    $id_utilisateur = $db->lastInsertId();

                    $stmtStagiaire = $db->prepare("
                        INSERT INTO stagiaire (id_utilisateur, universite, telephone, niveau_etude, profil_complet)
                        VALUES (?, ?, ?, ?, 1)
                    ");
                    $stmtStagiaire->execute([$id_utilisateur, $universite, $telephone, $niveau]);
                    $id_stagiaire = $db->lastInsertId();
                }

                $id_tuteur = $this->getTuteurIdFromSession();

                $statut = ($hasStagiaire && $id_stagiaire) ? 'Pris' : 'Libre';
                if (!in_array($statut, ['Libre', 'Pris'])) {
                    $statut = 'Libre';
                }

                $stmtTheme = $db->prepare("
                    INSERT INTO theme (titre, description, domaine, catalogue, origine, statut, date_proposition, id_tuteur)
                    VALUES (?, ?, ?, ?, 'tuteur', ?, NOW(), ?)
                ");
                $stmtTheme->execute([$titre, $description, $domaine, $catalogue, $statut, $id_tuteur]);
                $id_theme = $db->lastInsertId();

                if ($hasStagiaire && $id_stagiaire) {
                    $stmtCand = $db->prepare("
                        INSERT INTO candidature (id_theme, id_stagiaire, statut, date_candidature)
                        VALUES (?, ?, 'accepté', NOW())
                    ");
                    $stmtCand->execute([$id_theme, $id_stagiaire]);
                }

                $db->commit();
                header('Location: index.php?page=themes');
                exit;

            } catch (PDOException $e) {
                $db->rollBack();
                echo "<p style='color:red;'>Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
    }

    private function getTuteurIdFromSession() {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT id_tuteur FROM tuteur WHERE id_utilisateur = ?");
        $stmt->execute([$_SESSION['id_utilisateur']]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res['id_tuteur'] ?? null;
    }
}
