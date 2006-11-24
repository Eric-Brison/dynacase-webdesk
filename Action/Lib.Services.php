<?php

function parseUrl($link) {

  global $_SERVER;
  $gparams = array( "[user]" => $_SERVER['PHP_AUTH_USER'], 
		    "[pass]" => $_SERVER['PHP_AUTH_PW'],
		    );
  $ms = $mr = array();
  foreach ($gparams as $k => $v) {
    $ms[] = $k;
    $mr[] = $v;
  }
  return str_ireplace( $ms, $mr, $link);
}
  
?>