La page de connexion est index.php.
La page d'accueil est Controlleur/Accueil.php 

Une page dans le dossier Controlleur/ appelle une page du même nom dans Vue/
Le bandeau en haut est Vue/Common/Header.php

En cas de problème avec les requêtes une erreur est remontée, celle-ci donne le nom de la fonction qui a posé problême dans Gateway.php

Le script qui gère les champs dynamiques est js/add_field.js
La barre de progression moissons/progress.js
Pour les barres d'entités/propriétés filtres_traductions/entities.js
Pour l'affichage dynamique d'association configuration/règle c'est filtres_traductions/select.js
Pour le trie des destination sur la page TranslationRules c'est select-destination.js
L'affichage des prédicats se fait via filtres_traductions/predicate.js et l'affichage de l'arbre via filtres_traductions/filter_rule.js
