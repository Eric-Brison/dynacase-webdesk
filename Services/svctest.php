<?php

function svctest(&$action) {
  $gmt = GetHttpVars("gmt", 0);

 if ($gmt==0) $sd = date("M d Y H:i:s", time());
 else $sd = gmdate("M d Y H:i:s", time());
 
 $action->lay->set("gmt", ($gmt==1?true:false));
 $action->lay->set("date", $sd);
}
?>