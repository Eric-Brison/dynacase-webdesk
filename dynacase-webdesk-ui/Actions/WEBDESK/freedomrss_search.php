<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WEBDESK
*/

include_once ("WHAT/Lib.Http.php");
include_once ("WHAT/Class.Layout.php");
include_once ("FDL/Lib.Dir.php");

function freedomrss_search(Action &$action)
{
    
    $str = GetHttpVars("str", "");
    $sys = GetHttpVars("sys", 0);
    $user = GetHttpVars("user", $action->user->id);
    $lim = 10;
    
    $dbaccess = getParam("FREEDOM_DB");
    
    $filter[0] = "(title ~* '" . pg_escape_string($str) . "')";
    $filter[1] = "(gui_isrss = 'yes')";
    if ($sys == 1) $filter[2] = "(owner = " . pg_escape_string($user) . " or gui_sysrss = 'yes')";
    else $filter[2] = "(owner = " . pg_escape_string($user) . ")";
    
    $famids = array(
        "SEARCH",
        "DIR"
    );
    $tdoc = array();
    foreach ($famids as $vf) {
        $search = new SearchDoc("", $vf);
        $search->setSlice($lim);
        foreach ($filter as $currentFilter) {
            $search->addFilter($currentFilter);
        }
        $stdoc = $search->search();
        $tdoc = array_merge($tdoc, $stdoc);
    }
    $total = count($tdoc);
    
    $moreresult = ($total > $lim ? true : false);
    $cn = ($total > $lim ? $lim : $total);
    
    if ($moreresult) $atext = sprintf(_("more than %d results...") , $cn);
    else if ($cn > 0) $atext = $cn . " " . _("wd: result") . ($cn > 1 ? "s" : "");
    else $atext = _("no result for search");
    $action->lay->set("atext", $atext);
    
    $tfam = array();
    $tr = array();
    if ($cn > 0) {
        foreach ($tdoc as $k => $v) {
            if (!isset($tfam[$v["fromid"]])) $tfam[$v["fromid"]] = rssGetFamTitle($v["fromid"]);
            $tr[] = array(
                "title" => preg_replace('/(' . $str . '?)/i', '<span class="rss_warn">\1</span>', $v["title"]) ,
                "jstitle" => addslashes($v["title"] . " [" . $tfam[$v["fromid"]] . "]") ,
                "id" => $v["id"],
                "fam" => $tfam[$v["fromid"]]
            );
        }
    }
    $action->lay->setBlockData("result", $tr);
}

function rssGetFamTitle($id)
{
    $t = getTDoc(getParam("FREEDOM_DB") , $id);
    if (isset($t["title"])) return $t["title"];
    return "Family $id";
}
?>
