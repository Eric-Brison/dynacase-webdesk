<?php
/**
 * see webdesk user preferences
 *
 * @author Anakeen 2006
 * @version $Id: appprefs.php,v 1.1 2006/08/07 16:31:49 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package WEBDESK
 * @subpackage 
 */
 /**
 */
include_once("FDL/Class.Doc.php");

/**
 * Display webdesk user preferences
 * @param Action &$action current action
 */
function appprefs(&$action) {
  $dbaccess = $action->GetParam("FREEDOM_DB");
  $styleid= $action->getParam("STYLE","DEFAULT");
  $sizeid= $action->getParam("FONTSIZE","normal");
  
  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/FDC/Layout/setparamu.js");


  //----------------------------------------
  // the style
  $query = new QueryDb($action->dbaccess,"Style");
  $query->AddQuery("name !~ 'SIZE_'");
  $list = $query->Query(0,0,"TABLE");
  // select the wanted style
    while (list($k,$v)=each($list)) {
	if ($v["name"] == $styleid) $list[$k]["selected"]="selected";
	else $list[$k]["selected"]="";
    }

  $action->lay->SetBlockData("SELSTYLE",$list);

  //----------------------------------------
  // the size
  $query = new QueryDb($action->dbaccess,"Style");
  $query->AddQuery("name ~ 'SIZE_'");
  $list = $query->Query(0,0,"TABLE");
  // select the wanted style
    while (list($k,$v)=each($list)) {
	if ($v["name"] == $sizeid) $list[$k]["selected"]="selected";
	else $list[$k]["selected"]="";
    }

  $action->lay->SetBlockData("SELSIZE",$list);



  
}
?>
