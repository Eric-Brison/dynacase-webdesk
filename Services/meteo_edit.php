<?php
function meteo_edit(&$action) {

  require_once($_SERVER["DOCUMENT_ROOT"]."/phpweather/phpweather.php");
  require(PHPWEATHER_BASE_DIR . "/output/pw_images.php");
  
  $def_lang = GetHttpVars("la", "fr");

  $def_icao = GetHttpVars("icao", "LFBO");
  $def_icon = GetHttpVars("iconstyle");
  
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
  $action->lay->setBlockData("CC", $tcc);

  $foo='';
  $icaos = $weather->db->get_icaos($def_cc, $foo);
  $ticaos = array();
  foreach ($icaos as $kc => $vc) {
    $ticaos[] = array("icao_code" => $kc,
		      "icao_label" => $vc,
		      "icao_selected" => ($def_icao==$kc?"selected":""));
  }
  $action->lay->setBlockData("ICAO", $ticaos);
  $iconstyle=array();
  if ($handle = opendir(DEFAULT_PUBDIR."/phpweather/icons")) {
   while (false !== ($file = readdir($handle))) {
       if ($file != "." && $file != "..") {

	 if (is_dir(DEFAULT_PUBDIR."/phpweather/icons/$file")) $iconstyle[]=$file;
       }
   }
   closedir($handle);
}
  $action->lay->set("stylefound",(count($iconstyle) > 0));
  //  $iconstyle=array("Crystal","Grzanka");
  foreach ($iconstyle as $v) {
    $tcions[]=array("icon_selected"=>($def_icon==$v?"selected":""),
		    "iconstyle"=>$v);
  }
  $action->lay->setBlockData("ICONSTYLE", $tcions);
  $action->lay->set("seliconstyle", $def_icon);
  $action->lay->set("iconid", uniqid("icon"));

  return;
}
?>
