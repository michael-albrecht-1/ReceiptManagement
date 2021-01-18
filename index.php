<?php
    require __DIR__ . '/php/inc/header.tpl.php';
    require __DIR__ . '/php/inc/functions.php';
    require __DIR__ . '/php/config.php';
    require __DIR__ . '/php/inc/data.php';
    
    // Vérifiez si l'utilisateur est connecté, sinon redirigez-le vers la page de connexion
    if (!isset($_SESSION['username'])) {
        header("Location: php/login.php");
    }


    if (isset($_GET['page'])) {
        switch ($_GET['page']) {
            case 1:
                require __DIR__ . '/php/receipt.php';
                break;
            case 2:
                require __DIR__ . '/php/receiptList.php';
                break;
            case 3:
                require __DIR__ . '/php/logout.php';
                break;
            case 4:
                require __DIR__ . '/php/register.php';
                break;
            
            default:
                require __DIR__ . '/php/receipt.php';
                break;
        }
    } else {
        require __DIR__ . '/php/receipt.php';
    }

    
    require __DIR__ . '/php/inc/footer.tpl.php';