<?php
function svccontact(&$action) {

header('Content-type: text/xml; charset=utf-8');
$action->lay->setEncoding("utf-8");

 $action->lay->set("datebull", time());
 $action->lay->set("location", "");
 $action->lay->set("sstr", "");
 
 include_once("WHAT/Lib.Http.php");
 include_once("FDL/Lib.Dir.php");
 
 $fcard  = GetHttpVars("fcard", 1); 
 $maxl   = GetHttpVars("maxl", 10); 
 $maxc   = GetHttpVars("maxc", 2);

 $action->lay->set("maxl", $maxl);
 $action->lay->set("maxc", $maxc);
 $action->lay->set("fcard", $fcard);
 
}
?>
