<?php
require_once __DIR__ . '/../config/database.php';

class Tuteur {

    public static function create($id_utilisateur, $catalogue, $departement, $telephone) {
        $db = Database::connect();
        $stmt = $db->prepare("
            INSERT INTO tuteur (id_utilisateur, catalogue, departement, telephone_professionnel)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$id_utilisateur, $catalogue, $departement, $telephone]);
    }

    public static function findByUtilisateur($id_utilisateur) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM tuteur WHERE id_utilisateur = ?");
        $stmt->execute([$id_utilisateur]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAll() {
        $db = Database::connect();
        $stmt = $db->query("
            SELECT t.*, u.nom, u.prenom, u.email
            FROM tuteur t
            JOIN utilisateur u ON t.id_utilisateur = u.id_utilisateur
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function update($id_utilisateur, $catalogue, $departement, $telephone) {
        $db = Database::connect();
        $stmt = $db->prepare("
            UPDATE tuteur
            SET catalogue = ?, departement = ?, telephone_professionnel = ?
            WHERE id_utilisateur = ?
        ");
        return $stmt->execute([$catalogue, $departement, $telephone, $id_utilisateur]);
    }

    public static function delete($id_utilisateur) {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM tuteur WHERE id_utilisateur = ?");
        return $stmt->execute([$id_utilisateur]);
    }

    
    public static function ajouter($data) {
        $db = Database::connect();

        $stmt = $db->prepare("
            INSERT INTO utilisateur (identifiant, nom, prenom, email, mot_de_passe, type)
            VALUES (:identifiant, :nom, :prenom, :email, :mot_de_passe, 'tuteur')
        ");
        $stmt->execute([
            ':identifiant' => $data['identifiant'],
            ':nom' => $data['nom'],
            ':prenom' => $data['prenom'],
            ':email' => $data['email'],
            ':mot_de_passe' => password_hash($data['mot_de_passe'], PASSWORD_DEFAULT)
        ]);

        $id_utilisateur = $db->lastInsertId();

        $stmt2 = $db->prepare("
            INSERT INTO tuteur (id_utilisateur, telephone_professionnel, departement, catalogue)
            VALUES (:id_utilisateur, :telephone, :departement, :catalogue)
        ");
        return $stmt2->execute([
            ':id_utilisateur' => $id_utilisateur,
            ':telephone' => $data['telephone'],
            ':departement' => $data['departement'],
            ':catalogue' => $data['catalogue']
        ]);
    }
  

}

