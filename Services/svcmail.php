<?php

function svcmail(&$action) {

  $maxm = 10; // how many mails shown ?

  $action->lay->set("showmsg", false);
  $action->lay->set("msgtext", "");
  $action->lay->set("accset", false); 

  $ocount = GetHttpVars("oc", "N"); 
 
  $acc = GetHttpVars("account", "");
  $log = GetHttpVars("login", "");
  $pas = GetHttpVars("password", "");
  $srv = GetHttpVars("server", "");
  $pro = GetHttpVars("proto", "");
  $display = GetHttpVars("display", 0);
  
//   echo "account=[$acc] login=[$log] password=[$pas] serveur=[$srv] protocol=[$pro]<br>";


if ($acc=="" || $log=="" || $pas=="" || $srv=="" || $pro=="") {
 if ($ocount=="Y") {
   $action->lay->set("OnlyCount", true);
   $action->lay->set("new", "?");
 } else {
   $action->lay->set("OnlyCount", false);
   $action->lay->set("showmsg", true);
   $action->lay->set("msgtext", _("no account defined"));
 }
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


 if ($ocount=="Y") {

   $action->lay->set("OnlyCount", true);
   $action->lay->set("new", count($minfos["newmails"]));
   if ($minfos["error"]!="") $action->lay->set("new", "?");
   else $action->lay->set("new", count($minfos["newmails"]));
   return;
	  
 }


 $action->lay->set("OnlyCount", false);
 if ($minfos["error"]=="") {
   $action->lay->set("new", count($minfos["newmails"]));
   $action->lay->set("old", count($minfos["oldmails"]));
   if (count($minfos["newmails"])) {
     $action->lay->set("bmails", true);
     $ms = array();
     $nb = (count($minfos["newmails"])>$maxm ? count($minfos["newmails"])-$maxm : 0);
     for ($ic=count($minfos["newmails"])-1; $ic>=$nb; $ic--) {
      $sd = convertDH($minfos["newmails"][$ic]->date);
      $rfrom = clearText($minfos["newmails"][$ic]->from);
      $prfrom = preg_replace('/&lt;.*@.*&gt;/','',$rfrom);
      $ms[] = array( "subject" => clearText($minfos["newmails"][$ic]->subject),
		     "date" => $sd,
		     "fulldisplay" => ($display==0||$display=="" ? true : false),
                     "mailtolink" =>  setMailtoAnchor($rfrom,
                                                      ($prfrom==""?$rfrom:$prfrom), 
						      "Re: ".clearText($minfos["newmails"][$ic]->subject),
						      "", "", "",
						      array("class"=>"wd_amail", "target"=>"_blanck"))
		     );
     }
     if (count($minfos["newmails"])>$maxm)  $action->lay->set("moremails", true);
     $action->lay->setBlockData("mails", $ms);
   }  else $action->lay->setBlockData("mails", null);
 } else {
   $action->lay->set("showmsg", true);
   $action->lay->set("msgtext", _("error retrieving mails")."[".$minfos["error"]."]");
 }
 $action->lay->set("uptime", strftime("%X %x", time()));
 header('Content-type: text/xml; charset=utf-8');
 echo $action->lay->gen();
 exit;
}

function clearText($s) {
  return htmlentities((utf8_decode(imap_utf8($s))));
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
  return strftime("%d/%m/%y %H:%M",strtotime($id));
}
?>
