<?php

function savesvc(&$action) {
  $p = urldecode(GetHttpVars("params", ""));
  $tp = explode("&",$p);
  foreach ($tp as $k => $v) {
    if ($v=="") continue;
    $pv=explode("=", $v);
    echo "- name=".$pv[0]." value=".urldecode($pv[1])."<br>";
  }
  $action->lay->set("OUT", "");
}
?>