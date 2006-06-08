<?php
function svcardoise(&$action) {


  $craie  = GetHttpVars("craie", "white");  
  $cline = GetHttpVars("cline", 5);
  $content = GetHttpVars("content", "");

  $action->lay->set("cline", $cline);
  $action->lay->set("craie", $craie);
  $action->lay->set("content", $content);

  return;
}
?>