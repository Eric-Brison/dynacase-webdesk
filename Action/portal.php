<?php
/**
 * Generated Header (not documented yet)
 *
 * @author Anakeen 2000 
 * @version $Id: portal.php,v 1.26 2006/11/28 18:32:52 marc Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package FREEDOM
 * @subpackage WGCAL
 */
 /**
 */

include_once('FDL/Lib.Dir.php');
function portal(&$action) {
   

  $dbaccess = getParam("FREEDOM_DB");

//  $debug = true;
   $debug = false;

  $action->parent->AddJsRef("WHAT:subwindow.js", true);
  $action->parent->AddJsRef("FDL:common.js", true);
  $action->lay->set("debug", $debug);
  if (!$debug) $action->parent->AddJsRef("WEBDESK:portal.js", true);
  else {
    $jslay = new Layout("WEBDESK/Layout/portal.js", $action);
    $action->parent->AddJsCode($jslay->gen());
  }
  $action->parent->AddCssRef("WEBDESK:webdesk.css", true);

  $colcount = getParam("WDK_COLCOUNT",3);
  $svclist_colcount = getParam("WDK_SVCCOLCOUNT",3);

  $action->lay->set("colCount", $colcount); 

  $cwidth = floor(100/$colcount);
  $cols = array();
  for ($icol=0; $icol<$colcount; $icol++) {
    $cols[] = array( "firstCol" => ($icol==0?true:false), "icol" => $icol, "cwidth" => $cwidth );
  }
  $action->lay->setBlockData("cols", $cols); 
 
  //
  // List services ordered by category
  //
  $ts = GetChildDoc($dbaccess, 0, 0, "ALL", array(), $action->user->id, "TABLE", "PORTAL_SERVICE");
  $tserv = array();
  $tsubserv = array();
  $d = createDoc($dbaccess, "PORTAL_SERVICE", false);
  $acat = $d->GetAttribute("psvc_categorie");
  $cat = $acat->getEnum();

  $ordercat = array();
  foreach ($cat as $kc => $vc) {
    $tc = explode(".", $kc);
    $tt = explode("/", $vc);
    $ordercat[$kc]["father"] = (isset($tc[count($tc)-2])?$tc[count($tc)-2]:$tc[count($tc)-1]);
    $ordercat[$kc]["level"] = count($tc);
    $ordercat[$kc]["label"] = $tt[count($tc)-1];
    $ordercat[$kc]["key"] = $kc;
  }

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
      $num_cat = getV($v,"psvc_categorie");
      if (!isset($ordercat[$num_cat])) $num_cat = -1;
      $cattitle = $ordercat[$num_cat]["label"];
      $catlevel = $ordercat[$num_cat]["level"];
      $catfather = $ordercat[$num_cat]["father"];
      $ts[$k]["psvc_title_js"] = addslashes(getV($v, "psvc_title"));
      $ts[$k]["psvc_title"] = getV($v, "psvc_title");
      $ts[$k]["Icon"] = false;
      $ts[$k]["subc_title"] = $cattitle;
      $ts[$k]["num"] = $num_cat;
      if ($catlevel==1) {
	$tserv[$num_cat]["categorie"] = $cattitle;
	$ts[$k]["issubcat"] = false;
	$tserv[$num_cat]["svc"][] = $ts[$k];
      } else {
	$fcat = $ordercat[$num_cat]["father"];
	$tserv[$fcat]["categorie"] = $ordercat[$fcat]["label"];
	$ts[$k]["issubcat"] = true;
	$ts[$k]["num"] = $num_cat;
	if (!isset($tsubserv[$num_cat])) {
 	  $tserv[$catfather]["svc"][] = $ts[$k];
	  $tsubserv[$num_cat]["title"] = $cattitle;
	  $tsubserv[$num_cat]["num"] = $num_cat;
	  $tsubserv[$num_cat]["svc"] = array();
	}
	$tsubserv[$num_cat]["svc"][] = $ts[$k];
      }
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
    
  
  foreach ($tsubserv as $k => $v) {
    $action->lay->setBlockData("subcatserv".$v["num"], $v["svc"]);
  }
  $action->lay->setBlockData("subcat", $tsubserv);


  // Initialise user services
  $tsvc = array();
  $tup = GetChildDoc( $dbaccess, 0, 0, "ALL", 
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
      $tsvc[] = array( "rg" => count($tsvc),
		       "snum" => $v,
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
  } else {
    $welc = getIdFromName($dbaccess, "PS_WELCOME");
    if (is_numeric($welc) && $welc>0) {
      $svc = new_Doc($dbaccess, $welc);
      if ($svc->isAffected()) {
	$up = createDoc($dbaccess, "USER_PORTAL");
	$up->setValue("uport_ownerid", $action->user->fid);
	$up->setValue("uport_owner", $action->user->firstname." ".$action->user->firstname);
	$up->setValue("uport_title", "Mon portail (".$action->user->firstname." ".$action->user->firstname. ")");
	$up->Add();
	$svcnum   = $svcid = $svctitle = $svcparam = $svcrdel = $svccol = $svcline = array();
	$svcnumber = $up->getNumSequence();
	$svcnum[]   = $svcnumber;
	$svcid[]    = $svc->id;
	$svctitle[] = $svc->getValue("psvc_title");
	$svcparam[] = " ";
	$svcrdel[]   = 0;
	$svccol[]   = 0;
	$svcline[]   = 0;
	$svcopen[] = 1;
	$up->setValue("uport_svcnum",$svcnum);
	$up->setValue("uport_idsvc",$svcid);
	$up->setValue("uport_svc",$svctitle);
	$up->setValue("uport_param",$svcparam);
	$up->setValue("uport_refreshd",$svcrdel);
	$up->setValue("uport_column",$svccol);
	$up->setValue("uport_line",$svcline);
	$up->setValue("uport_open", $svcopen);
	$err = $up->modify();
	$up->postModify();
	
	$tsvc[] = array( "rg" => count($tsvc),
			 "snum" => $svcnumber,
			 "sid" => $svc->id,
			 "stitle" => addslashes($svc->getValue("psvc_title")),
			 "vurl" => $svc->getValue("psvc_vurl"),
			 "eurl" => $svc->getValue("psvc_eurl"),
			 "purl" => "",
			 "jslink" => "",
			 "jslinkmd5" => "",
			 "csslink" => "",
			 "csslinkmd5" => "",
			 "rdel" => 0, 
			 "col" => 0, 
			 "lin" => 0, 
			 "open" => "true", 
			 "interactif" => "false",  
			 "mandatory" => "false", 
			 "editable" => "true",
			 );
      }
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
