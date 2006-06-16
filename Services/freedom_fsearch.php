<?php

include_once('FDL/Lib.Dir.php');

function freedom_fsearch(&$action) {

  $dbaccess = getParam("FREEDOM_DB");

  // Interface init
  $tclass = GetClassesDoc($dbaccess, $action->user->id, array(1,2), "TABLE");
  $stclass[] = array ( "value" => 0, "label" => "toutes" );
  foreach($tclass as $k => $v) {
    $stclass[] = array ( "value" => $v["initid"], "label" => $v["title"] );
  }
  $action->lay->SetBlockData("SFam", $stclass);
 
  $sphrase = GetHttpVars("sphrase", "");
  $sfamily = GetHttpVars("sfamily", 0);
  $max = GetHttpVars("max", 10);

  $action->lay->set("csearch", false);

  if ($sphrase=="") return;


  // Search....
  $action->lay->set("csearch", true);
  $docs = getChildDoc($dbaccess, 0, 0, $max, array("title ~* '".$sphrase."'"), $action->user->id, "TABLE", $sfamily, false, "title");
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

  return;
}
