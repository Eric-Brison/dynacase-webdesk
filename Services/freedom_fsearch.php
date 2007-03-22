<?php

include_once('FDL/Lib.Dir.php');

function freedom_fsearch(&$action) {

  header('Content-type: text/xml; charset=utf-8');
  $action->lay->setEncoding("utf-8");
 
  $sphrase = GetHttpVars("sphrase", "");
  $sfamily = GetHttpVars("sfamily", 0);
  $tcheck = GetHttpVars("tcheck", 0);
  $max = GetHttpVars("max", 10);
 
  $dbaccess = getParam("FREEDOM_DB");

  // Interface init
  $tclass = GetClassesDoc($dbaccess, $action->user->id, array(1,2), "TABLE");
  $stclass[] = array ( "value" => 0, "sel" => "", "label" => "toutes" );
  foreach($tclass as $k => $v) {
    $stclass[] = array ( "value" => $v["initid"],
			 "sel" => ($sfamily==$v["id"] ? "selected" : ""),
                         "label" => $v["title"] );
  }
  $action->lay->SetBlockData("SFam", $stclass);

  $action->lay->set("vtcheck", $tcheck);
  $action->lay->set("bcheck", ($tcheck==1?"checked":""));
  $action->lay->set("sphrase", $sphrase);
  $action->lay->set("csearch", false);

  if ($sphrase=="") return;

  // Search....
  $action->lay->set("csearch", true);

  $attrs = "values";
  if ($tcheck==1) $attrs = "title";
  $tsp = explode(" ", $sphrase);
  $fs = array();
  foreach ($tsp as $ks => $vs) $fs[] = $attrs." ~* '".$vs."'";
  $docs = getChildDoc($dbaccess, 0, 0, $max, $fs, $action->user->id, "TABLE", $sfamily, false, "title");
  $tdocs = array();
  foreach($docs as $k => $v) {
    $fam = getTDoc($dbaccess, getV($v, "fromid"));
    $tdocs[] = array( 'id' => $v["id"],
		      'title' => getV($v, "title"),
		      'revdate' => strftime("%d/%m/%y %H:%M",getV($v, "revdate")),
		      'familie' => $fam["title"],
		      );
    if (count($tdocs)==$max) break;
  }
  $pd = (count($tdoc)>1);
  $action->lay->set("msg", count($rdoc)." document".($pd?"s":"")." trouvé".($pd?"s":"").", les $max premiers...");
  $action->lay->setBlockData("docs", $tdocs);

  $action->lay->set("uptime", strftime("%H:%M %d/%m/%Y", time()));
  return;
}
