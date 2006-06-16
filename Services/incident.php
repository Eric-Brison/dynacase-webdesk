<?php

include_once("FDL/Lib.Dir.php");
include_once("INCIDENT/incidentdayinterval.php");
include_once("FDL/Class.Doc.php");
// include_once("FDL/Class.WDocPropo.php");
function incident(&$action) {

  $dbaccess = $action->GetParam("FREEDOM_DB");
  
  $myid = $action->user->fid;

  $interval=incidentdayinterval(&$action);

  $filters=array();
  $filters[]=" (  (state='recorded' and in_qualifid=$myid) "
    .        " or (state='qualified' and in_analid=$myid) "
    .        " or (state='analyzed' and in_trtid=$myid) ) ";

  $action->lay->set("none", true);
  $cur=createDoc($dbaccess,"INCIDENT",false);
  $ti = getChildDoc($dbaccess, 0,
		    "0", "ALL", $filters,
		    1, "TABLE","INCIDENT");
  foreach ($ti as $k=>$v) {
    $action->lay->set("none", false);
    $catg = getv($v,"in_grav",_("without category"));
    $cur->Affect($v);
    $state = $cur->state;
    switch ($state) {
    case "recorded":
       $ida = $cur->getValue("IN_QUALIFID");
     break;
    case "qualified":
       $ida = $cur->getValue("IN_ANALID");
     break;
     case "analyzed":
       $ida = $cur->getValue("IN_TRTID"); 
     break;
    }

    $inc[] = array( "state" => _($state),
		    "id" => getV($v, "id"),
		    "title" => getV($v, "in_title"),
		    "desc" => getV($v, "in_pbdesc"),
		    "date" => getV($v, "in_createdate"),
		    "grav" => (getV($v, "in_grav")==""?'pas de gravité':getV($v, "in_grav")),
		    "site" => getV($v, "in_site"),
		    "ref" => $v["title"],
		   );
  }
  $action->lay->setBlockData("inc", $inc);
  
}
?>