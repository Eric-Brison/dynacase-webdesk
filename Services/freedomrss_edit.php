<?php
function freedomrss_edit(&$action) {

  $dbaccess = getParam("FREEDOM_DB");

  include_once("WHAT/Lib.Http.php");
  include_once("FDL/Lib.Dir.php");
  
  $action->lay->set("rsstitle", "");
  $rssid = GetHttpVars("rssid", -1);
  if ($rssid>0) {
    $doc = new_Doc($dbaccess, $rssid);
    if ($doc->isAffected()) $action->lay->set("rsstitle", $doc->getTitle());
  }

}
?>