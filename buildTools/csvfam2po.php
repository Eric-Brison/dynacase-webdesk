<?php


for ($i = 1; $i < count($argv); $i++) {
    prompt("Processing file " . $argv[$i]);
    if (file_exists($argv[$i])) {
        makePo($argv[$i]);
    } else {
        prompt("Can't access file " . $argv[$i]);
    }
}

function prompt($msg)
{
        error_log("csvfam2po: $msg");
}

function makePo($fi)
{
    $fdoc = fopen($fi, "r");
    if (!$fdoc) {
        prompt("fam2po: Can't access file [$fi]");
    } else {
        $nline = -1;
        $famname = "*******";
        $famtitle = "";

        while (!feof($fdoc)) {

            $nline++;

            $buffer = rtrim(fgets($fdoc, 16384));
            $data = explode(";", $buffer);

            $num = count($data);
            if ($num < 1)
                continue;

            $data[0] = trim($data[0]);
            switch ($data[0]) {

            case "BEGIN":
                $famname = $data[5];
                $famtitle = $data[2];

                echo "#, fuzzy, ($fi::$nline)\n";
                echo "msgid \"" . $famname . "#title\"\n";
                echo "msgstr \"" . $famtitle . "\"\n\n";
                break;

            case "END":
                $famname = "*******";
                $famtitle = "";
                break;

            case "ATTR":
            case "MODATTR":
            case "PARAM":
            case "OPTION":
                echo "#, fuzzy, ($fi::$nline)\n";
                echo "msgid \"" . $famname . "#" . strtolower($data[1]) . "\"\n";
                echo "msgstr \"" . $data[3] . "\"\n\n";

                // Enum ----------------------------------------------
                if ($data[6] == "enum" || $data[6] == "enumlist") {
                    $d = str_replace('\,', '\#', $data[12]);
                    $tenum = explode(",", $d);
                    foreach ($tenum as $ke => $ve) {
                        $d = str_replace('\#', ',', $ve);
                        $ee = explode("|", $d);
                        echo "#, fuzzy, ($fi::$nline)\n";
                        echo "msgid \"" . $famname . "#" . strtolower($data[1]) . "#" . (str_replace('\\', '', $ee[0])) . "\"\n";
                        echo "msgstr \"" . (str_replace('\\', '', $ee[1])) . "\"\n\n";
                    }
                }

                // Options ----------------------------------------------
                $topt = explode("|", $data[15]);
                foreach ($topt as $ko => $vo) {
                    $oo = explode("=", $vo);
                    switch (strtolower($oo[0])) {

                    case "elabel":
                    case "ititle":
                    case "submenu":
                    case "ltitle":
                    case "eltitle":
                    case "elsymbol":
                    case "showempty":
                        echo "#, fuzzy, ($fi::$nline)\n";
                        echo "msgid \"" . $famname . "#" . strtolower($data[1]) . "#" . strtolower($oo[0]) . "\"\n";
                        echo "msgstr \"" . $oo[1] . "\"\n\n";
                        break;

                    }
                }

                break;
            }

        }
    }
}

?>
