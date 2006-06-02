<?php
/**
 * Generated Header (not documented yet)
 *
 * @author Anakeen 2000 
 * @version $Id: preferences.php,v 1.1 2006/06/02 15:01:50 marc Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package FREEDOM
 * @subpackage WEBDESK
 */
 /**
 */

include_once('Class.QueryDb.php');
include_once('Class.Application.php');

function preferences(&$action) {

  $action->parent->AddJsRef($action->GetParam("CORE_JSURL")."/subwindow.js");  
  $action->parent->AddJsRef($action->GetParam("CORE_JSURL")."/DHTMLapi.js");  
  $action->parent->AddJsRef($action->GetParam("CORE_JSURL")."/AnchorPosition.js");  
  $action->parent->AddJsRef($action->GetParam("CORE_JSURL")."/geometry.js");  
  // Get application list

  $query=new QueryDb($action->dbaccess,"Application");
  $query->basic_elem->sup_where=array("available='Y'","displayable='Y'");
  $list = $query->Query(0,0,"TABLE");
  $tab = array();
  if ($query->nb > 0) {
    $i=0;
    foreach($list as $k=>$appli) {
      if ($action->Exists("APPPREFS", $appli["id"])) {
	if ($action->canExecute("APPPREFS", $appli["id"])=="") {
	  $appli["description"]= $action->text($appli["description"]); // translate
	  $appli["short_name"]= $action->text($appli["short_name"]); // translate
	  $appli["descriptionsla"]= addslashes($appli["description"]); // because its in between '' in layout
	  if ($appli["machine"] != "") $appli["pubdir"]= "http://".$appli["machine"]."/what";
	  else $appli["pubdir"]=$action->getParam("CORE_PUBURL");
	  $appli["iconsrc"]=$action->GetImageUrl($appli["icon"]);
	  if ($appli["iconsrc"]=="CORE/Images/noimage.png") $appli["iconsrc"]=$appli["name"]."/Images/".$appli["icon"];
	  $appli["params"] = "";
	  $appli["action"] = "APPPREFS";
	  $tab[$i++]=$appli;
	}
      }
    }
  }
  $action->lay->setBlockData("applist", $tab);
}
?>
