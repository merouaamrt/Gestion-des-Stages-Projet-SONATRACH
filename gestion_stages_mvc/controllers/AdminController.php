<?php
require_once 'models/Utilisateur.php';
require_once 'models/Tuteur.php';
require_once 'config/database.php';

class AdminController {
    public function handleAjouterTuteur() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $nom = $_POST['nom'] ?? '';
        $prenom = $_POST['prenom'] ?? '';
        $email = $_POST['email'] ?? '';
        $mot_de_passe = $_POST['mot_de_passe'] ?? '';
        $catalogue = $_POST['catalogue'] ?? '';
        $departement = $_POST['departement'] ?? '';
        $telephone = $_POST['telephone_professionnel'] ?? '';

        $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        try {
            $db = Database::connect();

            
            $check = $db->prepare("SELECT * FROM utilisateur WHERE email = ?");
            $check->execute([$email]);
            if ($check->fetch()) {
                $_SESSION['ajout_tuteur_erreur'] = "❌ Cet email est déjà utilisé.";
                header("Location: index.php?page=ajouter_tuteur");
                exit;
            }

           
            $stmt1 = $db->prepare("INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, type) VALUES (?, ?, ?, ?, 'tuteur')");
            $stmt1->execute([$nom, $prenom, $email, $mot_de_passe_hache]);
            $id_utilisateur = $db->lastInsertId();

            // Ajouter tuteur
            $stmt2 = $db->prepare("INSERT INTO tuteur (id_utilisateur, catalogue, departement, telephone_professionnel)
                                   VALUES (?, ?, ?, ?)");
            $stmt2->execute([$id_utilisateur, $catalogue, $departement, $telephone]);

            $_SESSION['ajout_tuteur_success'] = "✅ Tuteur ajouté avec succès.";
            header("Location: index.php?page=ajouter_tuteur");
        } catch (PDOException $e) {
            $_SESSION['ajout_tuteur_erreur'] = "❌ Erreur : " . $e->getMessage();
            header("Location: index.php?page=ajouter_tuteur");
        }
    }
}

