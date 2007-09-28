<?php

include_once('FDL/Lib.Dir.php');
function delservice(&$action) {
   
  $dbaccess = $action->getParam("FREEDOM_DB");

  $inter  = (GetHttpVars("silent", "no")=="no" ? true : false);

  $oid = GetHttpVars("oid", -1);
  $owner = $action->user->fid;
  if ($oid!=-1) $owner = $oid;

  $sid = GetHttpVars("sid", -1);
  $snum = GetHttpVars("snum", -1);
  if (($snum==-1 || $snum=="") && $sid==-1) {
    if ($inter) $action->lay->set("OUT", "var svcnum = -1;");
    return;
  }

  $tup = GetChildDoc( $dbaccess, 0, 0, "ALL", 
		      array("uport_ownerid = ".$owner), $action->user->id, "LIST", "USER_PORTAL");
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
  $svcrdel  = $up->getTValue("uport_refreshd");
  $svccol   = $up->getTValue("uport_column");
  $svcline  = $up->getTValue("uport_line");
  $svcopen  = $up->getTValue("uport_open");
    
  $up->deleteValue("uport_svcnum");
  $up->deleteValue("uport_idsvc");
  $up->deleteValue("uport_svc");
  $up->deleteValue("uport_param");
  $up->deleteValue("uport_refreshd");
  $up->deleteValue("uport_column");
  $up->deleteValue("uport_line");
  $up->deleteValue("uport_open");

  $nsvcnum   = array();
  $nsvcid    = array();
  $nsvctitle = array();
  $nsvcparam = array();
  $nsvrcdel  = array();
  $nsvccol   = array();
  $nsvcline  = array();
  $nsvcopen  = array();
  $change = false;
  foreach ($svcnum as $k => $v) {
    if ($v!="" && $snum!=$v && $sid!=$svcid[$k]) {
      $nsvcnum[]   = $svcnum[$k];
      $nsvcid[]    = $svcid[$k];
      $nsvctitle[] = $svctitle[$k];
      $nsvcparam[] = $svcparam[$k];
      $nsvcrdel[]  = $svcrdel[$k];
      $nsvccol[]   = $svccol[$k];
      $nsvcline[]  = $svcline[$k];
      $nsvcopen[]  = $svcopen[$k];
    }  else {
      $change = true;
    }
  }
  
  $up->setValue("uport_svcnum",$nsvcnum);
  $up->setValue("uport_idsvc",$nsvcid);
  $up->setValue("uport_svc",$nsvctitle);
  $up->setValue("uport_param",$nsvcparam);
  $up->setValue("uport_refreshd",$nsvcrdel);
  $up->setValue("uport_column",$nsvccol);
  $up->setValue("uport_line",$nsvcline);
  $up->setValue("uport_open",$nsvcopen);

  if ($change) {
    $err = $up->modify();
    $up->postModify();
    if ($inter) $action->lay->set("OUT", "var svcnum = $snum;");
  } else if ($inter) $action->lay->set("OUT", "var svcnum = -1;");
}
?>
