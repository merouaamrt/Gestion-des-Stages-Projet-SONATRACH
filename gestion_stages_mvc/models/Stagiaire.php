<?php
require_once __DIR__ . '/../config/database.php';

class Stagiaire {

    public static function profilComplet($id_utilisateur) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT profil_complet FROM stagiaire WHERE id_utilisateur = ?");
        $stmt->execute([$id_utilisateur]);
        return $stmt->fetchColumn() == 1;
    }

    public static function ajouterEtRetournerId($data) {
        $db = Database::connect();

        $stmt = $db->prepare("
            INSERT INTO stagiaire (nom, prenom, email, telephone, universite, niveau_etude)
            VALUES (:nom, :prenom, :email, :telephone, :universite, :niveau)
        ");

        $stmt->execute([
            ':nom' => $data['nom'],
            ':prenom' => $data['prenom'],
            ':email' => $data['email'],
            ':telephone' => $data['telephone'],
            ':universite' => $data['universite'],
            ':niveau' => $data['niveau']
        ]);

        return $db->lastInsertId();
    }

    public static function estStagiaire($id_utilisateur) {
        try {
            $db = Database::connect();
            $stmt = $db->prepare("SELECT id_stagiaire FROM stagiaire WHERE id_utilisateur = ?");
            $stmt->execute([$id_utilisateur]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            return false;
        }
    }
    
}
