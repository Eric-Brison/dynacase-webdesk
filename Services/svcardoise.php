<?php
function svcardoise(&$action) {

  header('Content-type: text/xml; charset=utf-8');
  $action->lay->setEncoding("utf-8");    
  $action->lay->set("uptime", strftime("%A %d %B %Y, %H:%M", time()));

  $craie  = GetHttpVars("craie", "white");  
  $cline = GetHttpVars("cline", 5);
  $content = GetHttpVars("content", "");
  if ($content!="") $action->parent->param->set("ARDOISE", $content, PARAM_USER.$action->user->id, $action->parent->id);

  $content = getParam("ARDOISE");
  $action->lay->set("cline", $cline);
  $action->lay->set("craie", $craie);
  $action->lay->set("content", $content);

  return;
}
?>