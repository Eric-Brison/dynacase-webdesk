<?php
include_once("WEBDESK/meteoiconmap.php");
function meteo(&$action) {

  header('Content-type: text/xml; charset=utf-8');
  $action->lay->setEncoding("utf-8");
 
  require_once($_SERVER["DOCUMENT_ROOT"]."/phpweather/phpweather.php");
  require(PHPWEATHER_BASE_DIR . "/output/pw_images.php");
  require(PHPWEATHER_BASE_DIR . "/output/pw_text_fr.php");

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

  $text=new pw_text_fr($weather);
  $text->set_pref_units('only_metric');
  $thetext= $text->print_pretty();
  $action->lay->set("bulletin",$thetext);
  $action->lay->set("buid",uniqid('buid'));
  $img = $icons->get_sky_image();
  
  

  $iconstyle=getHttpVars("iconstyle");
  $action->lay->set("oimg", $img);
  if (ereg("([a-z_0-9)]*)\.png",$img,$reg)) {
    if ($reg[1][0]=='n') $bgimg=$action->getImageUrl('n_meteobg.png');
    else $bgimg=$action->getImageUrl('meteobg.png');
    $action->lay->set("bodyimg", $bgimg);
    global $iconmap;
    if (isset($iconmap[$reg[1]])) {
      $img2="icons/$iconstyle/large_icons/".$iconmap[$reg[1]].'.png';

      if (file_exists(DEFAULT_PUBDIR."/phpweather/$img2")) {
	$img=$img2;
      }
    }

  }

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
    
  $action->lay->set("datebull", strftime("%H:%M", $data["time"]));

  //   print_r2($data);

  //// Temperature
  $action->lay->set("tempv", $data["temperature"]["temp_c"]);
  $action->lay->set("temp", sprintf(_("temperature %s c (dew %s c, heat %s c)"),$data["temperature"]["temp_c"],$data["temperature"]["dew_c"],(isset($data["heatindex"]["heatindex_c"])?$data["heatindex"]["heatindex_c"]:"?")));

  // Vent
  $action->lay->set("ventvms", sprintf(_("wind, speed %s m/s direction [%s]"), $data["wind"]["meters_per_second"],$data["wind"]["deg"]));
  $action->lay->set("ventv", $data["wind"]["meters_per_second"]);

  //Pression , humid
  $action->lay->set("rhumid", sprintf(_("relative humidity %s %%"),$data["rel_humidity"]));
  $action->lay->set("rhumidv", $data["rel_humidity"]);
  
  $action->lay->set("pression", sprintf(_("Pression %s hpa"),$data["altimeter"]["hpa"]));
  $action->lay->set("pressionv", $data["altimeter"]["hpa"]);
  
// Nuages
  $action->lay->set("nuage", "");
  switch ($data["visibility"][0]["prefix"]) {
  case -1: $vis = "<"; break;
  case 1: $vis = ">"; break;
  default: $vis = "";
  }
  $action->lay->set("vist", sprintf(_("Visibility %s %s km"),$vis,$data["visibility"][0]["km"]));
  $action->lay->set("vis", $vis);
  $action->lay->set("visd", $data["visibility"][0]["km"]);
  
  $action->lay->set("metar", $data["metar"]);
  
}
?>
