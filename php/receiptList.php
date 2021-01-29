

<h1>Liste des tickets</h1>

<form method="get" action="" id="filter-form" class="my-5">
    <div class="form-row flex-wrap">
        <div><input id="page" name="page" type="hidden" value="2"></div>
        <div class="col-8 col-md-4 mb-4 pr-4 pb-4">
            <div class="row">
                <div class="col-12 pb-4">
                    <label for="startDate">Date début</label>
                    <input type="date" class="form-control" id="startDate" name="startDate"  value="<?= $_GET['startDate'] ?? date("Y-m-d", mktime(0, 0, 0, 7, 1, 2000)) ?>" required>
                </div>
                <div class="col-12">
                    <label for="endDate">Date fin</label>
                    <input type="date" class="form-control" id="endDate" name="endDate"  value="<?= $_GET['endDate'] ?? date("Y-m-d") ?>" required>
                </div>
            </div>
        </div>
        <div class="col-4 col-md-3 pb-4">  
            <label class="pl-3">Pointé</label>
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
    </div>
    <div class="form-row">
        <div class="col-4 col-md-3 col-lg-2"><button type="submit" class="btn btn-primary">Valider</button></div>
        <div class="col-4 col-md-3 col-lg-2">
            <?php // link to the olded receipt not checked
                $firstReceiptToCheck = getFirstReceiptToCheck($conn);
                if ($firstReceiptToCheck != null) {
                    $firstReceiptToCheckLink = getLinkWithParamsFromRow($firstReceiptToCheck, $receiptCategories);
                    echo '<a href="' . $firstReceiptToCheckLink . '"><button type="button" class="btn btn-info">Pointer</button></a>';
                }

            ?>
        </div>
        <div class="col-4 col-md-3 col-lg-2"><a href="#"><button type="button" class="btn btn-info">Exporter</button></a></div>
    </div>
</form>

<table class="table">
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
        $date = new DateTime($row['date_emission']);
        $date = $date->format('d-m-Y');
        $fmt = new NumberFormatter( 'de_DE', NumberFormatter::CURRENCY );
        $amount = $fmt->formatCurrency($row['montant_ttc'], "EUR");
        $row['checked'] ? $isChecked = "oui" : $isChecked = "non"; 
        $description = truncate($row['description'], 40);
        $receiptCategory = formatCategory($row['category'], $receiptCategories);
        $tva = formatTva($row['tva']);

        // generate update receipt link
        $updateReceiptLink = getLinkWithParamsFromRow($row, $receiptCategories);

        
        // format values end
     

        echo "<tr>";
            echo "<td>" . $date . "</td>";
            echo "<td>" . $receiptCategory . "</td>";
            echo "<td>" . $row['provider'] . "</td>";
            echo "<td>" . $tva . "</td>";
            echo "<td>" . $amount . "</td>";
            echo "<td>" . $isChecked . "</td>";
            echo "<td>" . $row['description'] . "</td>";
            echo "<td>
                <a class='update-receipt' href=" . $updateReceiptLink . ">&#9998;</a>
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
                echo "<li class=\"page-item\"><a class=\"page-link\" href='?page=2&page_no=1'>First Page</a></li>";
                } ?>


                <li <?php if($page_no <= 1){ echo "class='page-item disabled'"; } else { echo "class='page-item'"; } ?>>
                <a class="page-link" <?php if($page_no > 1){
                echo "href='?page=2&page_no=$previous_page'";
                } ?>>Previous</a>
                </li>
                    

                <li <?php if($page_no >= $total_no_of_pages){
                    echo "class='disabled page-item'";
                } else {
                    echo "page-item'";
                } 
                ?>>
                <a class="page-link" <?php if($page_no < $total_no_of_pages) {
                            echo "href='?page=2&page_no=$next_page'";
                    } ?>
                >Next</a>
                </li>

                
                <?php if($page_no < $total_no_of_pages){
                echo "<li class=\"page-item\"><a class=\"page-link\" href='?page=2&page_no=$total_no_of_pages'>Last &rsaquo;&rsaquo;</a></li>";
                } ?>
            </ul>
            <div>
                <strong>Page <?php echo $page_no." of ".$total_no_of_pages; ?></strong>
            </div>
        </nav>
    </div>
</div>



<script src="js/receiptList.js"></script>
