<?php

include_once('FDL/Lib.Dir.php');
function getjsservice(&$action) {
   
  $dbaccess = getParam("FREEDOM_DB");

  $snum = GetHttpVars("snum", "");
  if ($snum<0 || $snum=="") {
    $action->lay->set("OUT", "var svc = false;");
    return;
  }

  $tup = GetChildDoc( getParam("FREEDOM_DB"), 0, 0, "ALL", 
		     array("uport_ownerid = ".$action->user->fid), $action->user->id, "LIST", "USER_PORTAL");
  if (count($tup)<1 || !$tup[0]->isAffected()) {
    $action->lay->set("OUT", "var svc = false;");
  } else {
    $up = $tup[0];
  }

  $svcnum   = $up->getTValue("uport_svcnum");
  $svcid    = $up->getTValue("uport_idsvc");
  $svctitle = $up->getTValue("uport_svc");
  $svcparam = $up->getTValue("uport_param");
  $svccol   = $up->getTValue("uport_column");
  $svcline  = $up->getTValue("uport_line");

  $sid = -1;
  foreach ($svcnum as $k => $v) {
    if ($v==$snum) {
      $sid = $svcid[$k];
      $stitle = $svctitle[$k];
      $sparam = $svcparam[$k];
      $scol = $svccol[$k];
      $slin = $svcline[$k];
      continue;
    }
  }
  if ($sid==-1) {
    $action->lay->set("OUT", "var svc = false;");
    return;
  }    

  $svc  = getTDoc($dbaccess, $sid);
  
  $ret = "var svc = { "
    .    "     snum:".$snum.","
    .    "     sid:".$sid.","
    .    "     stitle:'".getV($svc,"psvc_title")."',"
    .    "     vurl:'".getV($svc,"psvc_vurl")."',"
    .    "     eurl:'".getV($svc,"psvc_eurl")."',"
    .    "     purl:'".$sparam."'," 
    .    "     col:".$scol."," 
    .    "     lin:".$slin.","
    .    "     m:false,"
    .    "     e:true,"
    .    "     d:false };"; 

  $action->lay->set("OUT", $ret);
}
?>