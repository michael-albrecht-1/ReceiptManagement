# ReceiptManagement
Application de saisie de tickets de caisse et d'export en comptabilité.

## To DO

- ajouter des filtres de date sur la page de liste des tickets ?
- export CSV : DATE // TYPE // FOURNISSEUR // MONTANT HT // TAUX TVA // MONTANT TVA // MONTANT TTC // VALIDATION FINAL?

## Fonctionnalitées

### Authentification

- il faut être authentifié pour accéder à l'application
- les comptes sont créés uniquement par le compte "admin" dans la barre de navigation

### Liste des tickets

- affiche un tableau avec la date d'emission du ticket, le type de ticket (restaurant, gasoil, etc..), le fournisseur, le taux de TVA, le total TTC, si les tickets ont été pointés ou pas et une description optionnelle
- on peut si ils sont pointés ou non (par défaut on affiche tout)
- un bouton pointer renvoie vers le détail du plus ancien ticket non pointé
- un bouton bleu "pointer" permet d'afficher le ticket le plus ancien avec le plus petit id qui n'est pas pointé 

### Ajout/modification d'un ticket

- prendre en photo un ticket puis de renseigner son détail
- quand on valide le formulaire on peut directement enchainer avec un autre ticket
- lors de la saisie de plusieurs tickets consécutifs il conserve le dernier type choisi
- en fonction du type choisi l'application préselectionne un taux de TVA (RESTAURANT->10 / GASOIL->20 / HOTEL->TVA20/PEAGE->INTRA / AUTRE 20)
- un bouton bleu "Pointer et suivant" permet de passer un ticket en pointé, d'enregistrer cette modification puis d'appeler le plus ancien avec le plus petit id qui n'est pas pointé 

## Installer l'application
- cloner le repo
- importer les tables dans la base de donnée à l'aide de resources/receiptmanagement.sql
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


