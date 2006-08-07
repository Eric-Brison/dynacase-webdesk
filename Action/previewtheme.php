<?php
/**
 * preview theme
 *
 * @author Anakeen 2006
 * @version $Id: previewtheme.php,v 1.1 2006/08/07 16:31:49 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package WEBDESK
 * @subpackage 
 */
 /**
 */
include_once("FDL/Class.Doc.php");

/**
 * Display example view for a theme
 * @param Action &$action current action
 * @global style Http var : style name
 * @global size Http var : size name
 */
function previewtheme(&$action) {
  $style = GetHttpVars("style",$action->getParam("STYLE"));
  $size = GetHttpVars("size",$action->getParam("FONTSIZE"));
  
  $action->lay->set("style",$style);
  $action->lay->set("size",$size);
  $action->lay->set("PSTYLE",strtoupper($style));




  $pstyle=$action->parent->param->GetStyle($styleid);
  //print_r2($pstyle);
  $action->parent->parent->style=new Style($action->dbaccess,$style);
  $action->parent->parent->style->set($action->parent->parent);
  //$action->parent->parent->param->buffer=array();
  //print $action->parent->parent->style->name;
  $action->parent->parent->param->SetKey($action->parent->parent->id,ANONYMOUS_ID,$action->parent->parent->style->name);

  // print_r2( $action->parent->getAllParam());
  $cssfile=$action->GetLayoutFile("core.css");
  $csslay = new Layout($cssfile,$action);
  $action->parent->AddCssCode($csslay->gen());

}
?>
