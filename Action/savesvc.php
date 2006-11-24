<?php

include_once('FDL/Lib.Dir.php');
function savesvc(&$action) {

  $dbaccess = getParam("FREEDOM_DB");
  
  $excludev = array("sole","app","action","snum");
  global $_GET,$_POST,$ZONE_ARGS;
  $allvars = array();
  if (!isset($ZONE_ARGS)) $ZONE_ARGS = array();
  if (!isset($_GET)) $_GET = array();
  if (!isset($_POST)) $_POST = array();
  $allvars = array_merge($_GET,$_POST,$ZONE_ARGS);


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

  $p = "";
  foreach ($allvars as $k => $v) {
    if (in_array($k, $excludev)) continue;
    $p .= ($p==""?"":"&").$k."=".urlencode($v);
  }

  $svcnum   = $up->getTValue("uport_svcnum");
  $svcparam = $up->getTValue("uport_param");

  $change = false;
  foreach ($svcnum as $k => $v) {
    if ($snum==$v) {
      $svcparam[$k] = $p;
      $change = true;
    }
  }
  if ($change) {
    $up->setValue("uport_param", $svcparam);
    $err = $up->modify();
    $up->postModify();
    $action->lay->set("OUT", "var svcnum = $snum;");
  } else $action->lay->set("OUT", "var svcnum = -1;");
}
?>