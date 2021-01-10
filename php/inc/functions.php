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
