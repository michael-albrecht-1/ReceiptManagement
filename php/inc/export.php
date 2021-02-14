    <?php 
        session_start();
        $excel = "";
        $excel .=  "Id\tDate\tCategorie\tTVA\tFournisseur\tMontantTTC\tPointé\tDescription\n";
        
        foreach($_SESSION['receiptsToExport'] as $row) {
            $excel .= "1\n";
            // $excel .= "$row['id']\t$row['date_emission']\t$row['category']\t$row['tva']\t$row['provider']\t$row['montant_ttc']\t$row['checked']\t$row['description']\n";
        }
        
        header("Content-type: application/vnd.ms-excel");
        header("Content-disposition: attachment; filename=export.xls");
        
        print $excel;
        exit;