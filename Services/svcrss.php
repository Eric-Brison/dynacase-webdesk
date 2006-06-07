<?php
include_once("XML/RSS.php");
function svcrss(&$action) {

  $action->lay->set("rss", false);

  $rsslink = GetHttpVars("rss", "");
  if ($rsslink=="") {
    $action->lay->set("msg", _("wd no rss link given"));
    return;
  }
  $max = GetHttpVars("max", 5);


  $rssi =& new XML_RSS($rsslink);
  $pret = $rssi->parse();
//   print_r2($rssi);
  $rssc = $rssi->getItems();
  $ic = 0;
  if (count($rssc)>0) {
    $action->lay->set("rss", true);
    while ($ic<=$max && list($k, $v) = each($rssc)) {
      $tr[$ic] = $v;
      $tr[$ic]["title"] = htmlentities(utf8_decode($v["title"]));
      $ic++;
    }
  } else {
    $action->lay->set("msg", _("[TEXT:no information available, verify your server have http access to internet and/or check link please...]"). '('.$rsslink.')');
    return;
  }
  $action->lay->set("msg", _("Rss feed"). " : ".htmlentities(utf8_decode($rssi->channel["title"])));
  $action->lay->setBlockData("rssnews", $tr);
}
  