<?php

include_once('FDL/Lib.Dir.php');

function gsvc(&$action) {

  $svcname = strtolower(GetHttpVars("sname", ""));
  $svcact = strtolower(GetHttpVars("sact", ""));

  if ($svcname=="") {
    $action->lay->set("msg", "No service given!");
    return;
  }

  $actfile = $svcname.($svcact==""?"":"_").$svcact.".php";
  $actfunc = $svcname.($svcact==""?"":"_").$svcact;
  $actlay = $svcname.($svcact==""?"":"_").$svcact.".xml";
 

  $lfile = $action->GetLayoutFile($actlay);
  if ($lfile!="") $action->lay = new Layout($lfile);

  $hasfunc = false;
  include_once($actfile);
  if (function_exists($actfunc)) {
    $hasfunc = true;
    $ret = call_user_func($actfunc, $action);
    return $ret;
  }
  
  if ($lfile=="" && !$hasfunc) {
    $action->lay->set("msg", "no layout, no action !");
  }
  return;
}
