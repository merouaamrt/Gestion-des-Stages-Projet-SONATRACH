<?php
require_once __DIR__ . '/../models/Utilisateur.php';
require_once __DIR__ . '/../config/database.php';

class AuthController {

    public function login() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $erreur = $_SESSION['login_erreur'] ?? '';
        unset($_SESSION['login_erreur']);
        require 'views/auth/login.php';
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        header('Location: index.php?page=login');
        exit;
    }

    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?page=login");
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) session_start();

        $type         = $_POST['type_connexion'] ?? '';
        $email        = trim($_POST['email'] ?? '');
        $identifiant  = trim($_POST['identifiant'] ?? '');
        $mot_de_passe = $_POST['mot_de_passe'] ?? '';

        // Authentification
        if ($type === 'stagiaire' || $type === 'admin') {
            $utilisateur = Utilisateur::verifierConnexion($email, $mot_de_passe);
        } elseif ($type === 'tuteur') {
            $utilisateur = Utilisateur::verifierConnexionParIdentifiant($identifiant, $mot_de_passe);
        } else {
            $_SESSION['login_erreur'] = "❌ Type d'utilisateur invalide.";
            header("Location: index.php?page=login");
            exit;
        }

        if ($utilisateur && $utilisateur['type'] === $type) {
            $_SESSION['id_utilisateur'] = $utilisateur['id_utilisateur'];
            $_SESSION['type']           = $utilisateur['type'];
            $_SESSION['prenom']         = $utilisateur['prenom'] ?? '';
            $_SESSION['nom']            = $utilisateur['nom'] ?? '';

            switch ($type) {
                case 'stagiaire':
                    header("Location: index.php?page=dashboard");
                    break;
                case 'tuteur':
                    header("Location: index.php?page=tuteur_dashboard");
                    break;
                case 'admin':
                    header('Location: index.php?page=admin_dashboard');

                    break;
            }
            exit;
        } else {
            $_SESSION['login_erreur'] = "❌ Identifiants incorrects.";
            header("Location: index.php?page=login");
            exit;
        }
    }



    public function register() {
        require 'views/auth/register.php';
    }

    public function handleRegister() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?page=register");
            exit;
        }

        $nom           = trim($_POST['nom'] ?? '');
        $prenom        = trim($_POST['prenom'] ?? '');
        $email         = trim($_POST['email'] ?? '');
        $mot_de_passe  = $_POST['mot_de_passe'] ?? '';
        $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        try {
            $db = Database::connect();
            $stmt = $db->prepare("INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, type) VALUES (?, ?, ?, ?, 'stagiaire')");
            $stmt->execute([$nom, $prenom, $email, $mot_de_passe_hache]);

            header('Location: index.php?page=login');
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Erreur : " . $e->getMessage() . "</p>";
        }
    }

    public function handleProfilCompletion() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SESSION['type'] !== 'stagiaire') {
            echo "<p style='color:red;'>❌ Seuls les stagiaires peuvent compléter leur profil.</p>";
            return;
        }

        $id_utilisateur = $_SESSION['id_utilisateur'] ?? null;
        if (!$id_utilisateur) {
            header('Location: index.php?page=login');
            exit;
        }

        $universite   = trim($_POST['universite'] ?? '');
        $telephone    = trim($_POST['telephone'] ?? '');
        $niveau       = trim($_POST['niveau_etude'] ?? '');
        $experiences  = trim($_POST['experiences'] ?? '');
        $competences  = trim($_POST['competences'] ?? '');
        $motivation   = trim($_POST['motivation'] ?? '');

        try {
            $db = Database::connect();

            $check = $db->prepare("SELECT id_stagiaire FROM stagiaire WHERE id_utilisateur = ?");
            $check->execute([$id_utilisateur]);

            if ($check->fetch()) {
                $stmt = $db->prepare("UPDATE stagiaire SET 
                    universite = ?, telephone = ?, niveau_etude = ?, experiences = ?, competences = ?, message_motivation = ?, profil_complet = 1 
                    WHERE id_utilisateur = ?");
                $stmt->execute([$universite, $telephone, $niveau, $experiences, $competences, $motivation, $id_utilisateur]);
            } else {
                $stmt = $db->prepare("INSERT INTO stagiaire 
                    (id_utilisateur, universite, telephone, niveau_etude, experiences, competences, message_motivation, profil_complet) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
                $stmt->execute([$id_utilisateur, $universite, $telephone, $niveau, $experiences, $competences, $motivation]);
            }

            header('Location: index.php?page=dashboard');
            exit;
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Erreur SQL : " . $e->getMessage() . "</p>";
        }
    }
}
