<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WEBDESK
*/

include_once ('FDL/Lib.Dir.php');
function delservice(Action &$action)
{
    
    $inter = (GetHttpVars("silent", "no") == "no" ? true : false);
    
    $oid = GetHttpVars("oid", -1);
    if ($oid != - 1) $owner = $oid;
    
    $sid = GetHttpVars("sid", -1);
    $snum = GetHttpVars("snum", -1);
    if (($snum == - 1 || $snum == "") && $sid == - 1) {
        if ($inter) $action->lay->set("OUT", "var svcnum = -1;");
        return;
    }
    
    $search = new SearchDoc("", "USER_PORTAL");
    $search->addFilter("uport_ownerid = '%s'", $action->user->fid);
    $search->setSlice(1);
    $search->setObjectReturn();

    $search->search();

    /* @var $tup _USER_PORTAL */
    $tup = $search->getNextDoc();
    if (!is_object($tup) || !$tup->isAffected()) {
        $action->lay->set("OUT", "var svcnum = -1;");
        return;
    } else {
        /* @var $up Doc */
        $up = $tup;
    }
    $svcnum = $up->getMultipleRawValues("uport_svcnum");
    $svcid = $up->getMultipleRawValues("uport_idsvc");
    $svctitle = $up->getMultipleRawValues("uport_svc");
    $svcparam = $up->getMultipleRawValues("uport_param");
    $svcrdel = $up->getMultipleRawValues("uport_refreshd");
    $svccol = $up->getMultipleRawValues("uport_column");
    $svcline = $up->getMultipleRawValues("uport_line");
    $svcopen = $up->getMultipleRawValues("uport_open");
    
    $up->clearValue("uport_svcnum");
    $up->clearValue("uport_idsvc");
    $up->clearValue("uport_svc");
    $up->clearValue("uport_param");
    $up->clearValue("uport_refreshd");
    $up->clearValue("uport_column");
    $up->clearValue("uport_line");
    $up->clearValue("uport_open");
    
    $nsvcnum = array();
    $nsvcid = array();
    $nsvctitle = array();
    $nsvcparam = array();
    $nsvcrdel = array();
    $nsvccol = array();
    $nsvcline = array();
    $nsvcopen = array();
    $change = false;
    foreach ($svcnum as $k => $v) {
        if ($v != "" && $snum != $v && $sid != $svcid[$k]) {
            $nsvcnum[] = $svcnum[$k];
            $nsvcid[] = $svcid[$k];
            $nsvctitle[] = $svctitle[$k];
            $nsvcparam[] = $svcparam[$k];
            $nsvcrdel[] = $svcrdel[$k];
            $nsvccol[] = $svccol[$k];
            $nsvcline[] = $svcline[$k];
            $nsvcopen[] = $svcopen[$k];
        } else {
            $change = true;
        }
    }
    
    $up->setValue("uport_svcnum", $nsvcnum);
    $up->setValue("uport_idsvc", $nsvcid);
    $up->setValue("uport_svc", $nsvctitle);
    $up->setValue("uport_param", $nsvcparam);
    $up->setValue("uport_refreshd", $nsvcrdel);
    $up->setValue("uport_column", $nsvccol);
    $up->setValue("uport_line", $nsvcline);
    $up->setValue("uport_open", $nsvcopen);
    
    if ($change) {
        $err = $up->modify();
        $up->postStore();
        if ($inter) $action->lay->set("OUT", "var svcnum = $snum;");
    } else if ($inter) $action->lay->set("OUT", "var svcnum = -1;");
}
