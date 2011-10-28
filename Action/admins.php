<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FDL
 */
/**
 * Generated Header (not documented yet)
 *
 * @author Anakeen 2000
 * @version $Id: admins.php,v 1.4 2008/12/30 17:07:40 eric Exp $
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FREEDOM
 * @subpackage WEBDESK
 */
/**
 */

include_once ('Class.QueryDb.php');
include_once ('Class.Application.php');

function admins(&$action)
{
    
    $action->parent->AddCssRef("WEBDESK:webdesk.css", true);
    $action->parent->AddJsRef("WEBDESK:prefadmin.js", true);
    
    $action->parent->AddJsRef($action->GetParam("CORE_JSURL") . "/subwindow.js");
    $action->parent->AddJsRef($action->GetParam("CORE_JSURL") . "/DHTMLapi.js");
    $action->parent->AddJsRef($action->GetParam("CORE_JSURL") . "/AnchorPosition.js");
    $action->parent->AddJsRef($action->GetParam("CORE_JSURL") . "/geometry.js");
    $action->parent->AddJsRef($action->GetParam("CORE_JSURL") . "/resizeimg.js");
    // Get application list
    $query = new QueryDb($action->dbaccess, "Application");
    $query->basic_elem->sup_where = array(
        "available='Y'"
    );
    $list = $query->Query(0, 0, "TABLE");
    $tappli = array();
    foreach ($list as $k => $v) {
        $tappli[$v["id"]] = $v;
    }
    
    $qact = new QueryDb($action->dbaccess, "Action");
    $qact->addQuery("available='Y'");
    $qact->addQuery("name ~ '^ADMIN'");
    $qact->addQuery("name != 'ADMINS'");
    $tact = $qact->Query(0, 0, "TABLE");
    
    $tab = array();
    if ($qact->nb > 0) {
        $i = 0;
        foreach ($tact as $k => $act) {
            if ($act["available"] == "Y") {
                $appli = $tappli[$act["id_application"]];
                if ($appli && ($action->canExecute($act["name"], $appli["id"]) == "")) {
                    $appli["description"] = $action->text($appli["description"]); // translate
                    $appli["short_name"] = $action->text($act["short_name"]); // translate
                    $appli["descriptionsla"] = addslashes($appli["description"]); // because its in between '' in layout
                    if ($appli["machine"] != "") $appli["pubdir"] = "http://" . $appli["machine"] . "/what";
                    else $appli["pubdir"] = $action->getParam("CORE_PUBURL");
                    $appli["iconsrc"] = $action->GetImageUrl($appli["icon"]);
                    if ($appli["iconsrc"] == "CORE/Images/noimage.png") $appli["iconsrc"] = $appli["name"] . "/Images/" . $appli["icon"];
                    $appli["params"] = "";
                    $appli["action"] = $act["name"];
                    $tab[$i++] = $appli;
                }
            }
        }
    }
    $action->lay->setBlockData("applist", $tab);
}
?>
