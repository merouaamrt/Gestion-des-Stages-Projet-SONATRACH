<?php
require_once 'config/database.php'; 

try {
    $db = Database::connect();

    $nom = 'Amirat';
    $prenom = 'Meroua';
    $email = 'm.amirat@univ-lyon2.fr';
    $mot_de_passe = 'admin123'; // Le mot de passe 
    $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
    $type = 'admin';

    // Vérification 
    $stmt = $db->prepare("SELECT * FROM utilisateur WHERE email = ? AND type = 'admin'");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        echo "✅ Un compte admin existe déjà.";
    } else {
        $insert = $db->prepare("INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, type) VALUES (?, ?, ?, ?, ?)");
        $insert->execute([$nom, $prenom, $email, $hash, $type]);
        echo "✅ Admin ajouté avec succès !<br>Email : $email<br>Mot de passe : $mot_de_passe";
    }
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage();
}

