<?php
    require __DIR__ . '/inc/header.tpl.php';
    require __DIR__ . '/inc/functions.php';
    require __DIR__ . '/config.php';
    require __DIR__ . '/inc/data.php';

    // Vérifiez si l'utilisateur est connecté, sinon redirigez-le vers la page de connexion
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
    }
?>


<h1>Liste des tickets</h1>

<?php 

    
    $req = "SELECT * FROM receipts";

    if ( isset($_GET['filter']) ) 
    {
        if ( isset($_GET['isChecked']) )
        if ( count($_GET['isChecked']) == 1) {
            $req = $req . " WHERE checked = " . $_GET['isChecked'][0];
        } elseif (count($_GET['isChecked']) == 2) {
            $req = $req . " WHERE checked = " . $_GET['isChecked'][0] . " or " . "checked = " . $_GET['isChecked'][1]; 
        }
    }

    $showReceipts = mysqli_query($conn, $req);

?>
<table class="table table-hover">
    <form method="get" action="">
        <div class="row">
            <div class="form-group isChecked">
                <label for="ischecked">Pointé</label>
                <select multiple size = 2 class="form-control" name="isChecked[]">
                    <option value="true">oui</option>
                    <option value="false">non</option>
                </select>
            </div>

            <button type="submit" name="filter" class="btn btn-primary submitListReceiptFilters">Valider</button>
        </div> 
    </form>
<thead>
    <tr class="table-dark">
      <th scope="col">Date</th>
      <th scope="col">Type</th>
      <th scope="col">Fournisseur</th>
      <th scope="col">TVA</th>
      <th scope="col">TTC</th>
      <th scope="col">Pointé</th>
      <th scope="col">Description</th>
      <th scope="col"></th>
    </tr>
  </thead>
<?php
    while ($row = mysqli_fetch_array($showReceipts)) {
        
        foreach ($receiptTypes as $index => $type) {
            if ($index == $row['type']) {
                $receiptType = $type;
            }
        }

        // TVA
        if ($row['tva'] =='tva1') 
        {
            $tva = "0";
        } elseif ($row['tva'] =='tva2')
        {
            $tva = "5.5";
        } elseif ($row['tva'] =='tva3')
        {
            $tva = "10";
        }elseif ($row['tva'] =='tva4')
        {
            $tva = "20";
        }

        // TTC amount
        $fmt = new NumberFormatter( 'de_DE', NumberFormatter::CURRENCY );
        $amount = $fmt->formatCurrency($row['montant_ttc'], "EUR");

        // Is checked ?
        $row['checked'] ? $isChecked = "oui" : $isChecked = "non"; 

        // description
        $description = truncate($row['description'], 40);
        
        echo "<tr>";
            echo "<td>" . $row['date_emission'] . "</td>";
            echo "<td>" . $receiptType . "</td>";
            echo "<td>" . $row['fournisseur'] . "</td>";
            echo "<td>" . $tva . "</td>";
            echo "<td>" . $amount . "</td>";
            echo "<td>" . $isChecked . "</td>";
            echo "<td>" . $row['description'] . "</td>";
            // le lien ne fonctionne pas.
            echo "<td>
                <a href=\"receipt.php" .
                "?id=" . $row['id'] .
                "&photo=" . $row['photo'] .
                "&date=" . $row['date_emission'] .
                "&type=" . $receiptType .
                "&fournisseur=" . $row['fournisseur'] .
                "&tva=" . $tva .
                "&amount=" . $row['montant_ttc'] .
                "&isChecked=" . $isChecked .
                "&description=" . $description .
                "\">x</a>
            </td>";
            
        echo "</tr>";
    }
?>
</table>

<script src="../js/receiptList.js"></script>
<?php
    require __DIR__ . '/inc/footer.tpl.php';
?>
