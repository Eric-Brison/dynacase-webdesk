<?php

include_once("FDL/Lib.Dir.php");
include_once("INCIDENT/incidentdayinterval.php");
include_once("FDL/Class.Doc.php");
// include_once("FDL/Class.WDocPropo.php");
function incident(&$action) {

  $dbaccess = $action->GetParam("FREEDOM_DB");

  $interval=incidentdayinterval(&$action);

  $filters=array();
  $filters[]="(state<>'traited' and state<>'rejected' and state<>'closed'  ) ";

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

    $name = $cur->getDocValue($ida,"us_fname")." ".$cur->getDocValue($ida,"us_lname");
    $inc[] = array( "state" => _($state),
		    "title" => getV($v, "in_title"),
		    "desc" => getV($v, "in_pbdesc"),
		    "date" => getV($v, "in_createdate"),
		    "grav" => (getV($v, "in_grav")==""?'pas de gravité':getV($v, "in_grav")),
		    "site" => getV($v, "in_site"),
		    "ref" => $v["title"],
		    "owner" => $name
		   );
  }
  $action->lay->setBlockData("inc", $inc);
  
}
?>