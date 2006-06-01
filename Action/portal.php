<?php

include_once('FDL/Lib.Dir.php');
function portal(&$action) {
   
  $colcount = getParam("WDK_COLCOUNT",3);

  $action->lay->set("colCount", $colcount); 

  $cols = array();
  for ($icol=0; $icol<$colcount; $icol++) {
    $cols[] = array( "firstCol" => ($icol==0?true:false), "icol" => $icol );
  }
  $action->lay->setBlockData("cols", $cols); 
 

  // List services ordered by category
  $ts = GetChildDoc(getParam("FREEDOM_DB"), 0, 0, "ALL", array(), $action->user->id, "TABLE", "PORTAL_SERVICE");
  $tserv = array();
  $d = createDoc(getParam("FREEDOM_DB"), "PORTAL_SERVICE", false);
  foreach ($ts as $k => $v) {
    $ts[$k]["psvc_title_js"] = addslashes(getV($v, "psvc_title"));
    $d->Affect($v);

    $ts[$k]["Icon"] = false;
    $aico = $d->GetAttribute("psvc_icon");
//     echo getv($v,"psvc_icon");
    $vid="";
    if (ereg ("(.*)\|(.*)", getv($v,"psvc_icon"), $reg)) {
      $ts[$k]["Icon"] = true;
      $ts[$k]["docid"] = $v["id"];
      $ts[$k]["vid"] = $reg[2];
      $ts[$k]["attrid"] = "psvc_icon";
    }
    $acat = $d->GetAttribute("psvc_categorie");
    $cat = $acat->getEnum();
    $tserv[getV($v,"psvc_categorie")]["categorie"] = $cat[getV($v,"psvc_categorie")];
    $tserv[getV($v,"psvc_categorie")]["svc"][] = $ts[$k];
  }
  $action->lay->setBlockData("catS", $tserv); 
  foreach ($tserv as $k => $v)  {
    $action->lay->setBlockData("services".$v["categorie"], $v["svc"]); 
  }
    
}
?>