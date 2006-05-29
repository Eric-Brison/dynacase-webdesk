<?php

function main(&$action) {

  $action->lay->set("IsMBarStatic", getParam("WDK_MBARSTATIC",1)!=1); 

}
?>
