<?php

include('controleur.php');

// Connexion à la base de données
$pdo = connexion(); // Assure-toi que la fonction connexion() est bien définie

// Initialiser Twig
$twig = init_twig();

// Récupérer tous les blocs
$tableau = Article::readAll();

// Afficher le formulaire
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="style.css">

    <title>CMS</title>
</head>
<body>
    
</body>
</html>

