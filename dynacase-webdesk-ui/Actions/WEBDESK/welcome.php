<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WEBDESK
*/

function welcome(&$action)
{
    
    header('Content-type: text/xml; charset=utf-8');
    $action->lay->setEncoding("utf-8");
    
    $v = new Param($action->dbaccess, array(
        "VERSION",
        PARAM_APP,
        $action->parent->id
    ));
    $action->lay->set("version", $v->val);
}

