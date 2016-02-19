<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WEBDESK
*/

include_once 'FDL/Lib.Dir.php';
function addservice(Action & $action)
{
    
    $dbaccess = $action->dbaccess;
    
    $silent = GetHttpVars("silent", "no");
    $oid = GetHttpVars("oid", -1);
    $sid = GetHttpVars("sid", -1);
    
    if ($sid == - 1 || $sid == "") {
        $action->lay->set("OUT", "var svcnum = -1;");
    }
    
    $owner = $action->user->fid;
    if ($oid != - 1) $owner = $oid;
    $downer = new_Doc($dbaccess, $owner);
    
    $search = new SearchDoc("", "USER_PORTAL");
    $search->addFilter("uport_ownerid = '%s'", $action->user->fid);
    $search->setSlice(1);
    $search->setObjectReturn();
    
    $search->search();
    /* @var \Dcp\Family\User_portal $tup */
    $tup = $search->getNextDoc();
    if (!is_object($tup) || !$tup->isAffected()) {
        $tup = createDoc($dbaccess, "USER_PORTAL");
        $tup->setValue("uport_ownerid", $owner);
        $tup->setValue("uport_owner", $downer->getRawValue("us_firstname") . " " . $downer->getRawValue("us_lastname"));
        $tup->setValue("uport_title", "Mon portail (" . $downer->getRawValue("us_firstname") . " " . $downer->getRawValue("us_lastname") . ")");
        $tup->Add();
        $svcnum = $svcid = $svctitle = $svcparam = $svcrdel = $svccol = $svcline = array();
    } else {
        /** @var $up _USER_PORTAL */
        $svcnum = $tup->getMultipleRawValues("uport_svcnum");
        $svcid = $tup->getMultipleRawValues("uport_idsvc");
        $svctitle = $tup->getMultipleRawValues("uport_svc");
        $svcparam = $tup->getMultipleRawValues("uport_param");
        $svcrdel = $tup->getMultipleRawValues("uport_refreshd");
        $svccol = $tup->getMultipleRawValues("uport_column");
        $svcline = $tup->getMultipleRawValues("uport_line");
        $svcopen = $tup->getMultipleRawValues("uport_open");
    }
    
    $svnnumber = $tup->getNumSequence();
    
    $svc = getTDoc($dbaccess, $sid);
    $svcnum[] = $svnnumber;
    $svcid[] = $sid;
    $svctitle[] = getV($svc, "psvc_title");
    $svcparam[] = " ";
    $svcrdel[] = (getV($svc, "psvc_refreshd") == "" ? 0 : getV($svc, "psvc_refreshd"));
    $svccol[] = 0;
    $svcline[] = 0;
    $svcopen[] = 1;
    $tup->setValue("uport_svcnum", $svcnum);
    $tup->setValue("uport_idsvc", $svcid);
    $tup->setValue("uport_svc", $svctitle);
    $tup->setValue("uport_param", $svcparam);
    $tup->setValue("uport_refreshd", $svcrdel);
    $tup->setValue("uport_column", $svccol);
    $tup->setValue("uport_line", $svcline);
    $tup->setValue("uport_open", $svcopen);
    
    $tup->store();
    if ($silent != "yes") {
        $action->lay->set("OUT", "var svcnum = $svnnumber;");
    }
}

