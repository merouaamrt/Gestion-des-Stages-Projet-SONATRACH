<?php
require_once __DIR__ . '/../config/database.php';

class Candidature {
    public static function ajouter($data) {
        $db = Database::connect();

        $stmt = $db->prepare("
            INSERT INTO candidature (id_stagiaire, id_theme, type_stage, message_libre, statut, date_candidature)
            VALUES (:id_stagiaire, :id_theme, :type_stage, :message_libre, :statut, NOW())
        ");

        return $stmt->execute([
            ':id_stagiaire'   => $data['id_stagiaire'],
            ':id_theme'       => $data['id_theme'],
            ':type_stage'     => $data['type_stage'],
            ':message_libre'  => $data['info_complementaire'], 
            ':statut'         => $data['statut']
        ]);
    }

    
public static function getCandidaturesRecues($id_tuteur) {
    $db = Database::connect();
    $stmt = $db->prepare("
        SELECT 
            c.id_candidature,
            s.nom AS nom_stagiaire,
            s.prenom AS prenom_stagiaire,
            s.email AS email_stagiaire,
            s.niveau_etude,
            c.type_stage,
            c.message_libre,
            c.info_complementaire,
            c.statut,
            t.titre AS titre_theme
        FROM candidature c
        JOIN stagiaire s ON c.id_stagiaire = s.id_stagiaire
        JOIN theme t ON c.id_theme = t.id_theme
        WHERE t.id_tuteur = :id_tuteur

    ");
    $stmt->execute([':id_tuteur' => $id_tuteur]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public static function getCandidaturesByTheme($id_theme) {
    $db = Database::connect();
    $stmt = $db->prepare("
        SELECT 
        
            c.id_candidature,
            c.date_candidature,
            c.message_libre,
            s.nom AS nom_stagiaire,
            s.prenom AS prenom_stagiaire,
            s.telephone,
            s.universite,
            s.niveau_etude,
            s.experiences,
            s.competences
        FROM candidature c
        JOIN stagiaire s ON c.id_stagiaire = s.id_stagiaire
        WHERE c.id_theme = :id_theme
    ");
    $stmt->execute([':id_theme' => $id_theme]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



public static function mettreAJourStatut($id_candidature, $statut, $message_tuteur = '') {
    $db = Database::connect();

    $stmt = $db->prepare("
        UPDATE candidature 
        SET statut = :statut, message_libre = :message 
        WHERE id_candidature = :id
    ");

    return $stmt->execute([
        ':statut' => $statut,
        ':message' => $message_tuteur,
        ':id' => $id_candidature
    ]);
}


public static function getById($id_candidature) {
    $db = Database::connect();

    $stmt = $db->prepare("
        SELECT 
            c.*, 
            s.nom, s.prenom, s.telephone, s.niveau_etude, 
            s.universite, s.experiences, s.competences
        FROM candidature c
        JOIN stagiaire s ON s.id_stagiaire = c.id_stagiaire
        WHERE c.id_candidature = ?
    ");
    
    $stmt->execute([$id_candidature]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


}

 


