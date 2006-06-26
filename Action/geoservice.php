<?php

include_once('FDL/Lib.Dir.php');
function geoservice(&$action) {

  $dbaccess = getParam("FREEDOM_DB");
  

  $spec = GetHttpVars("sgeo", "");
  if ($spec=="") {
    $action->lay->set("OUT", "var svcnum = -1; // spec empty");
    return;
  }
  $tspec = explode("|",$spec);
  $svcgeo = array();
  foreach ($tspec as $k => $v) {
    $s = explode(":",$v);
    if (!is_numeric($s[0]) || !is_numeric($s[1]) || !is_numeric($s[2])) continue; 
    if (!isset($s[3])) $s[3]=1;
    $svcgeo[$s[0]] = array( "snum" =>  $s[0], "col" => $s[1], "lin" => $s[2], "open" => $s[3]);
  }

  $tup = GetChildDoc( getParam("FREEDOM_DB"), 0, 0, "ALL", 
		     array("uport_ownerid = ".$action->user->fid), $action->user->id, "LIST", "USER_PORTAL");
  if (count($tup)<1 || !$tup[0]->isAffected()) {
    $action->lay->set("OUT", "var svcnum = -1; // no portal");
    return;
  } else {
    $up = $tup[0];
  }

  $svcnum   = $up->getTValue("uport_svcnum");
  $svccol   = $up->getTValue("uport_column");
  $svcline  = $up->getTValue("uport_line");
  $svcopen  = $up->getTValue("uport_open");

  $change = false;
//   $msg = '';
  foreach ($svcnum as $k => $v) {
    if (isset($svcgeo[$v])) {
      $svccol[$k] = $svcgeo[$v]["col"];
      $svcline[$k] = $svcgeo[$v]["lin"];	
      $svcopen[$k] = $svcgeo[$v]["open"];	
      echo '  svn('.$v.') [col='.$svccol[$k].';lin='.$svcline[$k].']<br>';
      $change = true;
    }
  }
  if ($change) {
    $up->setValue("uport_column", $svccol);
    $up->setValue("uport_line", $svcline);
    $up->setValue("uport_open", $svcopen);
    $err = $up->modify();
    $up->postModify();
    $action->lay->set("OUT", "var svcnum = false;");
  } else $action->lay->set("OUT", "var svcnum = -1; // no modif");
}
?>