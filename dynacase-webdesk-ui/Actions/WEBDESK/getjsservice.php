<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WEBDESK
*/

include_once 'FDL/Lib.Dir.php';

function getjsservice(Action & $action)
{
    
    $dbaccess = $action->dbaccess;
    
    $snum = GetHttpVars("snum", "");
    if ($snum < 0 || $snum == "") {
        $action->lay->set("OUT", "var svc = false;");
        return;
    }
    
    $search = new SearchDoc($dbaccess, "USER_PORTAL");
    $search->addFilter("uport_ownerid = '%s'", $action->user->fid);
    $search->setSlice(1);
    $search->setObjectReturn();
    
    $search->search();
    /* @var $tup _USER_PORTAL */
    $tup = $search->getNextDoc();
    if (!is_object($tup) || !$tup->isAffected()) {
        $action->lay->set("OUT", "var svc = false;");
    } else {
        $up = $tup;
    }
    /**
     * @var Doc $up
     */
    $svcnum = $up->getMultipleRawValues("uport_svcnum");
    $svcid = $up->getMultipleRawValues("uport_idsvc");
    $svctitle = $up->getMultipleRawValues("uport_svc");
    $svcparam = $up->getMultipleRawValues("uport_param");
    $svcrdel = $up->getMultipleRawValues("uport_refreshd");
    $svccol = $up->getMultipleRawValues("uport_column");
    $svcline = $up->getMultipleRawValues("uport_line");
    
    $sid = - 1;
    foreach ($svcnum as $k => $v) {
        if ($v == $snum) {
            $sid = $svcid[$k];
            $stitle = $svctitle[$k];
            $sparam = $svcparam[$k];
            $rdel = ($svcrdel[$k] == "" ? 0 : $svcrdel[$k]);
            $scol = $svccol[$k];
            $slin = $svcline[$k];
            continue;
        }
    }
    if ($sid == - 1) {
        $action->lay->set("OUT", "var svc = false;");
        return;
    }
    
    $svc = getTDoc($dbaccess, $sid);
    Webdesk\Util::parseUrl("", $action);
    $ret = "var svc = { " . "     snum:" . $snum . "," . "     sid:" . $sid . "," . "     stitle:'" . addslashes(getV($svc, "psvc_title")) . "'," . "     vurl:'" . Webdesk\Util::parseUrl(getV($svc, "psvc_vurl")) . "'," . "     eurl:'" . Webdesk\Util::parseUrl(getV($svc, "psvc_eurl")) . "'," . "     jslink:'" . (getV($svc, "psvc_jsfile") != "" ? Getparam("CORE_STANDURL") . "&app=CORE&action=CORE_CSS&session=" . $action->session->id . "&layout=" . getV($svc, "psvc_jsfile") : "") . "'," . "     jslinkmd5:'" . md5(getV($svc, "psvc_jsfile")) . "'," . "     csslink:'" . (getV($svc, "psvc_cssfile") != "" ? Getparam("CORE_STANDURL") . "&app=CORE&action=CORE_CSS&session=" . $action->session->id . "&layout=" . getV($svc, "psvc_cssfile") : "") . "'," . "     csslinkmd5:'" . md5(getV($svc, "psvc_cssfile")) . "'," . "     purl:'" . $sparam . "'," . "     rdel:" . $rdel . "," . "     nextLoad:-1," . "     col:" . $scol . "," . "     lin:" . $slin . "," . "     open:true," . "     i:" . (getV($svc, "psvc_interactif") == 1 ? "true" : "false") . "," . "     m:" . (getV($svc, "psvc_mandatory") == 1 ? "true" : "false") . "," . "     e:" . (getV($svc, "psvc_umode") == 1 ? "true" : "false") . "," . "     d:false };";
    
    $action->lay->set("OUT", $ret);
}
