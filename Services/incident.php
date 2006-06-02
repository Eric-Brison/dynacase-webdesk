<?php

include_once("FDL/Lib.Dir.php");
include_once("INCIDENT/incidentdayinterval.php");
include_once("FDL/Class.Doc.php");
include_once("FDL/Class.WDocPropo.php");
function incident(&$action) {

  $dbaccess = $action->GetParam("FREEDOM_DB");

  $interval=incidentdayinterval(&$action);

  $trecordedr=getChildDoc($dbaccess,
                    0, 
                    "0", "ALL", $filters,
                    1,
                    "TABLE",$famid,true,"",false );
  $filters=array();
  $filters[]="(state<>'traited' and state<>'rejected' and state<>'closed'  ) ";

  $action->lay->set("none", true);
  $cur=createDoc($dbaccess,"INCIDENT",false);
  $ti = getChildDoc($dbaccess, 0,
		    "0", "ALL", $filters,
		    1, "TABLE","INCIDENT");
  
  foreach ($tcurent as $k=>$v) {
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
		    "title" => getV($v, "in_title"),
		    "owner" => $cur->getDocValue($cur->getValue("in_trtid"),"us_initials")
		   );
  }
  $action->lay->setBlockData("inc", $inc);
  
}
?>