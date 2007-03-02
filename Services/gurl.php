<?php

include_once('FDL/Lib.Dir.php');

function gurl(&$action) {

  header('Content-type: text/xml; charset=utf-8');
  $url = GetHttpVars("url", "");
  $h = GetHttpVars("height", "auto");
  $nodetype = GetHttpVars("urltype", "iframe");
  $title = GetHttpVars("title", $url);
  if (trim($title)=="") $title=$url;


  $action->lay->set("nodata",($url==""));
  $action->lay->set("height",$h);
  $action->lay->set("url",$url);
  $action->lay->set("nodetype",$nodetype);

  $action->lay->set("title",$title);
  return;
}