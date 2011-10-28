<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FDL
 */
/**
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 */

function svccontact_edit(&$action)
{
    
    $fcard = GetHttpVars("fcard", 1);
    $maxl = GetHttpVars("maxl", 10);
    $maxc = GetHttpVars("maxc", 2);
    
    $action->lay->set("maxl", $maxl);
    $action->lay->set("maxc", $maxc);
    
    $action->lay->set("nselected", ($fcard == 0 ? "selected" : ""));
    $action->lay->set("yselected", ($fcard == 1 ? "selected" : ""));
}
