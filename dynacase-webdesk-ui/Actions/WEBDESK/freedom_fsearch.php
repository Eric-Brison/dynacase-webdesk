<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WEBDESK
*/

include_once ('FDL/Lib.Dir.php');

function freedom_fsearch(Action & $action)
{

    header('Content-type: text/xml; charset=utf-8');

    $sphrase = GetHttpVars("sphrase", "");
    $tcheck = GetHttpVars("tcheck", 0);
    $max = GetHttpVars("max", 10);

    $dbaccess = getParam("FREEDOM_DB");
    //Nouveau paramètre 'family'
    //S'il est renseigné dans l'URL, le choix de la famille n'est plus proposé
    //Pour faire une recherche dans toutes les familles sans afficher le choix de la famille, il faut indiquer family=toutes
    $family = GetHttpVars("family", "");
    if ($family == "") {
        $sfamily = GetHttpVars("sfamily", "");
        $action->lay->set("widthselect", "45");
        $action->lay->set("selectfam", 1);
    } else {
        $sfamily = $family;
        $action->lay->set("widthselect", "90");
        $action->lay->set("selectfam", 0);
    }
    // Si le choix de la famille n'est pas proposé, ce n'est pas la peine de rechercher la liste
    if ($family == "") {
        // Interface init
        $tclass = GetClassesDoc($dbaccess, $action->user->id, 0, "TABLE", "usefor !~ '^S'");
        $stclass[] = array(
            "value" => 0,
            "sel" => "",
            "label" => "toutes"
        );
        foreach ($tclass as $k => $v) {
            $stclass[] = array(
                "value" => $v["initid"],
                "sel" => ($sfamily == $v["id"] || $sfamily == $v["name"] ? "selected" : ""),
                "label" => $v["title"]
            );
        }
        $action->lay->SetBlockData("SFam", $stclass);
    }

    $action->lay->set("vtcheck", $tcheck);
    $action->lay->set("bcheck", ($tcheck == 1 ? "checked" : ""));
    $action->lay->set("sphrase", $sphrase);
    $action->lay->set("csearch", false);
    # Lignes ajoutées pour que le titre corresponde au nom du document service-portail
    $sid = GetHttpVars("sid", -1);
    $svc = getTDoc($dbaccess, $sid);
    $title = getV($svc, "psvc_title");
    $action->lay->set("title", $title);

    if ($sphrase == "") return;
    // Search....
    $action->lay->set("csearch", true);

    $tsp = explode(" ", $sphrase);
    $search = new SearchDoc("", $sfamily);
    if ($tcheck == 1) {
        foreach ($tsp as $vs) {
            $search->addFilter("title ~* '%s'", $vs);
        }
    } else {
        foreach ($tsp as $vs) {
            $search->addFilter("svalues ~* '%s'", $vs);
        }
    }
    $search->setOrder("title");
    $docs = $search->search();

    $tdocs = array();
    foreach ($docs as $v) {
        $fam = getTDoc($dbaccess, getV($v, "fromid"));
        $tdocs[] = array(
            'id' => $v["id"],
            'title' => getV($v, "title"),
            'revdate' => strftime("%d/%m/%y %H:%M", getV($v, "revdate")),
            'familie' => $fam["title"],
        );
        if (count($tdocs) == $max) break;
    }
    $pd = (count($tdocs) > 1);
    $action->lay->set("msg", count($tdocs) . " document" . ($pd ? "s" : "") . " trouve" . ($pd ? "s" : "") . ", les $max premiers...");
    $action->lay->setBlockData("docs", $tdocs);

    $action->lay->set("uptime", strftime("%H:%M %d/%m/%Y", time()));
    return;
}
