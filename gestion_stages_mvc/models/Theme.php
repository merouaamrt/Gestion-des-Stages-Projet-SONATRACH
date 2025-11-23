<?php
require_once __DIR__ . '/../config/database.php';



class Theme {

public static function ajouterEtRetournerId($data) {
    $db = Database::connect();

    if (!in_array($data['statut'], ['Libre', 'Pris'])) {
        die("❌ Statut invalide : " . htmlspecialchars($data['statut']));
    }
    if (empty($data['propose_par'])) {
        die("❌ ID tuteur (propose_par) manquant !");
    }

    // ✅ Récupérer l'id_tuteur à partir de propose_par (id_utilisateur)
    $stmtTuteur = $db->prepare("SELECT id_tuteur FROM tuteur WHERE id_utilisateur = ?");
    $stmtTuteur->execute([$data['propose_par']]);
    $result = $stmtTuteur->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        die("❌ Aucun tuteur trouvé avec l'id_utilisateur : " . htmlspecialchars($data['propose_par']));
    }

    $id_tuteur = $result['id_tuteur'];

    // ✅ Insertion
    $stmt = $db->prepare("
        INSERT INTO theme (
            titre, 
            description, 
            domaine, 
            catalogue, 
            statut, 
            origine, 
            id_tuteur, 
            propose_par, 
            date_proposition
        ) VALUES (
            :titre, 
            :description, 
            :domaine, 
            :catalogue, 
            :statut, 
            'tuteur', 
            :id_tuteur, 
            :propose_par, 
            NOW()
        )
    ");

    $stmt->execute([
        ':titre' => $data['titre'],
        ':description' => $data['description'],
        ':domaine' => $data['domaine'],
        ':catalogue' => $data['catalogue'],
        ':statut' => $data['statut'],
        ':id_tuteur' => $id_tuteur,
        ':propose_par' => $data['propose_par']
    ]);

    return $db->lastInsertId();
}


    public static function getAllPourStagiaire() {
        $db = Database::connect();
        $stmt = $db->query("
            SELECT 
                t.id_theme,
                t.titre,
                t.description,
                t.domaine,
                t.catalogue,
                t.date_proposition,
                u.nom AS nom_tuteur,
                u.prenom AS prenom_tuteur
            FROM theme t
            LEFT JOIN tuteur tut ON t.id_tuteur = tut.id_tuteur
            LEFT JOIN utilisateur u ON tut.id_utilisateur = u.id_utilisateur
            WHERE t.origine = 'tuteur' AND t.statut = 'Libre'
            ORDER BY t.date_proposition DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAll() {
        $db = Database::connect();
        $stmt = $db->query("SELECT * FROM theme ORDER BY date_proposition DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllFiltered($motcle = '') {
        $db = Database::connect();
        if (!empty($motcle)) {
            $stmt = $db->prepare("SELECT * FROM theme WHERE titre LIKE ?");
            $stmt->execute(['%' . $motcle . '%']);
        } else {
            $stmt = $db->query("SELECT * FROM theme");
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getFilteredForStagiaire($motcle = '') {
        $db = Database::connect();
        $sql = "SELECT * FROM theme WHERE origine = 'tuteur'";
        if (!empty($motcle)) {
            $sql .= " AND titre LIKE ?";
            $stmt = $db->prepare($sql);
            $stmt->execute(['%' . $motcle . '%']);
        } else {
            $stmt = $db->query($sql);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function rechercherAvecPrefixe($q) {
        $db = Database::connect();
        $q = trim($q);

        if (str_starts_with($q, 'd:')) {
            $param = '%' . substr($q, 2) . '%';
            $sql = "SELECT t.*, u.nom AS nom_tuteur, u.prenom AS prenom_tuteur
                    FROM theme t
                    LEFT JOIN tuteur tut ON t.id_tuteur = tut.id_tuteur
                    LEFT JOIN utilisateur u ON tut.id_utilisateur = u.id_utilisateur
                    WHERE t.origine = 'tuteur' AND t.statut = 'Libre'
                    AND t.domaine IS NOT NULL AND t.domaine LIKE ?";
        } elseif (str_starts_with($q, 'c:')) {
            $param = '%' . substr($q, 2) . '%';
            $sql = "SELECT t.*, u.nom AS nom_tuteur, u.prenom AS prenom_tuteur
                    FROM theme t
                    LEFT JOIN tuteur tut ON t.id_tuteur = tut.id_tuteur
                    LEFT JOIN utilisateur u ON tut.id_utilisateur = u.id_utilisateur
                    WHERE t.origine = 'tuteur' AND t.statut = 'Libre'
                    AND t.catalogue IS NOT NULL AND t.catalogue LIKE ?";
        } else {
            $param = '%' . $q . '%';
            $sql = "SELECT t.*, u.nom AS nom_tuteur, u.prenom AS prenom_tuteur
                    FROM theme t
                    LEFT JOIN tuteur tut ON t.id_tuteur = tut.id_tuteur
                    LEFT JOIN utilisateur u ON tut.id_utilisateur = u.id_utilisateur
                    WHERE t.origine = 'tuteur' AND t.statut = 'Libre'
                    AND t.titre LIKE ?";
        }

        $stmt = $db->prepare($sql);
        $stmt->execute([$param]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getStagiaireId($id_utilisateur) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT id_stagiaire FROM stagiaire WHERE id_utilisateur = ?");
        $stmt->execute([$id_utilisateur]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id_stagiaire'] ?? null;
    }

    public static function aDejaPostule($id_stagiaire, $id_theme) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM candidature WHERE id_stagiaire = ? AND id_theme = ?");
        $stmt->execute([$id_stagiaire, $id_theme]);
        return $stmt->fetch() !== false;
    }
public static function getTousLesThemesAvecDetails() {
    $db = Database::connect();
    $stmt = $db->query("
        SELECT 
            t.*, 
            u.nom, 
            u.prenom, 
            COUNT(c.id_candidature) AS nb_candidatures
        FROM theme t
        LEFT JOIN tuteur tut ON t.id_tuteur = tut.id_tuteur
        LEFT JOIN utilisateur u ON tut.id_utilisateur = u.id_utilisateur
        LEFT JOIN candidature c ON t.id_theme = c.id_theme
        GROUP BY t.id_theme
        ORDER BY t.date_proposition DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public static function filtrerParColonnes($filtres) {
    $db = Database::connect();

    $sql = "
        SELECT t.*, u.nom AS nom_tuteur, u.prenom AS prenom_tuteur, COUNT(c.id_candidature) AS nb_candidatures
        FROM theme t
        LEFT JOIN tuteur tut ON t.id_tuteur = tut.id_tuteur
        LEFT JOIN utilisateur u ON tut.id_utilisateur = u.id_utilisateur
        LEFT JOIN candidature c ON t.id_theme = c.id_theme
        WHERE 1 = 1
    ";

    $params = [];

    if (!empty($filtres['titre'])) {
        $sql .= " AND t.titre LIKE ?";
        $params[] = '%' . $filtres['titre'] . '%';
    }
    if (!empty($filtres['domaine'])) {
        $sql .= " AND t.domaine LIKE ?";
        $params[] = '%' . $filtres['domaine'] . '%';
    }
    if (!empty($filtres['propose_par'])) {
        $sql .= " AND CONCAT(u.prenom, ' ', u.nom) LIKE ?";
        $params[] = '%' . $filtres['propose_par'] . '%';
    }
    if (!empty($filtres['statut'])) {
        $sql .= " AND t.statut = ?";
        $params[] = $filtres['statut'];
    }

    $sql .= " GROUP BY t.id_theme ORDER BY t.date_proposition DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public static function getThemesFiltresPourTuteur($filtre = '', $statut = '', $origine = '') {
    $db = Database::connect();

    $sql = "
        SELECT t.*, u.nom, u.prenom, COUNT(c.id_candidature) AS nb_candidatures
        FROM theme t
        LEFT JOIN tuteur tut ON t.id_tuteur = tut.id_tuteur
        LEFT JOIN utilisateur u ON tut.id_utilisateur = u.id_utilisateur
        LEFT JOIN candidature c ON c.id_theme = t.id_theme
        WHERE 1 = 1
    ";

    $params = [];

    if (!empty($filtre)) {
        $sql .= " AND (t.titre LIKE :filtre OR t.domaine LIKE :filtre OR u.nom LIKE :filtre OR u.prenom LIKE :filtre)";
        $params[':filtre'] = '%' . $filtre . '%';
    }

    if (!empty($statut)) {
        $sql .= " AND t.statut = :statut";
        $params[':statut'] = $statut;
    }

    if (!empty($origine)) {
        $sql .= " AND t.origine = :origine";
        $params[':origine'] = $origine;
    }

    $sql .= " GROUP BY t.id_theme ORDER BY t.date_proposition DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public static function getThemesParTuteur($id_utilisateur) {
    $db = Database::connect();
    $stmt = $db->prepare("
        SELECT 
            t.*, 
            u.nom, 
            u.prenom, 
            COUNT(c.id_candidature) AS nb_candidatures
        FROM theme t
        LEFT JOIN tuteur tut ON t.id_tuteur = tut.id_tuteur
        LEFT JOIN utilisateur u ON tut.id_utilisateur = u.id_utilisateur
        LEFT JOIN candidature c ON t.id_theme = c.id_theme
        WHERE tut.id_utilisateur = ?
        GROUP BY t.id_theme
        ORDER BY t.date_proposition DESC
    ");
    $stmt->execute([$id_utilisateur]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public static function filtrerPourTuteur($filtre = '', $statut = '', $origine = '') {
    $db = Database::connect();

    $sql = "
        SELECT t.*, 
            CASE 
                WHEN t.origine = 'tuteur' THEN u_tut.nom 
                WHEN t.origine = 'stagiaire' THEN u_stag.nom 
            END AS nom,
            CASE 
                WHEN t.origine = 'tuteur' THEN u_tut.prenom 
                WHEN t.origine = 'stagiaire' THEN u_stag.prenom 
            END AS prenom,
            COUNT(c.id_candidature) AS nb_candidatures
        FROM theme t
        LEFT JOIN tuteur tut ON t.id_tuteur = tut.id_tuteur
        LEFT JOIN utilisateur u_tut ON tut.id_utilisateur = u_tut.id_utilisateur

        LEFT JOIN stagiaire stag ON t.id_stagiaire = stag.id_stagiaire
        LEFT JOIN utilisateur u_stag ON stag.id_utilisateur = u_stag.id_utilisateur

        LEFT JOIN candidature c ON t.id_theme = c.id_theme
        WHERE 1 = 1
    ";

    $params = [];

    if (!empty($filtre)) {
        $sql .= " AND (t.titre LIKE :filtre OR t.domaine LIKE :filtre)";
        $params[':filtre'] = '%' . $filtre . '%';
    }

    if (!empty($statut)) {
        $sql .= " AND t.statut = :statut";
        $params[':statut'] = $statut;
    }

    if (!empty($origine)) {
        $sql .= " AND t.origine = :origine";
        $params[':origine'] = $origine;
    }

    $sql .= " GROUP BY t.id_theme ORDER BY t.date_proposition DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public static function getThemeAvecDetails($id) {
    $db = Database::connect();
    $stmt = $db->prepare("
        SELECT 
            t.*, u.nom AS nom_tuteur, u.prenom AS prenom_tuteur
        FROM theme t
        LEFT JOIN tuteur tut ON t.id_tuteur = tut.id_tuteur
        LEFT JOIN utilisateur u ON tut.id_utilisateur = u.id_utilisateur
        WHERE t.id_theme = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public static function getById($id) {
    $db = Database::connect();
    $stmt = $db->prepare("
        SELECT 
            t.*,
            u_tut.nom AS nom_tuteur,
            u_tut.prenom AS prenom_tuteur,
            
            u_stag.nom AS nom_stagiaire,
            u_stag.prenom AS prenom_stagiaire,
            u_stag.email AS stagiaire_email,
            
            s.telephone AS stagiaire_telephone,
            s.universite AS stagiaire_universite,
            s.niveau_etude AS stagiaire_niveau,
            s.competences AS stagiaire_competences,
            s.experiences AS stagiaire_experiences,
            s.message_motivation AS stagiaire_motivation

        FROM theme t
        LEFT JOIN tuteur tut ON t.id_tuteur = tut.id_tuteur
        LEFT JOIN utilisateur u_tut ON tut.id_utilisateur = u_tut.id_utilisateur

        LEFT JOIN stagiaire s ON t.id_stagiaire = s.id_stagiaire
        LEFT JOIN utilisateur u_stag ON s.id_utilisateur = u_stag.id_utilisateur

        WHERE t.id_theme = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
public static function getNotificationsPourStagiaire($id_stagiaire) {
    $db = Database::connect();
    
    $stmt = $db->prepare("
        SELECT titre, date_proposition
        FROM theme
        WHERE id_stagiaire = ? 
          AND origine = 'stagiaire'
          AND statut = 'Pris'
        ORDER BY date_proposition DESC
    ");
    $stmt->execute([$id_stagiaire]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public static function getPourTuteurUniquement($idTuteur)
{
    $db = Database::connect();

    $sql = "
        SELECT t.*,
               u.nom AS nom,
               u.prenom AS prenom,
               COUNT(c.id_candidature) AS nb_candidatures
        FROM theme t
        LEFT JOIN tuteur tut ON t.id_tuteur = tut.id_tuteur
        LEFT JOIN utilisateur u ON tut.id_utilisateur = u.id_utilisateur
        LEFT JOIN candidature c ON c.id_theme = t.id_theme
        WHERE tut.id_utilisateur = :id_tuteur
        GROUP BY t.id_theme
        ORDER BY t.date_proposition DESC
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute([':id_tuteur' => $idTuteur]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public static function rechercheAvanceePourTuteur($idTuteur, $filtre = '', $statut = '', $origine = '')
{
    $db = Database::connect();

   
    $sql = "
        SELECT t.*,
            CASE 
                WHEN t.origine = 'tuteur' THEN u_tut.nom 
                ELSE u_stag.nom 
            END AS nom,
            CASE 
                WHEN t.origine = 'tuteur' THEN u_tut.prenom 
                ELSE u_stag.prenom 
            END AS prenom,
            COUNT(c.id_candidature) AS nb_candidatures
        FROM theme t
        LEFT JOIN tuteur tut ON t.id_tuteur = tut.id_tuteur
        LEFT JOIN utilisateur u_tut ON tut.id_utilisateur = u_tut.id_utilisateur
        LEFT JOIN stagiaire st ON t.id_stagiaire = st.id_stagiaire
        LEFT JOIN utilisateur u_stag ON st.id_utilisateur = u_stag.id_utilisateur
        LEFT JOIN candidature c ON t.id_theme = c.id_theme
        WHERE 1=1
    ";

    $params = [];
    if (empty($filtre) && empty($statut) && empty($origine)) {
        $sql .= " AND tut.id_utilisateur = :id_tuteur";
        $params[':id_tuteur'] = $idTuteur;
    }

 
    if (!empty($filtre)) {
        if (stripos($filtre, 'd:') === 0) {
            // Recherche uniquement dans le domaine
            $mot = trim(substr($filtre, 2));
            $sql .= " AND t.domaine LIKE :search";
            $params[':search'] = "%$mot%";
        } else {
            // Recherche globale : titre, domaine, nom, prenom
            $sql .= " AND (
                t.titre LIKE :search
                OR t.domaine LIKE :search
                OR u_tut.nom LIKE :search
                OR u_tut.prenom LIKE :search
                OR u_stag.nom LIKE :search
                OR u_stag.prenom LIKE :search
            )";
            $params[':search'] = "%$filtre%";
        }
    }

  
    if (!empty($statut)) {
        $sql .= " AND t.statut = :statut";
        $params[':statut'] = $statut;
    }

    if (!empty($origine)) {
        $sql .= " AND t.origine = :origine";
        $params[':origine'] = $origine;
    }

    $sql .= " GROUP BY t.id_theme ORDER BY t.date_proposition DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    
}