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

<form method="get" action="" id="filter-form">
    <div class="row"> 
        <div>  
            <legend>Pointé</legend>
            <div class="form-check">
                <label class="form-check-label">
                <input type="radio" class="form-check-input tva-check" name="isChecked" id="isChecked-yes" value="isChecked-yes">
                Oui
                </label>
            </div>
            <div class="form-check">
                <label class="form-check-label">
                <input type="radio" class="form-check-input tva-check" name="isChecked" id="isChecked-no" value="isChecked-no">
                Non
                </label>
            </div>
            <div class="form-check">
                <label class="form-check-label">
                <input type="radio" class="form-check-input tva-check" name="isChecked" id="isChecked-all" value="isChecked-all">
                Les deux
                </label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary submitListReceiptFilters">Valider</button>
    </div>
</form>

<table class="table table-hover">
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

    // pagination ----------------
    $total_records_per_page = 6;
    if (isset($_GET['page_no']) && $_GET['page_no']!="") {
    $page_no = $_GET['page_no'];
    } else {
        $page_no = 1;
        }

    $offset = ($page_no-1) * $total_records_per_page;
    $previous_page = $page_no - 1;
    $next_page = $page_no + 1;
    $adjacents = "2";
    // ----------------------------


    // ---- checked filter -> set a cookie  
    if (isset($_GET['isChecked']) ) 
    {
        $isChecked = $_GET['isChecked'];
        if ( $isChecked == "isChecked-yes") {
            $checkedSQL = "checked=true";
        } elseif ( $isChecked == "isChecked-no") {
            $checkedSQL = "checked=false";
        } 
        else {
            $checkedSQL = "checked=true OR checked=false";
             
        }
        setcookie("isChecked", $checkedSQL,  time() + 2592000);
        setcookie("isCheckedJS", $_GET['isChecked'],  time() + 2592000);
        $where = 'WHERE ' . $checkedSQL;
        $req = "SELECT * FROM `receipts` $where ORDER BY `date_emission` DESC, `id` DESC LIMIT $offset, $total_records_per_page";
    } elseif ( isset($_COOKIE['isChecked']) ){
        $where = 'WHERE ' . $_COOKIE["isChecked"];
        $req = "SELECT * FROM `receipts` $where ORDER BY `date_emission` DESC, `id` DESC LIMIT $offset, $total_records_per_page";
    } else {
        $req = "SELECT * FROM `receipts` ORDER BY `date_emission` DESC, `id` DESC LIMIT $offset, $total_records_per_page";
    }
    $result = mysqli_query($conn, $req);
    // ---------------------------
    
    // pagination -------------------------------------------------
    if (isset($where)){
        $paginateCountReq = "SELECT COUNT(*) As total_records FROM `receipts` $where";
    } else {
        $paginateCountReq = "SELECT COUNT(*) As total_records FROM `receipts`";
    }

    $result_count = mysqli_query($conn, $paginateCountReq);
    $total_records = mysqli_fetch_array($result_count);
    $total_records = $total_records['total_records'];
    $total_no_of_pages = ceil($total_records / $total_records_per_page);
    $second_last = $total_no_of_pages - 1; // total pages minus 1
    //----------------------------------------------------------------


    while ($row = mysqli_fetch_array($result)) { 

        // format values
        $fmt = new NumberFormatter( 'de_DE', NumberFormatter::CURRENCY );
        $amount = $fmt->formatCurrency($row['montant_ttc'], "EUR");
        $row['checked'] ? $isChecked = "oui" : $isChecked = "non"; 
        $description = truncate($row['description'], 40);

        // format categories
        foreach ($receiptCategories as $index => $category) {
            if ($index == $row['category']) {
                $receiptCategory = $category;
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
                <a class='update-receipt' href=\"receipt.php" .
                "?id=" . $row['id'] .
                "&photo=" . $row['photo'] .
                "&date=" . $row['date_emission'] .
                "&receiptCategory=" . $receiptCategory .
                "&provider=" . $row['provider'] .
                "&tva=" . $tva .
                "&amount=" . $row['montant_ttc'] .
                "&isChecked=" . $isChecked .
                "&description=" . $description .
                "\">&#9998;</a>
            </td>";
            
        echo "</tr>";

        
    }
    mysqli_close($conn);


?> 

</table>

<div class="row">
    <div>
        <nav aria-label="Receipts navigation">
            <ul class="pagination">
                <?php if($page_no > 1){
                echo "<li class=\"page-item\"><a class=\"page-link\" href='?page_no=1'>First Page</a></li>";
                } ?>


                <li <?php if($page_no <= 1){ echo "class='page-item disabled'"; } else { echo "class='page-item'"; } ?>>
                <a class="page-link" <?php if($page_no > 1){
                echo "href='?page_no=$previous_page'";
                } ?>>Previous</a>
                </li>
                    

                <li <?php if($page_no >= $total_no_of_pages){
                    echo "class='disabled page-item'";
                } else {
                    echo "page-item'";
                } 
                ?>>
                <a class="page-link" <?php if($page_no < $total_no_of_pages) {
                            echo "href='?page_no=$next_page'";
                    } ?>
                >Next</a>
                </li>

                
                <?php if($page_no < $total_no_of_pages){
                echo "<li class=\"page-item\"><a class=\"page-link\" href='?page_no=$total_no_of_pages'>Last &rsaquo;&rsaquo;</a></li>";
                } ?>
            </ul>
            <div>
                <strong>Page <?php echo $page_no." of ".$total_no_of_pages; ?></strong>
            </div>
        </nav>
    </div>
</div>



<script src="../js/receiptList.js"></script>
<?php
    require __DIR__ . '/inc/footer.tpl.php';
?>
