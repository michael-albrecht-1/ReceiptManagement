    <?php 
        session_start();

        $receiptNumber = 1;
        


        $excel = "\ndate_emission\tjournal_achat\tcompte\tnumero\tfournisseur\tcredit\tdebit\n";
        
        foreach($_SESSION['receiptsToExport'] as $row) {
            $date_emission = date("m", strtotime($row['date_emission']));
            $optionnalZero = $receiptNumber > 9 ? '' : '0';
            $pieceNumber = $optionnalZero . $receiptNumber++ . '.' . $date_emission;

            switch ($row['tva']) {
                case 'tva1':
                    $tauxTVA = 0;
                    break;
                case 'tva2':
                    $tauxTVA = 5.5;
                    break;
                case 'tva3':
                    $tauxTVA = 10;
                    break;
                case 'tva4':
                    $tauxTVA = 20;
                    break;
                
                default:
                    $tauxTVA = 0;
                    break;
            }

            $tvaAmount = $row['montant_ttc'] * $tauxTVA / 100;
            $credit = $row['montant_ttc'] - $tvaAmount;
           
            switch ($row['category']) {
                case '0':
                    $providerAccount = '0RESTA';
                    $companyAccount = '625600';
                    break;
                case '1':
                    $providerAccount = '0CARBU';
                    $companyAccount = '606140';
                    break;
                case '2':
                    $providerAccount = 'HOTEL';
                    $companyAccount = '';
                    break;
                case '3':
                    $providerAccount = 'PEAGE';
                    $companyAccount = '';
                    break;
                case '4':
                    $providerAccount = 'AUTRE';
                    $companyAccount = '';
                    break;
            }

            if ($row['checked'] == true) {
                //! TTC            
                $excel .= 
                    $row['date_emission'] . "\t" .             
                    "ac\t" .                                 
                    $providerAccount . "\t" .                         
                    $pieceNumber . "\t" .                        
                    $row['provider'] . "\t" .                   
                    "0" . "\t" .              
                    $row['montant_ttc'] . "\n";   

                //! TVA if the receipt isnt EXO
                if ($row['tva'] != 'tva1') {           
                    $excel  .=                    
                        $row['date_emission'] . "\t" .              
                        "ac\t" .                                  
                        "44566" . "\t" .                        
                        $pieceNumber . "\t" .                       
                        $row['provider'] . "\t" .                  
                        $tvaAmount . "\t" .             
                        "0" . "\n";                    
                    } 
                
                // ! HT
                $excel .=
                    $row['date_emission'] . "\t" .             
                    "ac\t" .                                   
                    $companyAccount . "\t" .                         
                    $pieceNumber . "\t" .                     
                    $row['provider'] . "\t" .                 
                    $credit . "\t" .              
                    "0" . "\n";        
            } 

        }
        
        header("Content-type: application/vnd.ms-excel");
        header("Content-disposition: attachment; filename=export.xls");
        
        print $excel;
        exit;