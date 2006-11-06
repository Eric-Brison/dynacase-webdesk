<?php
function svccontact(&$action) {

header('Content-type: text/xml; charset=utf-8');
$action->lay->setEncoding("utf-8");

$action->lay->set("datebull", time());
$action->lay->set("location", "");
$action->lay->set("sstr", "");

include_once("WHAT/Lib.Http.php");
include_once("FDL/Lib.Dir.php");

$soc   = GetHttpVars("soc", 0);
$beg   = GetHttpVars("beg", 0);
$maxl  = GetHttpVars("maxl", 10);
$maxc  = GetHttpVars("maxc", 3);
$str   = GetHttpVars("str", "");

}
?>
