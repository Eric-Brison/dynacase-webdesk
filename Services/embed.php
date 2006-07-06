<?php

function embed(&$action) {
  $url = GetHttpVars("url", "");
  if ($url=="") {
    $action->lay->set("nodata", true);
  } else {
    $action->lay->set("nodata", false);
    $action->lay->set("url", $url);
  }
}
?>
