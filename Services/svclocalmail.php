<?php
include_once("Class.MailAccount.php");
include_once("Class.Pop.php");
function svclocalmail(&$action) {

  $ocount = GetHttpVars("oc", "N"); 
  $login = $_SERVER["PHP_AUTH_USER"];
  $password = $_SERVER["PHP_AUTH_PW"];

  if ($action->user->iddomain==1) {
    $domain = "";
    $server = "";
    $protocol = "";
  } else {
    $uacc = new MailAccount($action->dbaccess,$action->user->id);
    $udom = new Domain($action->dbaccess,$uacc->iddomain);
    $upop = new Pop($action->dbaccess,$uacc->pop);
    $domain = $udom->name;
    $server = $upop->popname;
    $protocol = "imap";
  }

  
  setHttpVar("account", $domain);
  setHttpVar("login", $login);
  setHttpVar("password", $password);
  setHttpVar("server", $server);
  setHttpVar("proto", $protocol);
  Redirect($action, "WEBDESK", "SVCMAIL&oc=$ocount");
}
?>