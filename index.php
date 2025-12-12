<?php
    session_start();

    // Réinitialiser uniquement si on clique sur "Retour à l'accueil" (paramètre "reset=1")
    if (isset($_GET['reset'])) {
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['cles'] = 0;
        $_SESSION['score'] = 0;
    }

    // Initialiser les variables de session si elles n'existent pas
    if (!isset($_SESSION['cles'])) {
        $_SESSION['cles'] = 0;
    }
    if (!isset($_SESSION['score'])) {
        $_SESSION['score'] = 0;
    }

    // Paramètres
    $bdd_fichier = 'labyrinthe.db';

    // Récupérer le couloir actuel depuis l'URL
    $couloir_actuel = isset($_GET['couloir']) ? $_GET['couloir'] : '13';

   // Incrémenter le score uniquement si on change de couloir ET que ce n'est pas le premier chargement
if (isset($_GET['couloir']) && isset($_SESSION['dernier_couloir']) && $_GET['couloir'] != $_SESSION['dernier_couloir']) {
    $_SESSION['score']++;
}
$_SESSION['dernier_couloir'] = $couloir_actuel; // Mettre à jour le dernier couloir visité


    // Ouverture de la base de données
    $sqlite = new SQLite3($bdd_fichier);
    if (!$sqlite) {
        die("Impossible d'ouvrir la base de données SQLite.");
    }
?>





<?php
    // Récupérer les passages accessibles depuis le couloir actuel
    $sql = "
        SELECT c2.id, p.type, p.position1, p.position2
        FROM passage p
        JOIN couloir c1 ON p.couloir1 = c1.id
        JOIN couloir c2 ON p.couloir2 = c2.id
        WHERE p.couloir1 = :couloir_actuel
        UNION
        SELECT c1.id, p.type, p.position2, p.position1
        FROM passage p
        JOIN couloir c1 ON p.couloir1 = c1.id
        JOIN couloir c2 ON p.couloir2 = c2.id
        WHERE p.couloir2 = :couloir_actuel
    ";

    $requete = $sqlite->prepare($sql);
    $requete->bindValue(':couloir_actuel', $couloir_actuel, SQLITE3_INTEGER);
    $result = $requete->execute();
?>


<?php
    // Vérifier si le joueur récupère une clé (couloirs 3 ou 16)
    if ($couloir_actuel == 3 || $couloir_actuel == 16) {
        if (!isset($_SESSION['cle_' . $couloir_actuel])) {
            $_SESSION['cles']++;
            $_SESSION['cle_' . $couloir_actuel] = true;
            $message_cle = "Vous avez trouvé une clé ! Total : " . $_SESSION['cles'];
        }
    }

    // Vérifier si le joueur a atteint la sortie (couloir 26)
    $fin_du_jeu = ($couloir_actuel == 26);
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Labyrinthe - Couloir <?php echo htmlspecialchars($couloir_actuel); ?></title>
    <link rel="stylesheet" href="style.css">
    
</head>
<body>
  
    <div class="container"> 
        <h1>Labyrinthe</h1>

    <?php
    // Récupérer le type du couloir actuel
    $sql_type = "SELECT type FROM couloir WHERE id = :id";
    $req_type = $sqlite->prepare($sql_type);
    $req_type->bindValue(':id', $couloir_actuel, SQLITE3_INTEGER);
    $res_type = $req_type->execute();
    $type_couloir = $res_type->fetchArray(SQLITE3_ASSOC)['type'];
    ?>

    <h2>Vous êtes dans le couloir <?php echo htmlspecialchars($couloir_actuel); ?>
        (<?php echo htmlspecialchars($type_couloir); ?>)
    </h2>

    <?php if (isset($message_cle)): ?>
        <p class="message"><?php echo $message_cle; ?></p>
    <?php endif; ?>

    <?php if ($fin_du_jeu): ?>
        <p class="fin">Félicitations, vous avez trouvé la sortie !</p>
        <p>Votre score : <?php echo $_SESSION['score']; ?> déplacements.</p>
        <p><a href="?reset=1">Recommencer une partie</a></p>
    <?php else: ?>
        <p>Clés en votre possession : <?php echo $_SESSION['cles']; ?></p>
        <p>Nombre de déplacements : <?php echo $_SESSION['score']; ?></p>

        <?php if (!$result): ?>
            <p>Aucun passage accessible.</p>
        <?php else: ?>
            <h3>Passages accessibles :</h3>
            <ul>
                <?php while ($passage = $result->fetchArray(SQLITE3_ASSOC)): ?>
                    <?php
                    $couloir_suivant = $passage['id'];
                    $type_passage = $passage['type'];
                    $direction = ($passage['couloir1'] == $couloir_actuel) ? $passage['position2'] : $passage['position1'];
                    
                    ?>
                    <li>
                        <?php if ($type_passage == 'grille' && $_SESSION['cles'] <= 0): ?>
                            Passage vers le couloir <?php echo htmlspecialchars($couloir_suivant); ?>
                            (direction : <?php echo $direction; ?>) - <strong>Grille verrouillée</strong> (il vous faut une clé)
                        <?php else: ?>
                            <a href="?couloir=<?php echo urlencode($couloir_suivant); ?>">
                                Aller vers le couloir <?php echo htmlspecialchars($couloir_suivant); ?>
                                (direction : <?php echo $direction; ?>)
                                <?php if ($type_passage != 'libre'): ?>
                                    - Passage de type : <?php echo $type_passage; ?>
                                <?php endif; ?>
                            </a>
                        <?php endif; ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php endif; ?>
        <p><a href="debut.php">Retour à l'accueil</a></p>

    <?php endif; ?>
</div>
</body>
</html>

<?php $sqlite->close(); ?>



