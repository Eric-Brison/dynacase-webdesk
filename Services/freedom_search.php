<?php

include_once('FDL/Lib.Dir.php');

function freedom_search(&$action) {

  $dbaccess = getParam("FREEDOM_DB");

  $max = GetHttpVars("max", 5);
  $s = GetHttpVars("s", "");
  $in = GetHttpVars("into", 0);
  
  if ($s=="") {
    $action->lay->set("msg", _("wd no freedom search given"));
    $action->lay->set("csearch", false);
    return;
  }

  if (is_integer($s)) $s = getIdFromName($dbaccess, $s);

  $fs = new_Doc($dbaccess, $s);
  if (!$fs->isAffected()) {
    $action->lay->set("msg", _("wd invalid freedom search given"));
    $action->lay->set("csearch", false);
    return;
  }

  $rdoc = $fs->getContent();

  if (count($rdoc)==0) {
    $action->lay->set("msg", _("wd freedom search, no  result"));
    $action->lay->set("csearch", false);
    return;
  }

  $tdocs = array();
  foreach ($rdoc as $k => $v) {
    
    $owner = getDocFromUserId($dbaccess, $v["owner"]);
    $fam = getTDoc($dbaccess, getV($v, "fromid"));
    
    $tdocs[] = array( 'id' => $v["id"],
		      'title' => getV($v, "title"),
		      'revdate' => strftime("%d/%m/%y %H:%M",getV($v, "revdate")),
		      'owner' => $owner->title,
		      'familie' => $fam["title"],
		      );
    if (count($tdocs)==$max) break;
  }
    
  $action->lay->set("csearch", true);
  $pd = (count($rdoc)>1);
  $action->lay->set("msg", "[".$fs->getTitle()."] ".count($rdoc)." document".($pd?"s":"")." trouvé".($pd?"s":"").", les $max premiers...");
  $action->lay->setBlockData("docs", $tdocs);


  return;   
}
