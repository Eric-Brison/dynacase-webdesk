<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WEBDESK
*/

include_once ('FDL/Lib.Dir.php');
function getjsservice(Action & $action)
{
    
    $dbaccess = getParam("FREEDOM_DB");
    
    $snum = GetHttpVars("snum", "");
    if ($snum < 0 || $snum == "") {
        $action->lay->set("OUT", "var svc = false;");
        return;
    }
    
    $tup = GetChildDoc(getParam("FREEDOM_DB") , 0, 0, "ALL", array(
        "uport_ownerid = '" . $action->user->fid . "'"
    ) , $action->user->id, "LIST", "USER_PORTAL");
    if (count($tup) < 1 || !$tup[0]->isAffected()) {
        $action->lay->set("OUT", "var svc = false;");
    } else {
        $up = $tup[0];
    }
    /**
     * @var Doc $up
     */
    $svcnum = $up->getTValue("uport_svcnum");
    $svcid = $up->getTValue("uport_idsvc");
    $svctitle = $up->getTValue("uport_svc");
    $svcparam = $up->getTValue("uport_param");
    $svcrdel = $up->getTValue("uport_refreshd");
    $svccol = $up->getTValue("uport_column");
    $svcline = $up->getTValue("uport_line");
    
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
    // $jslay = new Layout($jfile, $action);
    // $action->parent->AddJsCode($jslay->gen());
    $ret = "var svc = { " . "     snum:" . $snum . "," . "     sid:" . $sid . "," . "     stitle:'" . addslashes(getV($svc, "psvc_title")) . "'," . "     vurl:'" . getV($svc, "psvc_vurl") . "'," . "     eurl:'" . getV($svc, "psvc_eurl") . "'," . "     jslink:'" . (getV($svc, "psvc_jsfile") != "" ? Getparam("CORE_STANDURL") . "&app=CORE&action=CORE_CSS&session=" . $action->session->id . "&layout=" . getV($svc, "psvc_jsfile") : "") . "'," . "     jslinkmd5:'" . md5(getV($svc, "psvc_jsfile")) . "'," . "     csslink:'" . (getV($svc, "psvc_cssfile") != "" ? Getparam("CORE_STANDURL") . "&app=CORE&action=CORE_CSS&session=" . $action->session->id . "&layout=" . getV($svc, "psvc_cssfile") : "") . "'," . "     csslinkmd5:'" . md5(getV($svc, "psvc_cssfile")) . "'," . "     purl:'" . $sparam . "'," . "     rdel:" . $rdel . "," . "     nextLoad:-1," . "     col:" . $scol . "," . "     lin:" . $slin . "," . "     open:true," . "     i:" . (getV($svc, "psvc_interactif") == 1 ? "true" : "false") . "," . "     m:" . (getV($svc, "psvc_mandatory") == 1 ? "true" : "false") . "," . "     e:" . (getV($svc, "psvc_umode") == 1 ? "true" : "false") . "," . "     d:false };";
    
    $action->lay->set("OUT", $ret);
}
?>
