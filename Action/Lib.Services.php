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

function parseUrl($link)
{
    
    global $_SERVER;
    $gparams = array(
        "[user]" => urlencode($_SERVER['PHP_AUTH_USER']) ,
        "[pass]" => urlencode($_SERVER['PHP_AUTH_PW']) ,
    );
    $ms = $mr = array();
    foreach ($gparams as $k => $v) {
        $ms[] = $k;
        $mr[] = $v;
    }
    return str_ireplace($ms, $mr, $link);
}
?>