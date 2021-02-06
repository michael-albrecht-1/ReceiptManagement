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
function sendMessage($message, $type = "success") {
    return "<div class=\"col-lg-4 alert alert-" . $type . " alert-dismissible text-center\"><button type=\"button\" class=\"close\" data-bs-dismiss=\"alert\">&times;</button>" . $message . "</div>";

}

function formatCategory ($rowCategory) {
  foreach ($GLOBALS['$receiptCategories'] as $index => $category) {
    if ($index == $rowCategory) {
        return $category;
    }
  }
}

function formatTva ($rowTva) {
  if ($rowTva =='tva1') 
  {
      return "0";
  } elseif ($rowTva =='tva2')
  {
      return "5.5";
  } elseif ($rowTva =='tva3')
  {
      return "10";
  }elseif ($rowTva =='tva4')
  {
      return "20";
  }
}

