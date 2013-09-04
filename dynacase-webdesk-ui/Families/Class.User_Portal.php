<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WEBDESK
*/

namespace Dcp\WebdeskUi;

use Dcp\AttributeIdentifiers\User_Portal as myAttribute;
use Dcp\AttributeIdentifiers as Attribute;

Class User_Portal extends \Dcp\Family\Document
{
    function postCreate()
    {
        $this->uportPostModify(true);
    }
    function postModify()
    {
        $this->uportPostModify(false);
    }
    
    function uportPostModify($mod)
    {
        $this->lock();
        $change = false;
        $numt = $this->getMultipleRawValues(myAttribute::uport_svcnum);
        foreach ($numt as $k => $v) {
            if ($v == "" || $v < 0) {
                $change = true;
                $numt[$k] = $this->getNumSequence();
            }
        }
        if ($change) {
            $this->setValue(myAttribute::uport_svcnum, $numt);
            if ($mod) $this->modify();
        }
    }
    
    function getNumSequence()
    {
        $cnum = 0;
        $numt = $this->getMultipleRawValues(myAttribute::uport_svcnum);
        foreach ($numt as $k => $v) {
            if ($v != "" && $v >= 0) $cnum = ($cnum <= $v ? $v : $cnum);
        }
        $cnum++;
        return $cnum;
    }
}