# ReceiptManagement
Application de saisie de tickets de caisse et d'export en comptabilité.

## To DO


## Fonctionnalitées
    - liste des tickets affiche un tableau avec la date, la TVA, le TTC, si les tickets ont été points ou pas et la description des tickets
    - la page "ajout d'un ticket" permet de prendre en photo un ticket puis de renseigner son détail
### Authentification

    - il faut être authentifié pour accéder à l'application
    - les comptes sont créés uniquement par le compte "admin" dans la barre de navigation

## Installer l'application
    - cloner le repo
    - créer un fichier dans le projet *php/config.php*

```PHP
    <?php
    // FICHIER gitignore dans .git/info/exclude
    // Informations d'identification
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'USER');
    define('DB_PASSWORD', 'PASSWORD');
    define('DB_NAME', 'receiptmanagement');

    // Connexion à la base de données MySQL 
    $conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Vérifier la connexion
    if($conn === false){
        die("ERREUR : Impossible de se connecter. " . mysqli_connect_error());
    }
    ?>
```

    - lors de la première connexion il on vous demande de créer le compte "admin"

## Techno utilisées 
- PHP
- MySQL
- Bootstrap [Sketchy Theme](https://bootswatch.com/sketchy/)


