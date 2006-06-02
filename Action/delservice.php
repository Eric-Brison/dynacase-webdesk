<?php

include_once('FDL/Lib.Dir.php');
function delservice(&$action) {
   
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

  $svcnum   = $up->getTValue("uport_svcnum");
  $svcid    = $up->getTValue("uport_idsvc");
  $svctitle = $up->getTValue("uport_svc");
  $svcparam = $up->getTValue("uport_param");
  $svccol   = $up->getTValue("uport_column");
  $svcline  = $up->getTValue("uport_line");

  $nsvcnum   = array();
  $nsvcid    = array();
  $nsvctitle = array();
  $nsvcparam = array();
  $nsvccol   = array();
  $nsvcline  = array();
  $change = false;
  foreach ($svcnum as $k => $v) {
    if ($snum!=$v) {
      $nsvcnum[]   = $svcnum[$k];
      $nsvcid[]    = $svcid[$k];
      $nsvctitle[] = $svctitle[$k];
      $nsvcparam[] = $svcparam[$k];
      $nsvccol[]   = $svccol[$k];
      $nsvcline[]  = $svcline[$k];
    }  else {
      $change = true;
    }
  }
  
  $up->setValue("uport_svcnum",$nsvcnum);
  $up->setValue("uport_idsvc",$nsvcid);
  $up->setValue("uport_svc",$nsvctitle);
  $up->setValue("uport_param",$nsvcparam);
  $up->setValue("uport_column",$nsvccol);
  $up->setValue("uport_line",$nsvcline);

  if ($change) {
    $err = $up->modify();
    $up->postModify();
    $action->lay->set("OUT", "var svcnum = $snum;");
  } else $action->lay->set("OUT", "var svcnum = -1;");
}
?>