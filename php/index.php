<?php
    require __DIR__ . '/inc/header.tpl.php';
    require __DIR__ . '/inc/functions.php';
    require __DIR__ . '/config.php';

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
        <div class="form-group row">
            <label for="ischecked">Pointé</label>
            <select multiple size = 2 class="form-control" name="isChecked[]">
                <option value="true">oui</option>
                <option value="false">non</option>
            </select>
        </div>

        <button type="submit" name="filter" class="btn btn-primary">Valider</button>
    </form>
<thead>
    <tr class="table-dark">
      <th scope="col">Date</th>
      <th scope="col">Type</th>
      <th scope="col">TVA</th>
      <th scope="col">TTC</th>
      <th scope="col">Pointé</th>
      <th scope="col">Description</th>
    </tr>
  </thead>
<?php
    while ($row = mysqli_fetch_array($showReceipts)) {
        
        // type
        switch ($row['type']) {
            case '1':
                $type = "restaurant";
                break;
            case '2':
                $type = "gasoil";
                break;
            case '3':
                $type = "hôtel";
                break;
            case '4':
                $type = "péage";
                break;
            case '5':
                $type = "autre";
                break;
            default:
                $type = "autre";
                break;
        }

        // TVA
        if ($row['tva'] =='tva1') 
        {
            $tva = "5.5";
        } elseif ($row['tva'] =='tva2')
        {
            $tva = "10";
        } elseif ($row['tva'] =='tva3')
        {
            $tva = "20";
        }

        // Is checked ?
        $row['checked'] ? $isChecked = "oui" : $isChecked = "non"; 

        // description
        $description = truncate($row['description'], 40);
        
        echo "<tr>";
            echo "<td>".$row['date_emission']."</td>";
            echo "<td>".$type."</td>";
            echo "<td>".$tva."</td>";
            echo "<td>".$row['montant_ttc']."</td>";
            echo "<td>".$isChecked."</td>";
            echo "<td>".$description."</td>";
        echo "</tr>";
    }
?>
</table>


<?php
    require __DIR__ . '/inc/footer.tpl.php';
?>
