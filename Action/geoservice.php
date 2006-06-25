<?php

include_once('FDL/Lib.Dir.php');
function geoservice(&$action) {

  $dbaccess = getParam("FREEDOM_DB");
  

  $spec = GetHttpVars("spec", "");
  if ($spec=="") {
    $action->lay->set("OUT", "var svcnum = -1; // spec empty");
    return;
  }
  $tspec = explode("|",$spec);
  $svcgeo = array();
  foreach ($tspec as $k => $v) {
    $s = explode(":",$v);
    if (!is_numeric($s[0]) || !is_numeric($s[1]) || !is_numeric($s[2])) continue; 
    $svcgeo[] = array( "snum" =>  $s[0], "col" => $s[1], "lin" => $s[2] );
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

  $change = false;
//   $msg = '';
  foreach ($svcnum as $k => $v) {
    foreach ($svcgeo as $kg => $vg) {
      if ($vg["snum"]==$v) {
	$svccol[$k] = $vg["col"];
	$svcline[$k] = $vg["lin"];	
// 	$msg .= '    svn('.$v.') [col='.$svccol[$k].';lin='.$svcline[$k];
	$change = true;
      }
    }
  }
  if ($change) {
    $up->setValue("uport_column", $svccol);
    $up->setValue("uport_line", $svcline);
    $err = $up->modify();
    $up->postModify();
    $action->lay->set("OUT", "var svcnum = false;");
  } else $action->lay->set("OUT", "var svcnum = -1; // no modif");
//   echo $msg;
}
?>