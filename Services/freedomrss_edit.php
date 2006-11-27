<?php
function freedomedit(&$action) {

  include_once("WHAT/Lib.Http.php");
  include_once("FDL/Lib.Dir.php");
  
  header('Content-type: text/xml; charset=utf-8');
  $action->lay->setEncoding("utf-8");

  $rssid = GetHttpVars("rssid", -1);

}
?>