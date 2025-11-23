<?php
require_once __DIR__ . '/../config/database.php';

class Admin {
    public static function getAllTuteurs() {
        $db = Database::connect();

        $sql = "
            SELECT u.nom, u.prenom, u.email, t.catalogue, t.departement, t.telephone_professionnel
            FROM tuteur t
            JOIN utilisateur u ON t.id_utilisateur = u.id_utilisateur
            ORDER BY u.nom ASC
        ";

        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
