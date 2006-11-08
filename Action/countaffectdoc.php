<?php
/**
 * number of affected document
 *
 * @author Anakeen 2006
 * @version $Id: countaffectdoc.php,v 1.2 2006/11/08 06:21:30 marc Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package WEBDESK
 * @subpackage 
 */
 /**
 */
include_once("FDL/Class.Doc.php");
function countaffectdoc(&$action) {
  $dbaccess = $action->GetParam("FREEDOM_DB");

  $doc = new_doc($dbaccess,"WS_MYAFFECTDOC");
  $count='x';
  $text = "L\'espace d\'échange n\'est peut-être pas installé.";
  if ($doc->isAlive()) {
    $count=$doc->count();
    if ($count==0) $text = "Aucun document affecté.";
    else if ($count==1) $text = "$count document vous est affecté.";
    else $text = "$count documents vous sont affectés.";
  }
  $action->lay->template = 
    sprintf("var result = { text:'%s', ico:'', status:'0', msg:'%s' };",$count, $text);
  
}
?>
