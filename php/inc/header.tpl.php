<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">

    
    <title>Stop tickets</title>
</head>
<body>
    <div class="container">
        <?php 
            // Initialiser la session
            session_start();
            if (isset($_SESSION['username'])):
        ?>       
             <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
                <div class="container-fluid">
            
                    <a class="navbar-brand" href="index.php">Ajout d'un ticket</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?page=receiptList">Liste des tickets</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $_SESSION['username']; ?></a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="index.php?page=logout">Déconnexion</a>
                                    <?php if ($_SESSION['username'] == "admin"): ?>
                                        <a class="dropdown-item" href="index.php?page=register">Créer un nouveau compte</a>
                                    <?php endif; ?>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>   



            
        <?php endif; ?>
        <div class="container">
        