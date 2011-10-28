<?php
/**
 * Generated Header (not documented yet)
 *
 * @author Anakeen 2000 
 * @version $Id: preferences.php,v 1.5 2007/10/16 09:07:27 eric Exp $
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FREEDOM
 * @subpackage WEBDESK
 */
 /**
 */

include_once('Class.QueryDb.php');
include_once('Class.Application.php');

function preferences(&$action) {

  $action->parent->AddCssRef("WEBDESK:webdesk.css", true);
  $action->parent->AddJsRef("WEBDESK:prefadmin.js", true);
  $action->parent->AddJsRef($action->GetParam("CORE_JSURL")."/subwindow.js");  
  $action->parent->AddJsRef($action->GetParam("CORE_JSURL")."/DHTMLapi.js");  
  $action->parent->AddJsRef($action->GetParam("CORE_JSURL")."/AnchorPosition.js");  
  $action->parent->AddJsRef($action->GetParam("CORE_JSURL")."/geometry.js");   
  $action->parent->AddJsRef($action->GetParam("CORE_JSURL")."/resizeimg.js");  
  // Get application list
  $fuid=$action->user->fid;
  $query=new QueryDb($action->dbaccess,"Application");
//   $query->basic_elem->sup_where=array("available='Y'","displayable='Y'");
  $query->basic_elem->sup_where=array("available='Y'");
  $list = $query->Query(0,0,"TABLE");
  $tab = array();
  $tab[]=array("description"=>_("Theme preferences"),
	       "short_name"=>_("Theme"),
	       "iconsrc"=>$action->GetImageUrl("utheme.png"),
	       "id"=>"x1",
	       "action"=>"THEME",
	       "name"=>"WEBDESK");
  $tab[]=array("description"=>_("User identification"),
	       "short_name"=>_("Identification"),
	       "iconsrc"=>$action->GetImageUrl("uident.png"),
	       "id"=>"x2",
	       "action"=>"IMPCARD&id=$fuid&zone=WEBDESK:USERIDENT:T",
	       "name"=>"FDL");


  if ($query->nb > 0) {
    foreach($list as $k=>$appli) {
      if ($action->Exists("APPPREFS", $appli["id"])) {
	if ($action->canExecute("APPPREFS", $appli["id"])=="") {
	  $appli["description"]= $action->text($appli["description"]); // translate
	  $appli["short_name"]= $action->text($appli["short_name"]); // translate
	  $appli["iconsrc"]=$action->GetImageUrl($appli["icon"]);
	  if ($appli["iconsrc"]=="CORE/Images/noimage.png") $appli["iconsrc"]=$appli["name"]."/Images/".$appli["icon"];	      
	  $appli["action"] = "APPPREFS";
	  $tab[]=$appli;
	}
      }
    }
  }

  $action->lay->setBlockData("applist", $tab);
}
?>
