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
  $textlg = GetHttpVars("dlg", 100);
  $vfull = (GetHttpVars("vfull", 1)==1 ? true : false);


  $rssi =& new XML_RSS($rsslink);
  $pret = $rssi->parse();
  $rssc = $rssi->getItems();
//    print_r2($rssc);
  $ic = 0;
  if (count($rssc)>0) {
    $action->lay->set("rss", true);
    while ($ic<=$max && list($k, $v) = each($rssc)) {
      if ($v["title"]=="") continue;
      $tr[$ic] = $v;
      $tr[$ic]["id"] = $k;
      $tr[$ic]["title"] = htmlentities(utf8_decode($v["title"]));
      $sdesc = ($textlg>0 ? substr($v["description"],0,$textlg).(strlen($v["description"])>$textlg?"...":"") : $v["description"]);
      $tr[$ic]["descr"] = utf8_decode($sdesc);
      $tr[$ic]["date"] = $v["dc:date"];
      $tr[$ic]["vfull"] = $vfull;
     $ic++;
    }
  } else {
    $action->lay->set("msg", _("[TEXT:no information available, verify your server have http access to internet and/or check link please...]"). '(<a href="'.$rsslink.'">'.$rsslink.'</a>)');
    return;
  }
  $action->lay->set("msg",htmlentities(utf8_decode($rssi->channel["title"])));
  $action->lay->setBlockData("rssnews", $tr);
}
  