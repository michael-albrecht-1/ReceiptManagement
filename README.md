# ReceiptManagement
Application de saisie de tickets de caisse et d'export en comptabilité.

## To DO

- afficher la valeur totale de TVA concerrespondant à la sélection dans la liste des tickets

## Fonctionnalitées

### Authentification

- il faut être authentifié pour accéder à l'application
- les comptes sont créés uniquement par le compte "admin" dans la barre de navigation

### Liste des tickets

#### Filtrer les tickets à afficher

Un tableau avec la juste des tickets avec leur date d'emission, leur type (restaurant, gasoil, etc..), le fournisseur, le taux de TVA, le total TTC, si les tickets ont été pointés ou pas et une description optionnelle

On peut les filtrer par date ou si ils sont pointés.

#### Pointage

Un bouton bleu "pointer" permet d'afficher le ticket le plus ancien avec le plus petit id qui n'est pas pointé.

#### Export comptable

Cette fonctionnalité permet d'exporter un fichier au format *xls*. Un ticket doit être pointé pour pouvoir être exporté. Le fichier contient 7 colonnes :

- date-emission : date d'émission du ticket
- journal_achat : journal d'achat concerné
- compte : le compte comptable concerné (plus de détails plus bas)
- numero : le numéro de pièce qui est formaté de la manière suivante : "numéroTicketExporté"."moisEmission"
- fournisseur : le nom de l'entreprise émetrice du ticket
- credit : le montant si c'est une ligne de crédit
- debit :  le montant si c'est une ligne de débit

Chaque ticket donne lieu plusieurs lignes lors de l'export :
- une ligne de débit sur le compte du fournisseur avec le montant TTC payé
- une ligne pour la TVA qui n'est présente *que* si le ticket n'est pas exonéré
- une ligne de crédit sur le compte de comptable associé au type de dépense avec le montant HT

Les correspondances sont configurables dans le fichier *export.php*.

### Ajout/modification d'un ticket

- prendre en photo un ticket puis de renseigner son détail
- quand on valide le formulaire on peut directement enchainer avec un autre ticket
- lors de la saisie de plusieurs tickets consécutifs il conserve le dernier type choisi
- en fonction du type choisi l'application préselectionne un taux de TVA (RESTAURANT->10 / GASOIL->20 / HOTEL->TVA20/PEAGE->INTRA / AUTRE 20)
- un bouton bleu "Pointer et suivant" permet de passer un ticket en pointé, d'enregistrer cette modification puis d'appeler le plus ancien avec le plus petit id qui n'est pas pointé 

## Installer l'application
- cloner le repo
- importer les tables dans la base de donnée à l'aide de r*esources/receiptmanagement.sql*
- copier/coller le fichier *php/inc/configDB.dist.php* dans le même répertoire et appeler la copie *configDB.php*. Le modifier avec les infos de connexion à votre base
- lors de la première connexion il on vous demande de créer le compte "admin"

## Techno utilisées 
- PHP
- MySQL
- Bootstrap [Sketchy Theme](https://bootswatch.com/sketchy/)


