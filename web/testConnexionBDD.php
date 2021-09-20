<?php

    try
    {
        $bdd = new PDO('mysql:host=continuequhist.mysql.db;dbname=continuequhist', 'continuequhist', '');
    }
    catch (Exception $e)
    {
        die('Erreur : '.$e->getMessage());
    }
    
    $reponse = $bdd->query('SELECT * FROM avisutilisateur');
    
    while ($donnees = $reponse->fetch())
    {
        ?>
        <p> <?php echo $donnees['libelleAvis'] ?> </p>
        <?php
    }
?>