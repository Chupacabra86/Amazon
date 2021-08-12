# README

## NOUVEAU PROJET

- créer le projet avec composer :
composer create-project symfony/website-skeleton nom_du_projet

## GIT

- créer un dépot git sur GITHUB
- git init
- git remote add origin https://url_du_depot (https://github.com/davidHurtrel/sublimmo.git)
- git add .
- git commit -m "message_du_commit"
- git pull origin master
- git push origin master
- git log

## RECUPERER UN PROJET

- télécharger le zip ou faire un pull
- recréer le fichier .env à la racine du projet
- infos importantes : APP_ENV, APP_SECRET, DATABASE_URL, MAILER_URL
- mettre à jour le projet avec : "composer install" ou "composer update"

## PREPARATION PROJET

- installation apache-pack (composer require symfony/apache-pack) : barre de debug / routing / .htaccess
- profiler-pack (composer require --dev symfony/profiler-pack)
- creation home page :  php bin/console make:controller home (création controller et template "home")
- dans .env : DATABASE_URL="mysql://root@127.0.0.1:3306/sublimmo?serverVersion=5.7"
- création de la database : php bin/console doctrine:database:create
- vérifier les routes : php bin/console debug:router

## CREER UNE ENTITE

- créer une table : php bin/console make:entity
- modifier une table : php bin/console make:entity (puis entrer le nom de la table à modifier)
- clés étangères : ajouter une property (modifier comme au dessus); puis : field type = relation; puis nommer la classe avec laquelle on veut se lier (nom de la table à lier, première lettre en majuscule); choisir Many to one (selectionner le cas voulu); null ou pas; yes pour pouvoir accéder (simplification) ;''; puis est ce qu'on veut supprimer toutes les maisons lors de la suppression d'un utilisateur
- si nécessaire, dans le formulaire rajouter un nouveau champs à remplir :
        ->add('owner', EntityType::class, [
            'label' => 'propriétaire',
            'class' => 'User::class,
            'choice_label' => 'email'
        ]

- préparer la migration : php bin/console make:migration
- exécuter la migration : php bin/console doctrine:migrations:migrate
- supprimer la base de données : php bin/concole d:d:d --force 

## FIXTURES (en principe pour des jeux de fausses données)

- installer le bundle : composer require --dev doctrine/doctrine-fixtures-bundle ( ou composer require --dev orm-fixtures)
- compléter le fichier src/DataFixtures/AppFixtures (puis persist et flush)
- écraser la bdd : php bin/console doctrine:fixtures:load
- ajout à la bdd : php bin/console doctrine:fixtures:load --append
- bundle pour générer des fausses données : composer require fakerphp/faker

## FILTRES TWIG

- les filtres s'utilisent avec un pipe |
- extension utile : composer require twig/string-extra

## COMMANDES IMPORTANTES

- vider le cache : php bin/console cache:clear
- afficher les routes : php bin/console debug:router
- vérifier si une route existe : php bin/console router:match/url_de_la_route
- requete depuis le terminal : php bin/console doctrine:query:sql "la_requete_sql"

## CREER UNE NOUVELLE PAGE

- php bin/console make:controller contact (crée le controller et le template)

## EMAIL / FORMULAIRE

- création d'un nouveau mot de passe d'application dans Chrome
- extension utile : composer require symfony/swiftmailer-bundle
- ajout de l'adresse dans .env
- créer le formulaire :  php bin/console make:form (possibilité de le lier à une table comme 'maisons')
- importer la classe (clic droit dans le controller)
- créer le template du mail (contact/emailContact.html.twig)

## CREER UN CRUD

- php bin/console make:crud Maison

## LOGIN

- créer une entité USER : php bin/console make:user (puis appuyer 4x sur entrer)(puis make:migration, puis migrate)
- créer l'authentification : php bin/console make:auth (choisir 1)
- puis modifier src/security/Authentification... ligne 55 (pas de mail, app-login)

## REGISTER

- créer le formulaire d'inscription : php bin/console make registration-form
- créer un ADMIN lors de la première registration : dans User.php : private $roles = ['ROLE_ADMIN'];
- gérer la force du password : composer require rollerworks/password-strength-bundle

bundle espace administrateur fait d'un coup à nous montrer ! (easyadmin)
gestion du panier : https://www.youtube.com/watch?v=_tWL-QDFuQ4&t=1920s

## PAIEMENT

création d'un compte sur stripe
https://stripe.com/docs/api
composer require stripe/stripe-php

