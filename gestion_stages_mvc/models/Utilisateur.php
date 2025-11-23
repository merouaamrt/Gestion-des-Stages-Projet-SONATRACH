<?php
require_once 'config/database.php';

class Utilisateur {
    public static function verifierConnexion($email, $mot_de_passe) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
            return $utilisateur;
        }

        return false;
    }

  
    public static function verifierConnexionParIdentifiant($identifiant, $mot_de_passe) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM utilisateur WHERE identifiant = :identifiant AND type = 'tuteur'");
        $stmt->execute([':identifiant' => $identifiant]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
            return $user;
        }

        return false;
    }

public static function getAllTuteurs() {
    $db = Database::connect();
    $stmt = $db->prepare("SELECT * FROM utilisateur WHERE type = 'tuteur'");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


}




