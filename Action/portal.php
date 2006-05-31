<?php

function portal(&$action) {
   
  $colcount = getParam("WDK_COLCOUNT",3);

  $action->lay->set("colCount", $colcount); 

  $cols = array();
  for ($icol=0; $icol<$colcount; $icol++) {
    $cols[] = array( "icol" => $icol );
  }
  $action->lay->setBlockData("cols", $cols); 
  
}
?>