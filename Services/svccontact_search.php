<?php
include_once("WHAT/Lib.Http.php");
include_once("WHAT/Class.Layout.php");
include_once("FDL/Lib.Dir.php");

function svccontact_search(&$action) {

  $str    = GetHttpVars("str", "");
  $fam    = GetHttpVars("fam", "");
  $dcl    = GetHttpVars("dcl", ""); // Div result close
  $hclick = GetHttpVars("hcl", ""); // On click handler
  $hover  = GetHttpVars("hov", ""); // On Mouse Over handler
  $hout   = GetHttpVars("hou", ""); // On Mouse Out handler
  $hmov   = GetHttpVars("hmo", ""); // On Mouse Move handler
  $org    = GetHttpVars("org", "C"); // [C] by Column       [L] by Line
  $maxl   = GetHttpVars("maxl", 10); 
  $maxc   = GetHttpVars("maxc", 2); 
  $lim    = ($maxl * $maxc) + 1;
  $beg    = GetHttpVars("beg", 1);
  $soc    = GetHttpVars("soc", 0);

  $colsz = (is_numeric(GetHttpVars("csz",0)) ? GetHttpVars("csz",0) : 0 );

  $dbaccess = getParam("FREEDOM_DB"); //"host=adi.tlse.i-cesam.com dbname=freedom user=anakeen";
  $ret = "";
  $addret = "";

  $filter[0] = "(title ~* '".($beg==0?"":"^").$str."')";
  if ($soc==1) $filter[0] .= " OR (us_society ~* '".($beg==0?"":"^").$str."')";
  $rfilter = ($beg==0?"":"^");
  //echo "lim=$lim fam=$fam"; print_r2($filter);
  $r = getChildDoc($dbaccess, 0, 0, $lim, $filter, $action->user->id, "TABLE", $fam );
  
  $maxf = $maxc * $maxl;
  $total = count($r);
  $moreresult = ($total>$maxf ? true : false );
  $cn = ($total>$maxf ?  $maxf  : $total );
  $ret .= "<table cellspacing=\"0\" cellpadding=\"0\">";
  if ($moreresult) $atext = sprintf(_("more than %d results..."),$cn);
  else if ($cn>0) $atext = $cn." "._("wd: result").($cn>1?"s":"");
  else $atext = _("no result for search");
  $ret .= "<tr><td  class=\"sr_info\" ".($dcl!=""?"  title=\""._("clear displayed results")."\" onclick=\"".$dcl."()\" ":"")." colspan=\"".$maxc."\">".$atext."</td></tr>";
  if ($cn>0)  {
    $ret .= "<tr><td style=\"height:5px\"; colspan=\"".$maxc."\"></td></tr>";
    $m = ceil($cn/$maxc);
    $ret .= "<tr>";
    $ccol = array();
    $ili = 0;
    $icol = 0; 
    for ($ip=0; $ip<$cn; $ip++) {
      if ($org=="C") {
	if ($ili==$m) { 
	  $icol++;
	  $ili = 0;
	}
      } else {
	if ($icol==$maxc) {
	  $icol = 0;
	  $ili++;
	}
      }
      if ($colsz>0) $st = substr($r[$ip]["title"],0,$colsz).(strlen($r[$ip]["title"])>$colsz?"...":"");
      else $st = $r[$ip]["title"];
      $tt = "<span style=\"font-weight:bold;\">".preg_replace('/'.$rfilter.'('.$str.'?)/i', '<span class="sr_warn">\1</span>', $st)."</span>";
      if ($r[$ip]["us_society"]!="") {
	$socs = $r[$ip]["us_society"]; 
	if ($soc==1) $socs = preg_replace('/'.$rfilter.'('.$str.'?)/i', '<span class="sr_warn">\1</span>', $socs)."</span>";
	$tt .= "&nbsp;(".$socs.")";
      }
      $pt = "";
      if ($r[$ip]["us_privcard"] =="P") $pt = _("wd: Private");
      if ($r[$ip]["us_privcard"] =="R") $pt = _("wd: Public readonly");
      if ($r[$ip]["us_privcard"] =="G") $pt = _("wd: Group access");
      if ($r[$ip]["us_privcard"] =="S") $pt = _("wd: Special access");
      if ($pt!="") $tt .= "&nbsp;<img title=\"$pt\" src=\"cadenas.gif\"/>";
      $ccol[$icol][$ili] .= "<div class=\"sr_result\" ";
      $ccol[$icol][$ili] .= ($hclick!="" ? " onclick=\"".$hclick."(event, this,".$r[$ip]["id"].")\"" : "");
      $ccol[$icol][$ili] .= ($hover!="" ? " onmouseover=\"".$hover."(event, this,".$r[$ip]["id"].")\"" : "");
      $ccol[$icol][$ili] .= ($hout!="" ? " onmouseout=\"".$hout."(event, this,".$r[$ip]["id"].")\"" : "");
      $ccol[$icol][$ili] .= ($hmov!="" ? " onmousemove=\"".$hmov."(event, this,".$r[$ip]["id"].")\"" : "");
      $ccol[$icol][$ili] .= ">".($tt);
	
      $ext = "";
      $ext = addCopt($ext, $r[$ip], "us_phone", "<br>");
      $ext = addCopt($ext, $r[$ip], "us_mobile", "<br>");
      $ext = addCopt($ext, $r[$ip], "us_mail", "<br>");
      $ext = addCopt($ext, $r[$ip], "us_secr", "<br>", _("wd: secretary").":");
      if ($ext!="") $ccol[$icol][$ili] .= "<div class=\"sr_rcard\">".$ext."</div>"; 
	
      $more = "";
      $society = "";
      $society = addCopt($society, $r[$ip], "us_society", "<br>");
      $society = addCopt($society, $r[$ip], "us_workaddr", "<br>");
      $society = addCopt($society, $r[$ip], array("us_workpostalcode", "us_worktown"), "<br>");
      $society = addCopt($society, $r[$ip], "us_workweb", "<br>");
      if ($society!="") $more .= "<div class=\"sr_trcard\">"._("wdd info society")."</div>" . "<div class=\"sr_rcard\">". $society ."</div>";

      $perso = "";
      $perso = addCopt($perso, $r[$ip], "us_homephone", "<br>");
      $perso = addCopt($perso, $r[$ip], "us_homemobile", "<br>");
      $perso = addCopt($perso, $r[$ip], "us_homemail", "<br>");
      $perso = addCopt($perso, $r[$ip], "us_homeaddr", "<br>");
      $perso = addCopt($perso, $r[$ip], array("us_homepostalcode", "us_hometown"), "<br>");
      if ($perso!="") $more .= "<div class=\"sr_trcard\">"._("wdd info personal")."</div>" . "<div class=\"sr_rcard\">".$perso."</div>";

      $ccol[$icol][$ili] .= "</div>\n";

      if ($more!="") $addret .= "<div id=\"m".$r[$ip]["id"]."\" style=\"position:absolute;left:0;top:0;visibility:hidden\" class=\"sr_bordermorecard\"><div class=\"sr_morecard\">".$more."</div></div>"; 

      if ($org=="C") $ili++;
      else $icol++;
    }
    for ($icol=0; $icol<$maxc; $icol++) {
      $ret .= "<td style=\"vertical-align:top\">";
      if (isset($ccol[$icol]) && is_array($ccol[$icol])) {
	foreach ($ccol[$icol] as $k => $v) $ret .= $v;
      }
      $ret .= "</td>";
    }
  }
  $ret .= "</tr>";
  if ($cn>0)  $ret .= "<tr><td  style=\"border-top:1px solid; border-bottom:0px\" title=\""._("clear displayed results")."\" class=\"sr_info\" ".($dcl!=""?" onclick=\"".$dcl."()\" ":"")." colspan=\"".$maxc."\">x</td></tr>";
  $ret .= "</table>";

  echo $ret."<div style=\"visibility:hidden; position:fixed; top:0; left:0; border:1px solid red\">".$addret."</div>";
  exit;
  
}

function addCopt($str, $doc, $field, $epref="", $pref="", $suff="") {
  $instr = "";
  if (is_array($field)) {
    foreach ($field as $k => $v) {
      if (isset($doc[$v]) && $doc[$v]!="") $instr .= ($instr=="" ? "" : " ").$doc[$v];
    }
  } else {
    $instr = (isset($doc[$field]) && $doc[$field]!="" ? $doc[$field] : "" );
  }
  if ($instr!="")  return $str .= ($str==""?"":$epref).$pref.str_replace(" ", "&nbsp;", $instr).$suff;
    return $str;
}

?>
