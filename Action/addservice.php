<?php

include_once('FDL/Lib.Dir.php');
function addservice(&$action) {
   
  $dbaccess = getParam("FREEDOM_DB");

  $sid = GetHttpVars("sid", -1);
  if ($sid==-1 || $sid=="") {
    $action->lay->set("OUT", "var svcnum = -1;");
  }

  $tup = GetChildDoc( getParam("FREEDOM_DB"), 0, 0, "ALL", 
		     array("uport_ownerid = ".$action->user->fid), $action->user->id, "LIST", "USER_PORTAL");
  if (count($tup)<1 || !$tup[0]->isAffected()) {
    $up = createDoc($dbaccess, "USER_PORTAL");
    $up->setValue("uport_ownerid", $action->user->fid);
    $up->setValue("uport_owner", $action->user->firstname." ".$action->user->firstname);
    $up->setValue("uport_title", "Mon portail (".$action->user->firstname." ".$action->user->firstname. ")");
    $up->Add();
    $svcnum   = $svcid = $svctitle = $svcparam = $svcrdel = $svccol = $svcline = array();
  } else {
    $up = $tup[0];
    $svcnum   = $up->getTValue("uport_svcnum");
    $svcid    = $up->getTValue("uport_idsvc");
    $svctitle = $up->getTValue("uport_svc");
    $svcparam = $up->getTValue("uport_param");
    $svcrdel  = $up->getTValue("uport_refreshd");
    $svccol   = $up->getTValue("uport_column");
    $svcline  = $up->getTValue("uport_line");
  }

  $svnnumber = $up->getNumSequence();

  $svc        = getTDoc($dbaccess, $sid);
  $svcnum[]   = $svnnumber;
  $svcid[]    = $sid;
  $svctitle[] = getV($svc, "psvc_title");
  $svcparam[] = " ";
  $svcrdel[]   = (getV($svc, "psvc_refreshd")==""?0:getV($svc, "psvc_refreshd"));
  $svccol[]   = 0;
  $svcline[]   = 0;

  $up->setValue("uport_svcnum",$svcnum);
  $up->setValue("uport_idsvc",$svcid);
  $up->setValue("uport_svc",$svctitle);
  $up->setValue("uport_param",$svcparam);
  $up->setValue("uport_refreshd",$svcrdel);
  $up->setValue("uport_column",$svccol);
  $up->setValue("uport_line",$svcline);
  
  $err = $up->modify();
  $up->postModify();

  $action->lay->set("OUT", "var svcnum = $svnnumber;");
}
?>