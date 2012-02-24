<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WEBDESK
*/
/**
 * @begin-method-ignore
 * this part will be deleted when construct document class until end-method-ignore
 */
Class _USER_PORTAL extends Doc
{
    /*
     * @end-method-ignore
    */
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
        $numt = $this->getTValue("uport_svcnum");
        foreach ($numt as $k => $v) {
            if ($v == "" || $v < 0) {
                $change = true;
                $numt[$k] = $this->getNumSequence();
            }
        }
        if ($change) {
            $this->setValue("uport_svcnum", $numt);
            if ($mod) $this->modify();
        }
    }
    
    function getNumSequence()
    {
        $cnum = 0;
        $numt = $this->getTValue("uport_svcnum");
        foreach ($numt as $k => $v) {
            if ($v != "" && $v >= 0) $cnum = ($cnum <= $v ? $v : $cnum);
        }
        $cnum++;
        return $cnum;
    }
    /**
     * @begin-method-ignore
     * this part will be deleted when construct document class until end-method-ignore
     */
}
/*
 * @end-method-ignore
*/
?>
