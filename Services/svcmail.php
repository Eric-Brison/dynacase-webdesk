<?php

function svcmail(&$action) {

  $maxm = 10; // how many mails shown ?

  $action->lay->set("showmsg", false);
  $action->lay->set("msgtext", "");
  $action->lay->set("accset", false); 
 
  $acc = GetHttpVars("account", "");
  $log = GetHttpVars("login", "");
  $pas = GetHttpVars("password", "");
  $srv = GetHttpVars("server", "");
  $pro = GetHttpVars("proto", "");
  
//   echo "account=[$acc] login=[$log] password=[$pas] serveur=[$srv] protocol=[$pro]<br>";


if ($acc=="" || $log=="" || $pas=="" || $srv=="" || $pro=="") {
   $action->lay->set("showmsg", true);
   $action->lay->set("msgtext", _("no account defined"));
   return;
 }

 $action->lay->set("accset", true); 

 switch($pro) {
 case "pop3s":
   $port = "995";
   $proto = "/pop3";
   $mode = "/ssl/novalidate-cert";
   break;
 case "imap":
   $port = "143";
   $proto = "/imap4";
   $mode = "/notls";
   break;
 case "imaps":
   $port = "993";
   $proto = "/imap4";
   $mode = "/ssl/novalidate-cert";
   break;
 default: // pop3
   $port = "110";
   $proto = "/pop3";
   $mode = "";

 }

 $mboxspec = "{".$srv.":".$port.$proto.$mode."}";
 $minfos = getMbox($mboxspec, $log,$pas);

 $action->lay->set("login", $log);
 $action->lay->set("spec", $mboxspec);
 $action->lay->set("server", $srv);
 $action->lay->set("account", $acc);
 $action->lay->set("bmails", false);
 $action->lay->set("new", "-");
 $action->lay->set("old", "-");
 $action->lay->set("moremails", false);
 $action->lay->set("nfirst", $maxm);


 if ($minfos["error"]=="") {
   $action->lay->set("new", count($minfos["newmails"]));
   $action->lay->set("old", count($minfos["oldmails"]));
   if (count($minfos["newmails"])) {
     $action->lay->set("bmails", true);
     $ms = array();
     $nb = (count($minfos["newmails"])>$maxm ? count($minfos["newmails"])-$maxm : 0);
     for ($ic=count($minfos["newmails"])-1; $ic>=$nb; $ic--) {
      $sd = convertDH($minfos["newmails"][$ic]->date);
      $ms[] = array( "from" => utf8_decode(imap_utf8($minfos["newmails"][$ic]->from)),
		      "subject" => utf8_decode(imap_utf8($minfos["newmails"][$ic]->subject)),
		      "date" => $sd);
     }
     if (count($minfos["newmails"])>$maxm)  $action->lay->set("moremails", true);
     $action->lay->setBlockData("mails", $ms);
   }  else $action->lay->setBlockData("mails", null);
 } else {
   $action->lay->set("showmsg", true);
   $action->lay->set("msgtext", _("error retrieving mails")."[".$minfos["error"]."]");
 }
 return;   
}

function getMbox($mbox, $login, $pass) {
  $mailbox = array();
  $newh = $oldh = array();
  $err = "";
  $otime = time();
  $mbx = @imap_open($mbox, $login, $pass );
  if (!$mbx) {
    $err = imap_last_error();
  } else {
    $s = imap_check($mbx);
    if (!$s) {
      $err = imap_last_error();
    } else {
      $ni = imap_num_msg($mbx);
      $ovv = imap_fetch_overview($mbx, "$ni:1");
      foreach ($ovv as $k => $v) {
        if ($v->deleted) continue;
        if (!$v->seen) {
          $newh[]= $v;
        } else {
          $oldh[]= $v;
        }
      }
    }
    imap_close($mbx);
    $ftime = time() - $otime;
  }
  $mailbox = array( "newmails" => $newh,
                    "oldmails" => $oldh,
                    "error"    => $err,
                    "elapsed"  => $ftime );
//      print_r2($mailbox);
  return $mailbox;
}

function convertDH($id) {
  //0         111111111122222222223 
  //0123456789012345678901234567890
  //Tue, 23 May 2006 15:43:03 +0200
  $hou = substr($id, 17, 2);
  $min = substr($id, 20, 2);
  $day = substr($id, 5, 2);
  $yea = substr($id, 12, 4);
  $tiz = substr($id, 22, 5);
  switch(substr($id,8,3)) {
  case "Jan": $mon = 1; break;
  case "Feb": $mon = 2; break;
  case "Mar": $mon = 3; break;
  case "Apr": $mon = 4; break;
  case "May": $mon = 5; break;
  case "Jun": $mon = 6; break;
  case "Jul": $mon = 7; break;
  case "Aug": $mon = 8; break;
  case "Sep": $mon = 9; break;
  case "Oct": $mon = 10; break;
  case "Nov": $mon = 11; break;
  case "Dec": $mon = 12; break;
  }
  return strftime("%d/%m/%y %H:%M", mktime($hou,$min,0,$mon,$day,$yea,$tiz));
}
?>