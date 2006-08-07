<?php
/**
 * number of affected document
 *
 * @author Anakeen 2006
 * @version $Id: countaffectdoc.php,v 1.1 2006/08/07 10:10:24 eric Exp $
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
  if ($doc->isAlive()) {
    $count=$doc->count();
  }
  
  $action->lay->template=sprintf("var result='%s';",$count);
  
}
?>
