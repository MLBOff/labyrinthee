<?php
    session_start();
    // Réinitialiser la session au lancement du jeu
    session_unset();
    session_destroy();
    session_start();
    $_SESSION['cles'] = 0;
    $_SESSION['score'] = 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Labyrinthe - Accueil</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Style spécifique pour la page d'accueil */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            background-color: white;
            border: 2px solid #333;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 80%;
            max-width: 500px;
            text-align: center;
        }

        .bouton-lancer {
            background-color: #4CAF50;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        .bouton-lancer:hover {
            background-color: #45a049;
        }

        .titre {
            margin-bottom: 20px;
            color: #333;
        }

        .description {
            margin-bottom: 20px;
            color: #555;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="titre">Bienvenue dans le Labyrinthe</h1>
        <p class="description">
            Trouvez la sortie du labyrinthe en explorant les couloirs et en collectant des clés.
            Certaines portes sont verrouillées et nécessitent des clés pour être ouvertes.
            Votre objectif est de sortir en un minimum de déplacements !
        </p>
        <button class="bouton-lancer" onclick="window.location.href='index.php?couloir=13'">
            Commencer la partie
        </button>
    </div>
</body>
</html>

