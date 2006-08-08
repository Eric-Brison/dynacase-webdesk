<?php

function embed(&$action) {
  header('Content-type: text/xml; charset=utf-8');
  $action->lay->setEncoding("utf-8");
  $url = GetHttpVars("url", "");
  if ($url=="") {
    $action->lay->set("nodata", true);
  } else {
    $action->lay->set("nodata", false);
    $action->lay->set("url", $url);
  }
  $action->lay->set("date", strftime("%H:%M %d/%m/%Y", time()));
}
?>
