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


    $total_records_per_page = 6;

    // get the current page number
    if (isset($_GET['page_no']) && $_GET['page_no']!="") {
    $page_no = $_GET['page_no'];
    } else {
        $page_no = 1;
        }


    $offset = ($page_no-1) * $total_records_per_page;
    $previous_page = $page_no - 1;
    $next_page = $page_no + 1;
    $adjacents = "2";

    // 
    $result_count = mysqli_query(
    $conn,
    "SELECT COUNT(*) As total_records FROM `receipts`"
    );
    $total_records = mysqli_fetch_array($result_count);
    $total_records = $total_records['total_records'];
    $total_no_of_pages = ceil($total_records / $total_records_per_page);
    $second_last = $total_no_of_pages - 1; // total pages minus 1


    // build request------------------------
    if ( isset($_GET['filter']) ) 
    {
        if ( isset($_GET['isChecked']) )
        if ( count($_GET['isChecked']) == 1) {
            $checkedSQL = $_GET['isChecked'][0];
        } elseif (count($_GET['isChecked']) == 2) {
            $checkedSQL = $_GET['isChecked'][0] . " OR " . "checked = " . $_GET['isChecked'][1]; 
        }
        $req = "SELECT * FROM `receipts` WHERE checked=$checkedSQL ORDER BY `date_emission` DESC, `id` DESC LIMIT $offset, $total_records_per_page";
    } else {
        $req = "SELECT * FROM `receipts` ORDER BY `date_emission` DESC, `id` DESC LIMIT $offset, $total_records_per_page";
    }

    
    $result = mysqli_query($conn, $req);
    // ---------------------------

    while ($row = mysqli_fetch_array($result)) { 

        // format values
        $fmt = new NumberFormatter( 'de_DE', NumberFormatter::CURRENCY );
        $amount = $fmt->formatCurrency($row['montant_ttc'], "EUR");
        $row['checked'] ? $isChecked = "oui" : $isChecked = "non"; 
        $description = truncate($row['description'], 40);

        // format categories --- HS
        foreach ($receiptCategories as $index => $category) {
            if ($index == $row['category']) {
                $receiptCategory = $category;
            } else {
                $receiptCategory = "";
            }
        }

        // format TVA
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
        
        // format values end
     

        echo "<tr>";
            echo "<td>" . $row['date_emission'] . "</td>";
            echo "<td>" . $receiptCategory . "</td>";
            echo "<td>" . $row['provider'] . "</td>";
            echo "<td>" . $tva . "</td>";
            echo "<td>" . $amount . "</td>";
            echo "<td>" . $isChecked . "</td>";
            echo "<td>" . $row['description'] . "</td>";
            echo "<td>
                <a href=\"receipt.php" .
                "?id=" . $row['id'] .
                "&photo=" . $row['photo'] .
                "&date=" . $row['date_emission'] .
                "&receiptCategory=" . $receiptCategory .
                "&provider=" . $row['provider'] .
                "&tva=" . $tva .
                "&amount=" . $row['montant_ttc'] .
                "&isChecked=" . $isChecked .
                "&description=" . $description .
                "\">x</a>
            </td>";
            
        echo "</tr>";

        
    }
    mysqli_close($conn);


?> 

</table>

<div>
    <strong>Page <?php echo $page_no." of ".$total_no_of_pages; ?></strong>
</div>

<ul>
    <?php if($page_no > 1){
    echo "<li><a href='?page_no=1'>First Page</a></li>";
    } ?>
        
    <li <?php if($page_no <= 1){ echo "class='disabled'"; } ?>>
    <a <?php if($page_no > 1){
    echo "href='?page_no=$previous_page'";
    } ?>>Previous</a>
    </li>
        
    <li <?php if($page_no >= $total_no_of_pages){
    echo "class='disabled'";
    } ?>>
    <a <?php if($page_no < $total_no_of_pages) {
    echo "href='?page_no=$next_page'";
    } ?>>Next</a>
    </li>
    
    <?php if($page_no < $total_no_of_pages){
    echo "<li><a href='?page_no=$total_no_of_pages'>Last &rsaquo;&rsaquo;</a></li>";
    } ?>
</ul>


<script src="../js/receiptList.js"></script>
<?php
    require __DIR__ . '/inc/footer.tpl.php';
?>
