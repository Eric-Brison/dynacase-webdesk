<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WEBDESK
*/

include_once ("WEBDESK/Lib.Services.php");
function svcrss(Action & $action)
{
    $action->lay->set("rss", false);
    $ilink = urldecode(GetHttpVars("rss", ""));
    $rsslink = parseUrl($ilink);
    if ($rsslink == "") {
        $action->lay->set("msg", _("wd no rss link given") . "  [$ilink]");
        return;
    }
    $max = GetHttpVars("max", 5);
    $textlg = GetHttpVars("dlg", 0);
    $vfull = (GetHttpVars("vfull", 0) == 1 ? true : false);
    
    $local = parse_url($action->getParam("CORE_URLINDEX"));
    $tu = parse_url($rsslink);
    if (isset($local["host"]) && isset($tu["host"]) && $local["host"] == $tu["host"] && $local["scheme"] == "https") {
        $r2link = str_replace("http:", "https:", $rsslink);
        $r2link = str_replace(":80", ":443", $r2link);
    } else {
        $r2link = $rsslink;
    }
    $rssContent = @file_get_contents($r2link);
    
    if ($rssContent) {
        $rssDOM = new DOMDocument();
        $rssDOM->loadXML($rssContent);
        $rssDOM->formatOutput = true;
        $xpath = new DOMXPath($rssDOM);
        $rootName = $rssDOM->documentElement->tagName;
        $rssNs = '';
        if ($rootName === "feed") {
            $xpath->registerNamespace('atom', 'http://www.w3.org/2005/Atom');
            $rssNs = "atom:";
            $link = $xpath->query($rssNs . "link");
        } else {
            $link = $xpath->query("channel/link");
        }
        
        if ($link->length > 0) {
            $action->lay->set("nocontent", false);
            $action->lay->set("rss", true);
            if ($rootName === "feed") {
                $title = $xpath->query($rssNs . "title")->item(0)->nodeValue;
            } else {
                $title = $xpath->query("channel/title")->item(0)->nodeValue;
            }
            $action->lay->set("title", htmlspecialchars($title));
            $action->lay->set("uptime", strftime("%H:%M %d/%m/%Y", time()));
            
            if ($rootName === "feed") {
                $rssc = $xpath->query($rssNs . "entry");
            } else {
                $rssc = $xpath->query("channel/item");
            }
            $ic = 0;
            if ($rssc->length > 0) {
                $tr = [];
                foreach ($rssc as $k => $item) {
                    $children = $item->childNodes;
                    $v = array();
                    $v["description"] = "";
                    /**
                     * @var DOMElement $child
                     */
                    foreach ($children as $child) {
                        if (!empty($child->tagName)) {
                            $v[$child->tagName] = $child->nodeValue;
                        }
                    }
                    if ($v["title"] == "") {
                        continue;
                    }
                    if (empty($v["description"]) && !empty($v["summary"])) {
                        $v["description"] = $v["summary"];
                    }
                    if (empty($v["pubdate"]) && !empty($v["updated"])) {
                        $v["pubdate"] = $v["updated"];
                    }
                    
                    $tr[$ic] = $v;
                    $tr[$ic]["id"] = $k;
                    $tr[$ic]["title"] = ($v["title"]);
                    $sdesc = ($textlg > 0 ? substr($v["description"], 0, $textlg) . (strlen($v["description"]) > $textlg ? "..." : "") : $v["description"]);
                    $tr[$ic]["descr"] = $sdesc;
                    
                    $tr[$ic]["date"] = isset($v["pubdate"]) ? $v["pubdate"] : '';
                    $tr[$ic]["vfull"] = $vfull;
                    
                    $ic++;
                    if ($ic > $max) {
                        break;
                    }
                }
                
                $action->lay->setBlockData("rssnews", $tr);
            } else {
                $action->lay->set("nocontent", true);
            }
        } else {
            $action->lay->set("msg", _("[TEXT:no information available, verify your server have http access to internet and/or check link please...]") . '(<a href="' . $ilink . '">' . $ilink . '</a>)');
        }
    } else {
        $action->lay->set("msg", _("[TEXT:no information available, verify your server have http access to internet and/or check link please...]") . '(<a href="' . $ilink . '">' . $ilink . '</a>)');
    }
    header('Content-type: text/xml; charset=utf-8');
}
