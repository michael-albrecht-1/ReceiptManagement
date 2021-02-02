<?php
    require __DIR__ . '/php/inc/header.tpl.php';
    require __DIR__ . '/php/inc/functions.php';
    require __DIR__ . '/php/classes/DBService.php';
    require __DIR__ . '/php/classes/AuthService.php';
    require __DIR__ . '/php/classes/ReceiptService.php';
    require __DIR__ . '/php/inc/data.php';
    
    $authService = new AuthService();
    $receiptService = new ReceiptService();
    
    if (!isset($_SESSION['username'])) {
        require __DIR__ . '/php/login.php';
    } else {
        $page = filter_input(INPUT_GET, 'page');

        if ( $page === null) {
            $page = 'receipt';
        }
        require __DIR__ . '/php/' . $page . '.php';
    }
    
    require __DIR__ . '/php/inc/footer.tpl.php';