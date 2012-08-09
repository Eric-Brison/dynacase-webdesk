<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WEBDESK
*/

include_once ('FDL/Lib.Dir.php');
function portal(&$action)
{
    
    $dbaccess = getParam("FREEDOM_DB");
    //  $debug = true;
    $debug = false;
    
    $action->parent->AddCssRef("WEBDESK:webdesk-system.css", true);
    $styleCss = getParam("WDESK_PORTAL_CSS", "lib/jquery-ui/src/themes/base/jquery.ui.all.css");
    if (file_exists($action->GetParam("CORE_PUBURL") . "/" . $styleCss)) {
        $action->parent->AddCssRef($action->GetParam("CORE_PUBURL") . "/" . $styleCss);
    } else {
        $action->parent->AddCssRef($styleCss, true);
    }
    
    $action->parent->AddJsRef($action->GetParam("CORE_JSURL") . "/DHTMLapi.js");
    $action->parent->AddJsRef($action->GetParam("CORE_JSURL") . "/AnchorPosition.js");
    $action->parent->AddJsRef($action->GetParam("CORE_JSURL") . "/geometry.js");
    $action->parent->AddJsRef($action->GetParam("CORE_JSURL") . "/resizeimg.js");
    $action->parent->AddJsRef($action->GetParam("CORE_JSURL") . "/subwindow.js");
    $action->parent->AddJsRef($action->GetParam("CORE_PUBURL") . "/FDL/Layout/common.js");
    $action->parent->AddJsRef($action->GetParam("CORE_PUBURL") . "/FDC/Layout/setparamu.js");
    
    $action->parent->addJsRef('lib/jquery/jquery.js');
    $action->parent->addJsRef('lib/jquery-ui/src/ui/jquery.ui.core.js');
    $action->parent->addJsRef('lib/jquery-ui/src/ui/jquery.ui.widget.js');
    $action->parent->addJsRef('lib/jquery-ui/src/ui/jquery.ui.position.js');
    $action->parent->addJsRef('lib/jquery-ui/src/ui/jquery.ui.button.js');
    $action->parent->addJsRef('lib/jquery-ui/src/ui/jquery.ui.menu.js');
    $action->parent->addJsRef('lib/jquery-ui/src/ui/jquery.ui.menubar.js');
    
    $action->lay->set("debug", $debug);
    if (!$debug) $action->parent->AddJsRef("WEBDESK:portal.js", true);
    else {
        $jslay = new Layout("WEBDESK/Layout/portal.js", $action);
        $action->parent->AddJsCode($jslay->gen());
    }
    
    $svclist_colcount = getParam("WDK_SVCCOLCOUNT", 3);
    //
    // Get service categories
    //
    $d = createDoc($dbaccess, "PORTAL_SERVICE", false);
    $acat = $d->GetAttribute("psvc_categorie");
    $cat = $acat->getEnum();
    $categories = array();
    foreach ($cat as $kc => $vc) {
        initTCat($categories, $kc, 0, $cat);
    }
    // Get all services
    $ts = GetChildDoc($dbaccess, 0, 0, "ALL", array() , $action->user->id, "TABLE", "PORTAL_SERVICE", false, "psvc_title");
    foreach ($ts as $k => $v) {
        $access = true;
        $appn = getV($v, "psvc_appneeded");
        if ($appn != "") {
            $req = '';
            $appl = explode("|", $appn);
            foreach ($appl as $ka => $va) {
                $access = haveAppAccess($va);
                if (!$access) break;
            }
        }
        if ($access) {
            $num_cat = getV($v, "psvc_categorie");
            addTCatEntry($categories, $v["id"], $v["title"], $num_cat);
        }
    }
    
    $menucat = '<ul>';
    foreach ($categories as $kcat => $vcat) $menucat.= '' . genCatXml($vcat, $kcat) . '';
    $menucat.= '</ul>';
    
    $action->lay->set("service_menu", $menucat);
    
    $action->lay->set("colsCount", $svclist_colcount);
    $action->lay->set("colsWidth", (100 / $svclist_colcount));
    // Initialise page structures
    $pspec = getParam("WDK_PORTALSPEC", "??|33:33:33");
    $ts = explode("%", $pspec);
    $pages = array();
    $ip = 0;
    foreach ($ts as $k => $v) {
        if ($v == "") continue;
        $sts = explode("|", $v);
        $pages[$ip]["name"] = $sts[0];
        $scol = explode(":", $sts[1]);
        $pages[$ip]["coln"] = count($scol);
        $pages[$ip]["colw"] = $scol;
        $ip++;
    }
    
    foreach ($pages as $pn => $page) {
        
        $colcount = $page["coln"];
        
        $action->lay->set("colCount", $colcount);
        
        $cwidth = floor(100 / $colcount);
        $cols = array();
        for ($icol = 0; $icol < $colcount; $icol++) {
            $cols[] = array(
                "firstCol" => ($icol == 0 ? true : false) ,
                "lastCol" => ($icol == ($colcount - 1) ? true : false) ,
                "icol" => $icol,
                "cwidth" => $page["colw"][$icol]
            );
        }
        $action->lay->setBlockData("cols", $cols);
    }
    if (count($pages) > 1) $action->lay->setBlockData("plist", $pages);
    else $action->lay->setBlockData("plist", null);
    // Initialise user services --------------------------------------------------------------
    $ppage = 1;
    
    $tsvc = array();
    $tup = GetChildDoc($dbaccess, 0, 0, "ALL", array(
        "uport_ownerid = '" . $action->user->fid . "'"
    ) , $action->user->id, "LIST", "USER_PORTAL");
    if (is_object($tup[0]) && $tup[0]->isAffected()) {
        
        $svcnum = $tup[0]->getTValue("uport_svcnum");
        $svcid = $tup[0]->getTValue("uport_idsvc");
        $svctitle = $tup[0]->getTValue("uport_svc");
        $svcparam = $tup[0]->getTValue("uport_param");
        $svcrdel = $tup[0]->getTValue("uport_refreshd");
        $svccol = $tup[0]->getTValue("uport_column");
        $svcline = $tup[0]->getTValue("uport_line");
        $svcopen = $tup[0]->getTValue("uport_open");
        $svcpage = $tup[0]->getTValue("uport_page");
        
        foreach ($svcnum as $k => $v) {
            $spage = ($svcpage[$k] == "" ? 1 : $svcpage[$k]);
            if ($ppage != $spage) continue;
            $sd = getTDoc(getParam("FREEDOM_DB") , $svcid[$k]);
            if (getV($sd, "psvc_vurl") == "") continue;
            $tsvc[] = array(
                "rg" => count($tsvc) ,
                "snum" => $v,
                "sid" => $svcid[$k],
                "stitle" => addslashes($svctitle[$k]) ,
                "vurl" => getV($sd, "psvc_vurl") ,
                "eurl" => getV($sd, "psvc_eurl") ,
                "purl" => $svcparam[$k],
                "jslink" => (getV($sd, "psvc_jsfile") != "" ? Getparam("CORE_STANDURL") . "&app=CORE&action=CORE_CSS&session=" . $action->session->id . "&layout=" . getV($sd, "psvc_jsfile") : "") ,
                "jslinkmd5" => md5(getV($sd, "psvc_jsfile")) ,
                "csslink" => (getV($sd, "psvc_cssfile") != "" ? Getparam("CORE_STANDURL") . "&app=CORE&action=CORE_CSS&session=" . $action->session->id . "&layout=" . getV($sd, "psvc_cssfile") : "") ,
                "csslinkmd5" => md5(getV($sd, "psvc_cssfile")) ,
                "rdel" => ($svcrdel[$k] == "" ? 0 : $svcrdel[$k]) ,
                "col" => $svccol[$k],
                "lin" => $svcline[$k],
                "open" => ($svcopen[$k] == 1 ? "true" : "false") ,
                "interactif" => (getV($sd, "psvc_interactif") == 1 ? "true" : "false") ,
                "mandatory" => (getV($sd, "psvc_mandatory") == 1 ? "true" : "false") ,
                "editable" => (getV($sd, "psvc_umode") == 1 ? "true" : "false") ,
            );
        }
    } else {
        $welc = getIdFromName($dbaccess, "PS_WELCOME");
        if (is_numeric($welc) && $welc > 0) {
            $svc = new_Doc($dbaccess, $welc);
            if ($svc->isAffected()) {
                $up = createDoc($dbaccess, "USER_PORTAL");
                if (!is_object($up)) {
                    $action->addWarningMsg(_("Could not create user portal."));
                    return;
                }
                $up->setValue("uport_ownerid", $action->user->fid);
                $up->setValue("uport_owner", $action->user->firstname . " " . $action->user->firstname);
                $up->setValue("uport_title", "Mon portail (" . $action->user->firstname . " " . $action->user->firstname . ")");
                $up->Add();
                $svcnum = $svcid = $svctitle = $svcparam = $svcrdel = $svccol = $svcline = array();
                $svcnumber = $up->getNumSequence();
                $svcnum[] = $svcnumber;
                $svcid[] = $svc->id;
                $svctitle[] = $svc->getValue("psvc_title");
                $svcparam[] = " ";
                $svcrdel[] = 0;
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
                
                $tsvc[] = array(
                    "rg" => count($tsvc) ,
                    "snum" => $svcnumber,
                    "sid" => $svc->id,
                    "stitle" => addslashes($svc->getValue("psvc_title")) ,
                    "vurl" => $svc->getValue("psvc_vurl") ,
                    "eurl" => $svc->getValue("psvc_eurl") ,
                    "purl" => "",
                    "jslink" => "",
                    "jslinkmd5" => "",
                    "csslink" => "",
                    "csslinkmd5" => "",
                    "rdel" => 0,
                    "col" => 0,
                    "lin" => 0,
                    "open" => "true",
                    "interactif" => "false",
                    "mandatory" => "false",
                    "editable" => "true",
                );
            }
        }
    }
    $action->lay->setBlockData("USvc", $tsvc);
}

function haveAppAccess($appname)
{
    global $action;
    $query = new QueryDb($action->dbaccess, "Application");
    // Check if application is installed and available
    $query->basic_elem->sup_where = array(
        "name='" . $appname . "'",
        "available='Y'"
    );
    $list = $query->query(0, 0, "TABLE");
    if ($query->nb <= 0) return false;
    // User have permission ?
    if ($action->user->id == 1) return true;
    
    $queryact = new QueryDb($action->dbaccess, "Action");
    $queryact->AddQuery("id_application=" . $list[0]["id"]);
    $queryact->AddQuery("root='Y'");
    $listact = $queryact->Query(0, 0, "TABLE");
    $root_acl_name = $listact[0]["acl"];
    if (!$action->HasPermission($root_acl_name, $list[0]["id"])) return false;
    
    return true;
}

function initTCat(&$fcat, $kcat, $level, $cat)
{
    $tcat = explode(".", $kcat);
    $clevel = $level;
    if (!isset($fcat[$tcat[$level]])) $fcat[$tcat[$level]] = array(
        "label" => $cat[$kcat],
        "subcat" => array()
    );
    $level++;
    if ($clevel < count($tcat) - 1) initTCat($fcat[$tcat[$clevel]]["subcat"], $kcat, $level, $cat);
    return;
}

function addTCatEntry(&$categories, $id, $label, $cat)
{
    foreach ($categories as $k => $v) {
        if ($k == $cat) {
            $categories[$k]['item'][$id] = array(
                'label' => $label,
                'ids' => $id
            );
            return;
        }
        addTCatEntry($categories[$k]['subcat'], $id, $label, $cat);
    }
    return;
}

function genCatXml($cat, $k)
{
    $menu = "";
    if (count($cat["subcat"]) > 0 && subcatNotEmpty($cat["subcat"])) {
        $menu.= '<li><a href="#">' . $cat["label"] . "...</a>\n";
        $menu.= "<ul>\n";
        foreach ($cat["subcat"] as $kcat => $vcat) $menu.= genCatXml($vcat, $kcat);
        $menu.= "</ul>\n</li>\n";
    }
    if (count($cat["item"]) > 0) {
	$menu .= "<li>\n";
	$menu .= '<a class="menu-active" href="#"';
	$menu.= '>' . $cat["label"];
	$menu.= '...';
	$menu.= "</a>";
	$menu.= '<ul>';
	foreach ($cat["item"] as $kcat => $vcat) $menu.= genCatXml($vcat, $kcat);
	$menu.= "</ul>\n";
	$menu.= "</li>\n";
    }
    if (isset($cat["ids"])) {
      $menu .= "<li>\n";
      $menu .= '<a class="menu-active" href="#"';
      $menu .= ' ids="' . $cat["ids"] . '"';
      $menu .= '>' . $cat["label"];
      $menu .= "</a>";
      $menu .= "</li>";
    }
    return $menu;
}

function subcatNotEmpty($scat) {
  foreach ($scat as $k=>$v) {
    if ((is_array($v["subcat"]) && count($v["subcat"])>0) || (is_array($v["item"]) && count($v["item"])>0)) {
      return true;
    }
  }
  return false;

}
?>
