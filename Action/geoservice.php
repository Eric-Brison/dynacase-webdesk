<?php

include_once('FDL/Lib.Dir.php');
function geoservice(&$action) {

  $dbaccess = getParam("FREEDOM_DB");
  
  $snum = GetHttpVars("snum", -1);
  if ($snum==-1 || $snum=="") {
    $action->lay->set("OUT", "var svcnum = -1;");
    return;
  }

  $tup = GetChildDoc( getParam("FREEDOM_DB"), 0, 0, "ALL", 
		     array("uport_ownerid = ".$action->user->fid), $action->user->id, "LIST", "USER_PORTAL");
  if (count($tup)<1 || !$tup[0]->isAffected()) {
    $action->lay->set("OUT", "var svcnum = -1;");
    return;
  } else {
    $up = $tup[0];
  }

  $col = GetHttpVars("col", 0);
  $lin = GetHttpVars("lin", 0);

  $svcnum   = $up->getTValue("uport_svcnum");
  $svccol   = $up->getTValue("uport_column");
  $svcline  = $up->getTValue("uport_line");

  $change = false;
  foreach ($svcnum as $k => $v) {
    if ($snum==$v) {
      $svccol[$k] = $col;
      $svcline[$k] = $lin;	
      $change = true;
    }
  }
  if ($change) {
    $up->setValue("uport_column", $svccol);
    $up->setValue("uport_line", $svcline);
    $err = $up->modify();
    $up->postModify();
    $action->lay->set("OUT", "var svcnum = $snum;");
  } else $action->lay->set("OUT", "var svcnum = -1;");
}
?>