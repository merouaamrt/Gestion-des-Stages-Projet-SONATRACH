<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>📝 Compléter votre profil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }
        h2 {
            color: #2c3e50;
        }
        form {
            max-width: 500px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }
        textarea, input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }
        button {
            margin-top: 20px;
            background-color: #2ecc71;
            color: white;
            padding: 10px 20px;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color: #27ae60;
        }
    </style>
</head>
<body>

    <h2>📝 Compléter votre profil</h2>

    <form method="post" action="index.php?page=profil_handler">
        <label>Téléphone :</label>
        <input type="text" name="telephone" required>

        <label>Niveau d'étude :</label>
        <input type="text" name="niveau_etude" required>

        <label>Expériences :</label>
        <textarea name="experiences" rows="4" placeholder="Parlez de vos projets, stages, etc."></textarea>

        <label>Compétences techniques :</label>
        <textarea name="competences" rows="4" placeholder="Ex : PHP, Laravel, HTML, CSS..."></textarea>

        <label>📩 Renforcez votre candidature</label>
        <textarea name="motivation" rows="5" placeholder="Bonjour, je suis motivé(e) par ce stage car..."></textarea>

        <button type="submit">✅ Enregistrer le profil</button>
    </form>

</body>
</html>

