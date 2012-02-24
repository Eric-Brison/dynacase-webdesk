<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WEBDESK
*/
/**
 * preview theme
 */
include_once ("FDL/Class.Doc.php");
/**
 * Display example view for a theme
 * @param Action &$action current action
 * @global style Http var : style name
 * @global size Http var : size name
 */
function previewtheme(&$action)
{
    $style = GetHttpVars("style", $action->getParam("STYLE"));
    $size = GetHttpVars("size", $action->getParam("FONTSIZE"));
    
    $action->lay->set("style", $style);
    $action->lay->set("size", $size);
    $action->lay->set("PSTYLE", strtoupper($style));
    
    $pstyle = $action->parent->param->GetStyle($styleid);
    //print_r2($pstyle);
    $action->parent->parent->style = new Style($action->dbaccess, $style);
    $action->parent->parent->style->set($action->parent->parent);
    //$action->parent->parent->param->buffer=array();
    //print $action->parent->parent->style->name;
    $action->parent->parent->param->SetKey($action->parent->parent->id, ANONYMOUS_ID, $action->parent->parent->style->name);
    // print_r2( $action->parent->getAllParam());
    $cssfile = $action->GetLayoutFile("core.css");
    $csslay = new Layout($cssfile, $action);
    $action->parent->AddCssCode($csslay->gen());
}
?>
