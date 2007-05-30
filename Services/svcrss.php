<?php
include_once("XML/RSS.php");
include_once("WEBDESK/Lib.Services.php");
function svcrss(&$action) {

 header('Content-type: text/xml; charset=utf-8');
 $action->lay->setEncoding("utf-8");

  $action->lay->set("rss", false);

  $ilink = urldecode(GetHttpVars("rss", ""));
  $rsslink = parseUrl($ilink);
  if ($rsslink=="") {
    $action->lay->set("msg", _("wd no rss link given")."  [$ilink]");
    return;
  }
  $max = GetHttpVars("max", 5);
  $textlg = GetHttpVars("dlg", 0);
  $vfull = (GetHttpVars("vfull", 0)==1 ? true : false);

  $rssi =& new XML_RSS($rsslink);
  $pret = $rssi->parse();
  if (isset($rssi->channel["link"])) {
    $action->lay->set("nocontent", false);
    $action->lay->set("rss", true);
    $action->lay->set("title",$rssi->channel["title"]);
    $action->lay->set("uptime", strftime("%H:%M %d/%m/%Y", time()));
    $rssc = $rssi->getItems();
    $ic = 0;
    if (count($rssc)>0) {
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
      $action->lay->setBlockData("rssnews", $tr);
    } else {
      $action->lay->set("nocontent", true);
    }
  } else {
    $action->lay->set("msg", _("[TEXT:no information available, verify your server have http access to internet and/or check link please...]"). '(<a href="'.$ilink.'">'.$ilink.'</a>)');
  }
  return;
}

?>
