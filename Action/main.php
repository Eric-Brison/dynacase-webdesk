<?php
/**
 * Generated Header (not documented yet)
 *
 * @author Anakeen 2000 
 * @version $Id: main.php,v 1.11 2006/11/30 12:10:35 marc Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package FREEDOM
 * @subpackage WEBDESK
 */
 /**
 */

include_once('Class.QueryDb.php');
include_once('Class.Application.php');

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

function main(&$action) {

  $action->lay->set("Workspace", haveAppAccess("WORKSPACE"));
  $action->lay->set("MailAccount", haveAppAccess("MAIL"));
  $action->lay->set("Agenda", haveAppAccess("WGCAL"));
    
  $action->parent->AddJsRef("WEBDESK:main.js", true);
  $action->parent->AddJsRef("FDC:setparamu.js", true);
  $action->parent->AddCssRef("WEBDESK:webdesk.css", true);
  

  $action->lay->set("IsMBarStatic", getParam("WDK_MBARSTATIC",1)==0); 
  $action->lay->set("userRealName",$action->user->firstname." ".$action->user->lastname); 
  $action->lay->set("userDomain",getParam("CORE_CLIENT"));
  $action->lay->set("sessionId",$action->session->id); 
  $action->lay->set("title", ""); 
  $action->lay->set("PHP_AUTH_USER",$_SERVER['PHP_AUTH_USER']);    

  $defApp = false;


  $appb = getParam("WDK_BARAPP", "");
  $tapp = array();
  if ($appb!="") {
    $tapp = explode("|", $appb);
  }
  
  // Get application list

  $query=new QueryDb($action->dbaccess,"Application");
  $query->basic_elem->sup_where=array("available='Y'","displayable='Y'", "name!='WEBDESK'");
  $list = $query->Query(0,0,"TABLE");
  $tab = array();
  $appinbar = array();
  if ($query->nb > 0) {
    $i=0; $j=0;
    foreach($list as $k=>$appli) {
      if ($appli["access_free"] == "N") {
        $action->log->debug("Access not free for :".$appli["name"]);
        if (isset($action->user)) {
	  if ($action->user->id != 1) { // no control for user Admin
	    // search  acl for root action
	    $queryact=new QueryDb($action->dbaccess,"Action");
	    $queryact->AddQuery("id_application=".$appli["id"]);
	    $queryact->AddQuery("root='Y'");
	    $listact = $queryact->Query(0,0,"TABLE");
	    $root_acl_name=$listact[0]["acl"];
	    if (! $action->HasPermission($root_acl_name,$appli["id"])) continue;
	  }
        } else { continue; }
      }
      $appli["description"]= $action->text($appli["description"]); // translate
      $appli["short_name"]= $action->text($appli["short_name"]); // translate
      $appli["jsname"]= addslashes($action->text($appli["short_name"])); // translate
      $appli["descriptionsla"]= addslashes($appli["description"]); // because its in between '' in layout
      if ($appli["machine"] != "") $appli["pubdir"]= "http://".$appli["machine"]."/what";
      else $appli["pubdir"]=$action->getParam("CORE_PUBURL");
      $appli["iconsrc"]=$action->GetImageUrl($appli["icon"]);
      if ($appli["iconsrc"]=="CORE/Images/noimage.png") $appli["iconsrc"]=$appli["name"]."/Images/".$appli["icon"];
      $appli["params"] = "";
      $tab[$i++]=$appli;
      if (in_array($appli["id"],$tapp) || in_array($appli["name"],$tapp)) $appinbar[$j++] = $appli;
    }
  }
  $action->lay->setBlockData("appList", $tab);
  $action->lay->setBlockData("appListBody", $tab);

  $specialapp[] = array( "id" => "100000", 
			 "short_name" => _("My portal"), 
			 "jsname" => addslashes(_("My portal")), 
			 "description" => _("My portal"), 
			 "name" => "WEBDESK", 
			 "params" => "&action=PORTAL",
			 "iconsrc" => "[IMG:wd_portal.gif]" );
  if (in_array("100000",$tapp) || in_array("WEBDESK",$tapp)) $appinbar[$j++] = $specialapp[0];

  $specialapp[] = array( "id" => "100001", 
			 "short_name" => _("My account"), 
			 "jsname" => addslashes(_("My account")), 
			 "name" => "WEBDESK", 
			 "description" => _("Webdesk preferences"), 
			 "params" => "&action=PREFERENCES",
			 "iconsrc" => "[IMG:wd_myaccount.png]" );
// 			 "iconsrc" => "[IMG:wd_preferences.gif]" );
  $cexec = $action->canExecute("ADMINS", $action->parent->id);
  if ($cexec=="") {
    $specialapp[] = array( "id" => "100020", 
			   "short_name" => _("Administration"), 
			   "description" => _("Webdesk administration"), 
			   "name" => "WEBDESK", 
			   "params" => "&action=ADMINS",
			   "iconsrc" => "[IMG:wd_admin.gif]" );
  }

  $action->lay->setBlockData("specialAppList", $specialapp);
  $action->lay->setBlockData("specialAppListBody", $specialapp);

  $action->lay->setBlockData("barAppList", $appinbar);


  $m_bgcolor = GetParam("WDESK_MENUCOLOR", GetParam("COLOR_A7"));
  $action->lay->set("menu_bgcolor", $m_bgcolor);
  $m_bgimagew = GetParam("WDESK_MENUIMGW", "15");
  $action->lay->set("menu_imgwidth", $m_bgimagew );
  $m_bgimage = GetParam("WDESK_MENUIMAGE", "webdesk-logo.png");
  $action->lay->set("menu_bgimage", $action->GetImageUrl($m_bgimage));

  if (!$defApp) {
    $action->lay->set("defid", $specialapp[0]["id"]);
    $action->lay->set("defname", $specialapp[0]["name"]);
    $action->lay->set("defparams", $specialapp[0]["params"]);
  }    	   
  
}
?>
