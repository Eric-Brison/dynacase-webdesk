<?php
function freedomrss(&$action) {

  include_once("WHAT/Lib.Http.php");
  include_once("FDL/Lib.Dir.php");
  include_once("WEBDESK/svcrss.php");
  
  header('Content-type: text/xml; charset=utf-8');
  $action->lay->setEncoding("utf-8");

  $rssid = GetHttpVars("rssid", -1);

  if ($rssid==-1) $rsslink="";
  else $rsslink = urlencode(str_replace('://', '://[user]:[pass]@', getParam("CORE_ABSURL"))."/".getParam("CORE_STANDURL")."&app=FREEDOM&action=FREEDOM_RSS&id=$rssid");
  $max = (GetHttpVars("max",10)>0 ? GetHttpVars("max",10) : 100);
  $fcard = GetHttpVars("fcard",0);

  setHttpVar("rss", $rsslink);
  setHttpVar("max", $max );
  setHttpVar("vfull", $fcard );
  setHttpVar("dlg", 0);

  $action->lay = new Layout($action->GetLayoutFile("svcrss.xml"),$action);
  svcrss($action);
}
?>
