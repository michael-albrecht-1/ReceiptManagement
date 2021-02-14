    <?php 
        session_start();
        $excel = "\ndate_emission\tjournal_achat\t\t\tfournisseur\tcredit\tdebit\n";
        
        foreach($_SESSION['receiptsToExport'] as $row) {
            
            switch ($row['category']) {
                case '0':
                    $category = '0RESTA';
                    break;
                case '1':
                    $category = '0CARBU';
                    break;
                case '2':
                    $category = 'hôtel';
                    break;
                case '3':
                    $category = 'péage';
                    break;
                case '4':
                    $category = 'autre';
                    break;
            }
            
            $excel .= 
                $row['date_emission'] . "\t" .              // row 1 col1
                "ac\t" .                                    // col2
                "" . "\t" .                          // col3
                "" . "\t" .                        // col4
                $row['provider'] . "\t" .                   // col5
                "" . "\t" .                // col6
                "" . "\n" .                    // col7
                $row['date_emission'] . "\t" .              // row2
                "ac\t" .                                    // col2
                "" . "\t" .                          // col3
                "" . "\t" .                        // col4
                $row['provider'] . "\t" .                   // col5
                "" . "\t" .                // col6
                "" . "\n" .                    // col7
                $row['date_emission'] . "\t" .              // row3 col1
                "ac\t" .                                    // col2
                "" . "\t" .                          // col3
                "" . "\t" .                        // col4
                $row['provider'] . "\t" .                   // col5
                "" . "\t" .                // col6
                "" . "\n";                    // col7
        }
        
        header("Content-type: application/vnd.ms-excel");
        header("Content-disposition: attachment; filename=export.xls");
        
        print $excel;
        exit;