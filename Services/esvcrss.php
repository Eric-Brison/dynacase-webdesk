<?php

/**
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 */


function esvcrss(&$action) {

  if (GetHttpVars("static")==1) 
    $action->lay->set("static", true);
  else
    $action->lay->set("static", false);
}

?>