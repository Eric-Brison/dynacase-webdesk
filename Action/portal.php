<?php

include_once('FDL/Lib.Dir.php');
function portal(&$action) {
   
  $action->parent->AddJsRef("FDL:common.js", true);
  $action->parent->AddJsRef("WEBDESK:portal.js", true);
  $action->parent->AddCssRef("WEBDESK:webdesk.css", true);

  $colcount = getParam("WDK_COLCOUNT",3);
  $svclist_colcount = getParam("WDK_SVCCOLCOUNT",3);

  $action->lay->set("colCount", $colcount); 

  $cols = array();
  for ($icol=0; $icol<$colcount; $icol++) {
    $cols[] = array( "firstCol" => ($icol==0?true:false), "icol" => $icol );
  }
  $action->lay->setBlockData("cols", $cols); 
 
  //
  // List services ordered by category
  //
  $ts = GetChildDoc(getParam("FREEDOM_DB"), 0, 0, "ALL", array(), $action->user->id, "TABLE", "PORTAL_SERVICE");
  $tserv = array();
  $d = createDoc(getParam("FREEDOM_DB"), "PORTAL_SERVICE", false);
  $query=new QueryDb($action->dbaccess,"Application");
  foreach ($ts as $k => $v) {
    $access = true;
    $appn = getV($v, "psvc_appneeded");
    if ($appn!="") {
      $req = '';
      $appl = explode("|",$appn);
      foreach ($appl as $ka => $va) {
	$access = haveAppAccess($va);
	if (!$access) break;
      }
    }
    if ($access) {
      $ts[$k]["psvc_title_js"] = addslashes(getV($v, "psvc_title"));
      $d->Affect($v);
      $ts[$k]["Icon"] = false;
      $acat = $d->GetAttribute("psvc_categorie");
      $cat = $acat->getEnum();
      $tserv[getV($v,"psvc_categorie")]["categorie"] = $cat[getV($v,"psvc_categorie")];
      $tserv[getV($v,"psvc_categorie")]["svc"][] = $ts[$k];
    }
  }
  
  $svcols = array();
  $curcol = 0;
  foreach ($tserv as $k => $v) {
    $action->lay->setBlockData("services".$v["categorie"], $v["svc"]); 
    $svcols[$curcol]["nCols"] = $curcol;
    $svcols[$curcol]["content"][] = $tserv[$k];
    $curcol = ($curcol==$svclist_colcount-1?0:$curcol+1);
  }
  foreach ($svcols as $k => $v) {
    $action->lay->setBlockData("catS".$k, $v["content"]); 
  }
  $action->lay->setBlockData("COLS", $svcols); 
  $action->lay->set("colsCount", $svclist_colcount);
  $action->lay->set("colsWidth", (100/$svclist_colcount));
    

//   $action->lay->setBlockData("catS", $tserv); 
//   foreach ($tserv as $k => $v)  {
//     $action->lay->setBlockData("services".$v["categorie"], $v["svc"]); 
//   }


  // Initialise user services
  $tsvc = array();
  $tup = GetChildDoc( getParam("FREEDOM_DB"), 0, 0, "ALL", 
		     array("uport_ownerid = ".$action->user->fid), $action->user->id, "LIST", "USER_PORTAL");
  if (is_object($tup[0]) && $tup[0]->isAffected()) {
    
    $svcnum   = $tup[0]->getTValue("uport_svcnum");
    $svcid    = $tup[0]->getTValue("uport_idsvc");
    $svctitle = $tup[0]->getTValue("uport_svc");
    $svcparam = $tup[0]->getTValue("uport_param");
    $svcrdel  = $tup[0]->getTValue("uport_refreshd");
    $svccol   = $tup[0]->getTValue("uport_column");
    $svcline  = $tup[0]->getTValue("uport_line");
    $svcopen  = $tup[0]->getTValue("uport_open");

    foreach ($svcnum as $k => $v) {
      $sd = getTDoc(getParam("FREEDOM_DB"), $svcid[$k]);
      if (getV($sd, "psvc_vurl")=="") continue;
      $tsvc[] = array( "snum" => $v,
		       "sid" => $svcid[$k],
		       "stitle" => addslashes($svctitle[$k]),
		       "vurl" => getV($sd, "psvc_vurl"),
		       "eurl" => getV($sd, "psvc_eurl"),
		       "purl" => $svcparam[$k],
		       "jslink" => (getV($sd, "psvc_jsfile")!=""?Getparam("CORE_STANDURL")."&app=CORE&action=CORE_CSS&session=".$action->session->id."&layout=".getV($sd, "psvc_jsfile"):""),
		       "jslinkmd5" => md5(getV($sd, "psvc_jsfile")),
		       "csslink" => (getV($sd, "psvc_cssfile")!=""?Getparam("CORE_STANDURL")."&app=CORE&action=CORE_CSS&session=".$action->session->id."&layout=".getV($sd, "psvc_cssfile"):""),
		       "csslinkmd5" => md5(getV($sd, "psvc_cssfile")),
		       "rdel" => ($svcrdel[$k]==""?0:$svcrdel[$k]),
		       "col" => $svccol[$k],
		       "lin" => $svcline[$k],
		       "open" => ($svcopen[$k]==1?"true":"false"),
		       "interactif" => (getV($sd, "psvc_interactif")==1?"true":"false"),
		       "mandatory" => "false",
		       "editable" => "true",
		      );
    }
  }
  $action->lay->setBlockData("USvc", $tsvc); 
  
    
}



  function haveAppAccess($appname) {
    global $action;
    $query=new QueryDb($action->dbaccess,"Application");

    // Check if application is installed and available
    $query->basic_elem->sup_where=array("name='".$appname."'","available='Y'","displayable='Y'");
    $list = $query->query(0,0,"TABLE");
    if ($query->nb<=0) return false;

    // User have permission ?
    if ($action->user->id==1) return true;
   
    $queryact=new QueryDb($action->dbaccess,"Action");
    $queryact->AddQuery("id_application=".$list[0]["id"]);
    $queryact->AddQuery("root='Y'");
    $listact = $queryact->Query(0,0,"TABLE");
    $root_acl_name=$listact[0]["acl"];
    if (!$action->HasPermission($root_acl_name,$list[0]["id"])) return false;

    return true;
  }
?>
