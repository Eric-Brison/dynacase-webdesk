<?php
  //
  // $Id: svcmng.php,v 1.1 2007/09/28 09:30:33 marc Exp $
  //

include_once('FDL/Lib.Dir.php');
include_once('WEBDESK/addservice.php');
include_once('WEBDESK/delservice.php');

global $action;

$do    = GetHttpVars("do", "");
$svcid = GetHttpVars("svc", "");
$uid   = GetHttpVars("uid", 0);
$login = GetHttpVars("login", 0);
$page  = GetHttpVars("pid", 1);

if ($svcid=="" || ($do!="add" && $do!="del")) {
  print "usage : wsh --api=svcmng --do=add|del --svc=<service doc (logical) id> [--uid=<userid>|--login=<user login>] [--page=<page number>\n";
  print "              --uid AND --login not set, service is added to all user portal\n";
  exit;
 }

$dbaccess = $action->GetParam("FREEDOM_DB");
$dsvc = new_Doc($dbaccess, $svcid);
if (!$dsvc->isAlive() || $dsvc->fromid!=getIdFromName($dbaccess,"PORTAL_SERVICE")) {
  print "error: $svcid is not a valid doc or a portal service\n";
  exit;
 }
echo "Service($do) : ".$dsvc->getTitle()." (".$dsvc->id.")\n";

$lp = array();
if ($uid!=0 || $login!="") {
  if ($uid!=0) $lp[0] = new_Doc($dbaccess, $pid);
  else $lp = GetChildDoc( $dbaccess, 0, 0, "ALL", array("us_login = '".$login."'"), 1, "LIST", "IUSER");
 } else {
  $lp = GetChildDoc( $dbaccess, 0, 0, "ALL", array(), 1, "LIST", "IUSER");
 }
foreach ($lp as $k => $v) {

  echo " - processing [".$v->getTitle()."] (".$v->id.") : ";

  setHttpVar("silent", "yes"); 
  setHttpVar("sid", $dsvc->id);
  setHttpVar("oid", $v->id);
  switch($do) {
  case "add" : 
    addservice($action);
    echo "added";
    break;
  case "del":
    delservice($action);
    echo "removed";
    break;
  default:
    echo "nop($do)";
  }
  echo "\n";

  
}

?>