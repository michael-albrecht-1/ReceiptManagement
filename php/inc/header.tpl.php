<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/style.css">
    <title>Gestionnaire de tickets</title>
</head>
<body>
    <div class="container">
        <?php 
            // Initialiser la session
            session_start();
            if (isset($_SESSION['username'])):
        ?>       
             <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
                <div class="collapse navbar-collapse" id="navbarColor01">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item active">
                            <a class="nav-link" href="index.php">Liste des tickets
                                <span class="sr-only">(current)</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="receipt.php">Ajout d'un ticket</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="export.php">Export</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $_SESSION['username']; ?></a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="logout.php">Déconnexion</a>
                                <?php if ($_SESSION['username'] == "admin"): ?>
                                    <a class="dropdown-item" href="register.php">Créer un nouveau compte</a>
                                <?php endif ?>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>   

            
        <?php endif ?>
        