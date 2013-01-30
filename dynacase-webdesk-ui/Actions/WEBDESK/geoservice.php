<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WEBDESK
*/

include_once ('FDL/Lib.Dir.php');
function geoservice(Action &$action)
{
    
    $spec = GetHttpVars("sgeo", "");
    if ($spec == "") {
        $action->lay->set("OUT", "var svcnum = -1; // spec empty");
        return;
    }
    $tspec = explode("|", $spec);
    $svcgeo = array();
    foreach ($tspec as $k => $v) {
        $s = explode(":", $v);
        if (!is_numeric($s[0]) || !is_numeric($s[1]) || !is_numeric($s[2])) continue;
        if (!isset($s[3])) $s[3] = 1;
        $svcgeo[$s[0]] = array(
            "snum" => $s[0],
            "col" => $s[1],
            "lin" => $s[2],
            "open" => $s[3]
        );
    }
    
    $search = new SearchDoc("", "USER_PORTAL");
    $search->addFilter("uport_ownerid = '%s'", $action->user->fid);
    $search->setSlice(1);
    $search->setObjectReturn();

    $search->search();

    /* @var $tup _USER_PORTAL */
    $tup = $search->getNextDoc();
    if (!is_object($tup) || !$tup->isAffected()) {
        $action->lay->set("OUT", "var svcnum = -1; // no portal");
        return;
    } else {
        /* @var $up Doc */
        $up = $tup;
    }
    
    $svcnum = $up->getMultipleRawValues("uport_svcnum");
    $svccol = $up->getMultipleRawValues("uport_column");
    $svcline = $up->getMultipleRawValues("uport_line");
    $svcopen = $up->getMultipleRawValues("uport_open");
    
    $change = false;
    foreach ($svcnum as $k => $v) {
        if (isset($svcgeo[$v])) {
            $svccol[$k] = $svcgeo[$v]["col"];
            $svcline[$k] = $svcgeo[$v]["lin"];
            $svcopen[$k] = $svcgeo[$v]["open"];
            echo '  svn(' . $v . ') [col=' . $svccol[$k] . ';lin=' . $svcline[$k] . ']<br>';
            $change = true;
        }
    }
    if ($change) {
        $up->setValue("uport_column", $svccol);
        $up->setValue("uport_line", $svcline);
        $up->setValue("uport_open", $svcopen);
        $err = $up->modify();
        $up->postStore();
        $action->lay->set("OUT", "var svcnum = false;");
    } else {
        $action->lay->set("OUT", "var svcnum = -1; // no modif");
    }
}
