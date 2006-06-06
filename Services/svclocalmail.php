<?php
include_once("Class.MailAccount.php");
include_once("Class.Pop.php");
function svclocalmail(&$action) {

  $login = $_SERVER["PHP_AUTH_USER"];
  $password = $_SERVER["PHP_AUTH_PW"];

  if ($login=="" || $password=="") {
    $action->lay->set("OUT", "<div>Vous n'êtes pas connecté.</div>");
    return;
  }

  if ($action->user->iddomain==1) {
    $action->lay->set("OUT", "<div>Vous n'avez pas de compte de messagerie local.</div>");
    return;
  }
  $uacc = new MailAccount($action->dbaccess,$action->user->id);
  $udom = new Domain($action->dbaccess,$uacc->iddomain);
  $upop = new Pop($action->dbaccess,$uacc->pop);

  $domain = $udom->name;
  $server = $upop->popname;
  $protocol = "pop3";
  
//     echo "SVCMAIL&account=$domain&login=$login&password=$password&server=$server&proto=$protocol";

  Redirect($action, "WEBDESK", "SVCMAIL&account=$domain&login=$login&password=$password&server=$server&proto=$protocol");
}
?>