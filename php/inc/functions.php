<?php


function filterReceipts($conn) {
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

  return mysqli_query($conn, $req);
}

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
