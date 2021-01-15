<?php



// conserve que les $length premiers caractères d'une chaine et ajoute ... à la fin
function truncate($string,$length=350,$append="&hellip;") {
    $string = trim($string);
  
    if(strlen($string) > $length) {
      $string = wordwrap($string, $length);
      $string = explode("\n", $string, 2);
      $string = $string[0] . $append;
    }
  
    return $string;
  }
  
// affichage de bloc de message de type alert
function sendMessage($message, $type) {
  if ($type === "success") {
    return "<div class=\"col-lg-4 alert alert-success alert-dismissible text-center\"><button type=\"button\" class=\"close\" data-bs-dismiss=\"alert\">&times;</button>" . $message . "</div>";
  } else {
    return "<div class=\"col-lg-4 alert alert-danger alert-dismissible text-center\"><button type=\"button\" class=\"close\" data-bs-dismiss=\"alert\">&times;</button>". $message ."</div>";
  }
}

// pagination
function generateRequest($conn) {

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

 
    // req dazdzad841adz------------------------
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

    
    var_dump($req);
    return mysqli_query($conn, $req);


    // ---------------------------




}
