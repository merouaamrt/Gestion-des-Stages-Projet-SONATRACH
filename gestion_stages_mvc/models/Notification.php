<?php
require_once 'config/database.php';

class Notification {

 public static function ajouter($id_theme, $message, $type = 'candidature') {
    $db = Database::connect();
    $stmt = $db->prepare("
        INSERT INTO notification (id_theme, message, type, date_creation)
        VALUES (?, ?, ?, NOW())
    ");
    return $stmt->execute([$id_theme, $message, $type]);
}


    // Récupérer toutes les notifications pour un stagiaire donné
public static function getPourStagiaire($id_stagiaire) {
    $db = Database::connect();

    
    $sql = "
        SELECT n.message AS contenu, n.date_creation AS date_notification
        FROM notification n
        INNER JOIN theme t ON n.id_theme = t.id_theme
        WHERE t.id_stagiaire = ?
        ORDER BY n.date_creation DESC
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute([$id_stagiaire]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public static function ajouterPourStagiaire($id_stagiaire, $message, $type = 'candidature') {
    $db = Database::connect();
    $stmt = $db->prepare("
        INSERT INTO notification (id_stagiaire, message, type, date_creation) 
        VALUES (?, ?, ?, NOW())
    ");
    return $stmt->execute([$id_stagiaire, $message, $type]);
}


}
