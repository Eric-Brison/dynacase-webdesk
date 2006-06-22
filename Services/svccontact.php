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
  for ($i=0; $i<$nb; $i++) {

    

    $tco[] = array(
		   "sexe" => getV($rq[$i], "us_civility"),
		   "title" => preg_replace('/('.$search.'?)/i','<span style="background-color:'.getParam("COLOR_C2").'">\1</span>', getV($rq[$i], "title")),
		   "mailadd" => getV($rq[$i], "us_mail"),
		   "hmail" => (getV($rq[$i], "us_mail")=="" ? false : true ),


		   "hsmob" => (getV($rq[$i], "us_mobile")==""?false:true),
		   "smob"  => getV($rq[$i], "us_mobile"),

		   "hspho" => (getV($rq[$i], "us_phone")==""?false:true),
		   "spho"  => getV($rq[$i], "us_phone"),

		   "hsfax" => (getV($rq[$i], "us_fax")==""?false:true),
		   "sfax"  => getV($rq[$i], "us_fax"),

		   "hpmob" => (getV($rq[$i], "us_homemobile")==""?false:true),
		   "pmob"  => getV($rq[$i], "us_homemobile"),

		   "hppho" => (getV($rq[$i], "us_homephone")==""?false:true),
		   "ppho"  => getV($rq[$i], "us_homephone"),

		   "hpfax" => (getV($rq[$i], "us_homefax")==""?false:true),
		   "pfax"  => getV($rq[$i], "us_homefax"),

		   "hsoc" => (getV($rq[$i], "us_society")==""?false:true),
		   "soc"  => getV($rq[$i], "us_society"),

		   );
  }
  $action->lay->setBlockData("contacts", $tco);
    

  return;   
}
