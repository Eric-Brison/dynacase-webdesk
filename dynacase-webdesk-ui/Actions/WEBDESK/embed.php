<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WEBDESK
*/

function embed(Action &$action)
{
    header('Content-type: text/xml; charset=utf-8');
    $url = GetHttpVars("url", "");
    if ($url == "") {
        $action->lay->set("nodata", true);
    } else {
        $action->lay->set("nodata", false);
        $action->lay->set("url", $url);
    }
    $action->lay->set("date", strftime("%H:%M %d/%m/%Y", time()));
}
