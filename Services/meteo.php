<?php
function meteo(&$action) {

  require_once("phpweather/phpweather.php");
  require(PHPWEATHER_BASE_DIR . "/output/pw_images.php");

  $def_icao = GetHttpVars("icao", "LFBO");
  $def_lang = GetHttpVars("la", "fr");

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

//  require(PHPWEATHER_BASE_DIR . "/output/pw_text_".$def_lang.".php");
  //$type = 'pw_text_' . $def_lang;
  //$text = new $type($weather);
  //$icons = new pw_images($weather);

  $metar =  $weather->get_metar();
  $data = $weather->decode_metar();

  //echo "Metar: $metar <pre>\n";
  //print_r($data);
  //echo "</pre>\n";
  
  //echo '<p>This is the current weather in ' .
          //$weather->get_location() . ":</p>\n<blockquote>\n" .
          //$text->print_pretty() . "\n</blockquote>\n" .
          //"\n</blockquote>\n" .
          //"<p>The raw METAR is <code>" .
          //$weather->get_metar() . "</code></p>\n";
//
  //// Temperature
  $action->lay->set("temp", $data["temperature"]["temp_c"]);
  $action->lay->set("rosee", $data["temperature"]["dew_c"]);
  $action->lay->set("ressentie", $data["heatindex"]["heatindex_f"]);

  // Vent
  $action->lay->set("ventv", $data["wind"]["meters_per_second"]);
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

  // Infos bulletin
  $action->lay->set("datebull", strftime("%x %X", $data["time"]));
}
?>
