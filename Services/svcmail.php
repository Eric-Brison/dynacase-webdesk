<?php

function svcmail(&$action) {

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
  $shmails = GetHttpVars("shmails", 0);
  $maxm  = GetHttpVars("cntm", 10);
   
  $action->lay->set("OnlyCount", ($ocount=="Y"?true:false));
  if ($ocount!="Y") {
    header('Content-type: text/xml; charset=utf-8');
    $action->lay->setEncoding("utf-8");
  }
  $action->lay->set("new", "?");
  $action->lay->set("ico", "");
  $action->lay->set("status", "0");
  $action->lay->set("msgtext", "");
  
  if ($acc=="" || $log=="" || $pas=="" || $srv=="" || $pro=="") {
    $action->lay->set("showmsg", true);
    $action->lay->set("status", "-1");
    $action->lay->set("msgtext", _("no account defined"));
    $action->lay->set("new", "-");
    $action->lay->set("old", "-");
    $action->lay->set("account", _("no account defined"));
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
   $mode = "/notls/readonly";
   break;
 case "imaps":
   $port = "993";
   $proto = "/imap4";
   $mode = "/ssl/novalidate-cert/readonly";
   break;
 default: // pop3
   $port = "110";
   $proto = "/pop3";
   $mode = "";

 }

 $mboxspec = "{".$srv.":".$port.$proto.$mode."}";
 $minfos = getMbox($mboxspec, $log, $pas, $maxm, ($shmails!=1?true:false));

 $action->lay->set("login", $log);
 $action->lay->set("spec", $mboxspec);
 $action->lay->set("server", $srv);
 $action->lay->set("account", $acc );
 $action->lay->set("bmails", false);
 $action->lay->set("new", "-");
 $action->lay->set("old", "-");
 $action->lay->set("moremails", false);
 $action->lay->set("nfirst", $maxm);


 if ($ocount=="Y") {
   if ($minfos["error"]!="") {
     $action->lay->set("new", "?");
     $action->lay->set("status", "-1");
     $action->lay->set("msgtext", $minfos["error"]);
   } else $action->lay->set("new", count($minfos["newmails"]));
   return;  
 }

 if ($minfos["error"]=="") {
   $action->lay->set("new", $minfos["newcount"]);
   $action->lay->set("old", $minfos["newcount"]+$minfos["oldcount"]);
   if (count($minfos["mails"])) {
     $action->lay->set("bmails", true);
     $ms = array();
     if ($maxm==0) $nb = 0;
     else $nb = (count($minfos["mails"])>$maxm ? count($minfos["mails"])-$maxm : 0);
     for ($ic=count($minfos["mails"])-1; $ic>=$nb; $ic--) {
       $sd = convertDH($minfos["mails"][$ic]->date);
       $rfrom = clearText($minfos["mails"][$ic]->from);
       $prfrom = preg_replace('/&lt;.*@.*&gt;/','',$rfrom);
       $ms[] = array( "subject" => clearText($minfos["mails"][$ic]->subject),
		      "date" => $sd,
		      "fulldisplay" => ($display==0||$display=="" ? true : false),
		      "mailtolink" =>  setMailtoAnchor($rfrom,
						       ($prfrom==""?$rfrom:$prfrom), 
						       "Re: ".clearText($minfos["mails"][$ic]->subject),
						       "", "", "",
						       array("class"=>"wd_amail", "target"=>"_blanck")),
		      "newmail" =>  !$minfos["mails"][$ic]->seen);
     }
     if ($maxm!=0 && count($minfos["mails"])>$maxm)  $action->lay->set("moremails", true);
     $action->lay->setBlockData("mails", $ms);
   }  else {
     $action->lay->setBlockData("mails", null);
   }
 } else {
   $action->lay->set("showmsg", true);
   $action->lay->set("msgtext", _("error retrieving mails")."[".$minfos["error"]."]");
 }
 $action->lay->set("uptime", strftime("%H:%M %d/%m/%Y", time()));
 return;
}

function clearText($s) {
  $t=imap_mime_header_decode($s);
  $ot='';
  foreach ($t as $st) {
    if ($st->charset=="utf-8") $ot.=utf8_decode($st->text);
    else $ot.=$st->text;
  }

  if ($ot[0]=='"') $ot=str_replace('"',"",$ot);

  return $ot;
}

function getMbox($mbox, $login, $pass, $count, $new=true) {
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

      $stat = imap_status($mbx,$mbox,SA_ALL);
      $newm = $stat->unseen;
      $total = $stat->messages;
      $oldm =  $total - $newm;

      $max = ($count<$total ? $count : $total); 
      $int = $total.":".($total-$max+1);
      $ovv = imap_fetch_overview($mbx, $int);
      $mail = array();
      foreach ($ovv as $k => $v) {
        if ($v->deleted) continue;
        if ($v->seen && $new) continue;
	$mail[]= $v;
      }
    }
    imap_close($mbx);
    $ftime = time() - $otime;
  }
  $mailbox = array( "mails" => $mail,
		    "newcount" => $newm,
		    "oldcount" => $oldm,
		    "total" => $total,
                    "error"    => $err,
                    "elapsed"  => $ftime );
//      print_r2($mailbox);
  return $mailbox;
}

function convertDH($id) {
  return strftime("%d/%m/%y %H:%M",strtotime($id));
}
?>
