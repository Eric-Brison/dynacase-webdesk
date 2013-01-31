<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WEBDESK
*/
/**
 * number of affected document
 */
include_once ("FDL/Class.Doc.php");

function countaffectdoc(Action &$action)
{
    $dbaccess = $action->GetParam("FREEDOM_DB");
    
    $doc = new_doc($dbaccess, "WS_MYAFFECTDOC");
    $count = 'x';
    $text = "L\'espace d\'échange n\'est peut-être pas installé.";
    if ($doc->isAlive()) {
        $count = $doc->count();
        if ($count == 0) $text = "Aucun document affecté.";
        else if ($count == 1) $text = "$count document vous est affecté.";
        else $text = "$count documents vous sont affectés.";
    }
    $action->lay->template = sprintf("var result = { text:'%s', ico:'', status:'0', msg:'%s' };", $count, $text);
}
?>
