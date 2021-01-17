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