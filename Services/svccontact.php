<?php

include_once('FDL/Lib.Dir.php');

function svccontact(&$action) {

  $limit = 10;

  $search = GetHttpVars("search", "");
  $in = GetHttpVars("into", 0);

  if ($search=="") {
    $action->lay->set("csearch", false);
    return;
  }
  $action->lay->set("csearch", true);
  $action->lay->set("sphrase", $search);

  $opf = ( $in==1 ? "^" : "" );
  $filter[] = "(title ~* '$opf".$search."')";

  $rq = getChildDoc(getParam("FREEDOM_DB"), 0, 0, $limit+1, $filter, $action->user->id, "TABLE", "USER", true, "title");
  $nb = count($rq);
  $action->lay->set("begin", ($in==1 ? false : true ));
  $action->lay->set("slimit", false);
  $action->lay->set("noresult", false);

  if ($nb==0) {
    $action->lay->set("noresult", true);
    return;
  }
    
  if ($nb>$limit) {
    $nb = $limit;
    $action->lay->set("limit", $limit);
    $action->lay->set("slimit", true);
  }

  $tco = array();
  $person = createdoc(getParam("FREEDOM_DB"), "USER", false);
  $pzone = ((!$usedefaultview)&& isset($person->faddbook_card))?$person->faddbook_card:$ddoc->defaultview;
  $action->lay->set("pzone", $pzone);
  for ($i=0; $i<$nb; $i++) {

    $ident = '<span style="font-weight:bold;">'
      .      preg_replace('/('.$search.'?)/i','<span class="svcstrsearch">\1</span>', getV($rq[$i], "title"))
      .      '</span>';
    if (getV($rq[$i], "us_mail")=="") {
      $mailtolink = $ident;
    } else {
      $mailtolink = setMailtoAnchor(getV($rq[$i], "title")." <".getV($rq[$i], "us_mail").">", 
				    $ident,
				    "", "", "", "",
				    array("class"=>"wd_amail", "target"=>"_blanck"));
    }
    $tco[] = array(
		   "pzone" => $pzone,
		   "mailtolink" =>  $mailtolink,
		   "id" => $rq[$i]["id"],
		   "sexe" => getV($rq[$i], "us_civility"),

		   "hmob" => (getV($rq[$i],"us_mobile")!="" || getV($rq[$i], "us_homemobile")),
		   "smob"  => getV($rq[$i], "us_mobile"),
		   "pmob"  => getV($rq[$i], "us_homemobile"),

		   "hpho" => (getV($rq[$i],"us_phone")!="" || getV($rq[$i], "us_homephone")),
		   "spho"  => getV($rq[$i], "us_phone"),
		   "ppho"  => getV($rq[$i], "us_homephone"),

		   "hfax" => (getV($rq[$i],"us_fax")!="" || getV($rq[$i], "us_homefax")),
		   "sfax"  => getV($rq[$i], "us_fax"),
		   "pfax"  => getV($rq[$i], "us_homefax"),

		   "coor" => (getV($rq[$i],"us_fax")!="" || getV($rq[$i], "us_homefax")) || (getV($rq[$i],"us_mobile")!="" || getV($rq[$i], "us_homemobile")) || (getV($rq[$i],"us_phone")!="" || getV($rq[$i], "us_homephone")),
		   "hsoc" => (getV($rq[$i], "us_society")==""?false:true),
		   "soc"  => getV($rq[$i], "us_society"),

		   );
  }
  $action->lay->setBlockData("contacts", $tco);
    

  return;   
}
