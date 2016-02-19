<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FDL
 */

namespace Webdesk;

class Util
{
    /**
     * @param string $url
     * @param \Action $action
     *
     * @return string
     */
    public static function parseUrl($url, $action = null)
    {
        static $coreUrlKeys = null;
        static $coreUrlValues = null;
        if ($coreUrlKeys === null && $action) {
            $params = $action->parent->GetAllParam();
            $coreUrlKeys = [];
            $coreUrlValues = [];
            foreach ($params as $k => $v) {
                if (preg_match("/CORE.*URL.*/", $k)) {
                    $coreUrlKeys[] = "[$k]";
                    $coreUrlValues[] = $v;
                }
            }
        }
        return str_replace($coreUrlKeys, $coreUrlValues, $url);
    }
}
