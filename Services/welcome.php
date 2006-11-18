<?php
function welcome(&$action) {

  header('Content-type: text/xml; charset=utf-8');
  $action->lay->setEncoding("utf-8");
  
  $v = new Param($action->dbaccess, array("VERSION", PARAM_APP, $action->parent->id));
  $action->lay->set("version", $v->val);

}

