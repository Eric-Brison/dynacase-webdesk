<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WEBDESK
*/

include_once ('FDL/Lib.Dir.php');
function addservice(&$action)
{
    
    $dbaccess = $action->getParam("FREEDOM_DB");
    
    $silent = GetHttpVars("silent", "no");
    $oid = GetHttpVars("oid", -1);
    $sid = GetHttpVars("sid", -1);
    
    if ($sid == - 1 || $sid == "") {
        $action->lay->set("OUT", "var svcnum = -1;");
    }
    
    $owner = $action->user->fid;
    if ($oid != - 1) $owner = $oid;
    $downer = new_Doc($dbaccess, $owner);
    
    $tup = GetChildDoc($dbaccess, 0, 0, "ALL", array(
        "uport_ownerid = '" . $owner . "'"
    ) , $action->user->id, "LIST", "USER_PORTAL");
    if (count($tup) < 1 || !$tup[0]->isAffected()) {
        $up = createDoc($dbaccess, "USER_PORTAL");
        $up->setValue("uport_ownerid", $owner);
        $up->setValue("uport_owner", $downer->getValue("us_firstname") . " " . $downer->getValue("us_lastname"));
        $up->setValue("uport_title", "Mon portail (" . $downer->getValue("us_firstname") . " " . $downer->getValue("us_lastname") . ")");
        $up->Add();
        $svcnum = $svcid = $svctitle = $svcparam = $svcrdel = $svccol = $svcline = array();
    } else {
        $up = $tup[0];
        $svcnum = $up->getTValue("uport_svcnum");
        $svcid = $up->getTValue("uport_idsvc");
        $svctitle = $up->getTValue("uport_svc");
        $svcparam = $up->getTValue("uport_param");
        $svcrdel = $up->getTValue("uport_refreshd");
        $svccol = $up->getTValue("uport_column");
        $svcline = $up->getTValue("uport_line");
        $svcopen = $up->getTValue("uport_open");
    }
    
    $svnnumber = $up->getNumSequence();
    
    $svc = getTDoc($dbaccess, $sid);
    $svcnum[] = $svnnumber;
    $svcid[] = $sid;
    $svctitle[] = getV($svc, "psvc_title");
    $svcparam[] = " ";
    $svcrdel[] = (getV($svc, "psvc_refreshd") == "" ? 0 : getV($svc, "psvc_refreshd"));
    $svccol[] = 0;
    $svcline[] = 0;
    $svcopen[] = 1;
    $up->setValue("uport_svcnum", $svcnum);
    $up->setValue("uport_idsvc", $svcid);
    $up->setValue("uport_svc", $svctitle);
    $up->setValue("uport_param", $svcparam);
    $up->setValue("uport_refreshd", $svcrdel);
    $up->setValue("uport_column", $svccol);
    $up->setValue("uport_line", $svcline);
    $up->setValue("uport_open", $svcopen);
    
    $err = $up->modify();
    $up->postModify();
    if ($silent != "yes") $action->lay->set("OUT", "var svcnum = $svnnumber;");
}
?>
