<?php
function meteo(&$action) {

  header('Content-type: text/xml; charset=utf-8');
  $action->lay->setEncoding("utf-8");
 
  require_once($_SERVER["DOCUMENT_ROOT"]."/phpweather/phpweather.php");
  require(PHPWEATHER_BASE_DIR . "/output/pw_images.php");

  $def_icao = GetHttpVars("icao", "LFBO");
  $def_lang = GetHttpVars("la", "fr");
  $full = (GetHttpVars("fulldata", "")>=1 ?  true : false );
  $wmetar = (GetHttpVars("fulldata", "")==2 ?  true : false );
  $dd = false;
  if (GetHttpVars("dd", "")=="1") $dd = true;;

  $weather = new phpweather();
  $weather->set_icao($def_icao);
  $def_cc = $weather->get_country_code();
  $cc = $weather->db->get_countries();
  $tcc = array();
  foreach ($cc as $kc => $vc) {
    $tcc[] = array("cc_code" => $kc,
		   "cc_label" => $vc,
		   "cc_selected" => ($def_cc==$kc?"selected":""));
  }
		
  $foo='';
  $icaos = $weather->db->get_icaos($def_cc, $foo);
  $ticaos = array();
  foreach ($icaos as $kc => $vc) {
    $ticaos[] = array("icao_code" => $kc,
		      "icao_label" => $vc,
		      "icao_selected" => ($def_icao==$kc?"selected":""));
  }
  $action->lay->setBlockData("ICAO", $ticaos);

  $icons = new pw_images($weather);
  $metar =  $weather->get_metar();
  $data = $weather->decode_metar();

  $img = $icons->get_sky_image();
  $action->lay->set("bgimg", "/phpweather/".$img);

  $action->lay->set("location", $weather->get_location());

  if (!isset($data["icao"])) {
    $action->lay->set("datebull", "Pas d'information.");
    $action->lay->set("data", false);
    return;
  }
  $action->lay->set("fulldata", $full);
  $action->lay->set("withmetar", $wmetar);
  $action->lay->set("data", true);
    
  $action->lay->set("datebull", strftime("%x %X", $data["time"]));
 
  //// Temperature
  $action->lay->set("temp", $data["temperature"]["temp_c"]);
  $action->lay->set("rosee", $data["temperature"]["dew_c"]);
  $action->lay->set("ressentie", (isset($data["heatindex"]["heatindex_c"])?$data["heatindex"]["heatindex_c"]:"?"));

  // Vent
  $action->lay->set("ventvms", ($data["wind"]["meters_per_second"]));
  $action->lay->set("ventv", ($data["wind"]["meters_per_second"]*3.6));
  $action->lay->set("d1", $data["wind"]["var_beg"]);
  $action->lay->set("d2", $data["wind"]["var_end"]);

  //Pression , humid
  $action->lay->set("rhumid", $data["rel_humidity"]);
  $action->lay->set("pression", $data["altimeter"]["hpa"]);

  // Nuages
  $action->lay->set("nuage", "");
  switch ($data["visibility"][0]["prefix"]) {
    case -1: $vis = "<"; break;
    case 1: $vis = ">"; break;
    default: $vis = "";
  }
  $action->lay->set("vis", $vis);
  $action->lay->set("visd", $data["visibility"][0]["km"]);

  $action->lay->set("metar", $data["metar"]);
  // Infos bulletin
}
?>
