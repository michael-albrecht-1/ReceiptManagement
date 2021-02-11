<h1>Liste des tickets</h1>

<form method="get" action="" id="filter-form" class="my-5">
    <div class="form-row flex-wrap">
        <input id="page" name="page" type="hidden" value="receiptList">
        <div class="col-8 col-md-4 mb-4 pr-4 pb-4">
            <div class="row">
                <div class="col-12 pb-4">
                    <label for="startDate">Date début</label>
                    <input type="date" class="form-control" id="start-date" name="start-date"  value="<?= $receiptService->getDateFilter('start-date') ?>" required>
                </div>
                <div class="col-12">
                    <label for="endDate">Date fin</label>
                    <input type="date" class="form-control" id="end-date" name="end-date"  value="<?= $receiptService->getDateFilter('end-date') ?>" required>
                </div>
            </div>
        </div>
        <div class="col-4 col-md-3 pb-4">  
            <legend>Pointé</legend>
            <div class="form-check">
                <label class="form-check-label">
                <input type="radio" class="form-check-input tva-check" name="is-checked" id="isChecked-yes" value="true" <?= $receiptService->getIsCHeckedFilter('true') ?>>
                Oui
                </label>
            </div>
            <div class="form-check">
                <label class="form-check-label">
                <input type="radio" class="form-check-input tva-check" name="is-checked" id="isChecked-no" value="false" <?= $receiptService->getIsCHeckedFilter('false') ?>>
                Non
                </label>
            </div>
            <div class="form-check">
                <label class="form-check-label">
                <input type="radio" class="form-check-input tva-check" name="is-checked" id="isChecked-all" value="both" <?= $receiptService->getIsCHeckedFilter('both') ?>>
                Les deux
                </label>
            </div>
        </div>
    </div>
    <div class="form-row">      
        <div class="col-4 col-md-3 col-lg-2 "><button type="submit" class="btn btn-primary submitListReceiptFilters">Valider</button></div>
        <div class="col-4 col-md-3 col-lg-2">
            <?php // link to the olded receipt not checked
                $firstReceiptToCheck = $receiptService->getFirstReceiptToCheck();
                if ($firstReceiptToCheck != null) {
                    $firstReceiptToCheckLink = $receiptService->getLinkWithParamsFromRow($firstReceiptToCheck);
                    echo '<a href="' . $firstReceiptToCheckLink . '"><button type="button" class="btn btn-info submitListReceiptFilters">Pointer</button></a>';
                }

            ?>
        </div>
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

    if ( isset($_GET['is-checked'])) {
        $filters['isChecked'] = filter_input(INPUT_GET, 'is-checked');
        $filters['startDate'] = filter_input(INPUT_GET, 'start-date');
        $filters['endDate'] = filter_input(INPUT_GET, 'end-date');
    } else {
        $filters = null;
    }
    $receipts =  $receiptService->getFilteredReceiptList($filters, $total_records_per_page, $offset); 
    
    // pagination -------------------------------------------------



    $totalReceiptsNumber = $receiptService->getTotalReceiptsNumber();

    
    $totalReceiptsNumber = $totalReceiptsNumber['total_records'];
    $total_no_of_pages = ceil($totalReceiptsNumber / $total_records_per_page);
    $second_last = $total_no_of_pages - 1; // total pages minus 1
    //----------------------------------------------------------------


    foreach ($receipts as $receipt) { 

        // ================
        // format values ==
        // ================
        $date_emission = date("d-m-Y", strtotime($receipt['date_emission']));
        $fmt = new NumberFormatter( 'de_DE', NumberFormatter::CURRENCY );
        $amount = $fmt->formatCurrency($receipt['montant_ttc'], "EUR");
        $receipt['checked'] ? $isChecked = "oui" : $isChecked = "non"; 
        $description = truncate($receipt['description'], 40);
        $receiptCategory = formatCategory($receipt['category']);
        $tva = formatTva($receipt['tva']);

        // generate update receipt link
        $updateReceiptLink = $receiptService->getLinkWithParamsFromRow($receipt);

        // ===================
        // format values end =
        // ===================     

        echo "<tr>";
            echo "<td>" . $date_emission . "</td>";
            echo "<td>" . $receiptCategory . "</td>";
            echo "<td>" . $receipt['provider'] . "</td>";
            echo "<td>" . $tva . "</td>";
            echo "<td>" . $amount . "</td>";
            echo "<td>" . $isChecked . "</td>";
            echo "<td>" . $receipt['description'] . "</td>";
            echo "<td><a class='update-receipt' href=" . $updateReceiptLink . ">&#9998;</a></td>";
        echo "</tr>";
    }
?> 
</table>

<!-- ======================== -->
<!-- pagination ============= -->
<!-- ======================== -->
<div class="row">
    <div>
        <nav aria-label="Receipts navigation">
            <ul class="pagination">
                <?php if($page_no > 1){
                echo "<li class=\"page-item\"><a class=\"page-link\" href='?page=receiptList&page_no=1'>First Page</a></li>";
                } ?>


                <li <?php if($page_no <= 1){ echo "class='page-item disabled'"; } else { echo "class='page-item'"; } ?>>
                <a class="page-link" <?php if($page_no > 1){
                echo "href='?page=receiptList&page_no=$previous_page'";
                } ?>>Previous</a>
                </li>
                    

                <li <?php if($page_no >= $total_no_of_pages){
                    echo "class='disabled page-item'";
                } else {
                    echo "page-item'";
                } 
                ?>>
                <a class="page-link" <?php if($page_no < $total_no_of_pages) {
                            echo "href='?page=receiptList&page_no=$next_page'";
                    } ?>
                >Next</a>
                </li>

                
                <?php if($page_no < $total_no_of_pages){
                echo "<li class=\"page-item\"><a class=\"page-link\" href='?page=receiptList&page_no=$total_no_of_pages'>Last &rsaquo;&rsaquo;</a></li>";
                } ?>
            </ul>
            <div>
                <strong>Page <?php echo $page_no." of ".$total_no_of_pages; ?></strong>
            </div>
        </nav>
    </div>
</div>



<script src="js/receiptList.js"></script>
