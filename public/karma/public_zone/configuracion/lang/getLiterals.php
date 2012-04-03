#!/usr/bin/php
<?php
echo "\n========Get Literals========\n";

/*
 * CONF
 */
$aLangs = array('es'=>false,'en'=>false,'eu'=>false,'fr'=>false,'pt'=>false);

$aSearchKeys = array(
    "tit",
    "desc",
    "alias",
    "entity"
);

$aSeparators = array("=", " =");





$aSearchElements = array();

foreach ($aSearchKeys as $key) {
    foreach ($aSeparators as $sep) {
        $aSearchElements[] = $key.$sep;
    }
}


$aLiterals = array(); $aLiteralsData = array();

$aLiteralsVariables = array();

foreach ($aSearchElements as $aData) {

    $command = 'grep -iR "'.$aData.'" ../* | grep -v \.svn | grep -v getLiterals';

    $output;

    $returnVar;

    $exec = exec($command, &$output, &$returnVar);

    foreach ($output as $i=>$line) {

        $ret = false;

        $ret = explode($aData, $line, 2);

        if ($ret && is_array($ret) && sizeof($ret)>1) {
            $file = trim($ret[0]);
            $str = trim($ret[1]);

            $l = strlen($str);

            $cont = -1;

            while (substr($str, $cont, 1)!="\""){
                $cont--;
            }
            $start = 1;
            if (substr($str, 0, 1) != "\"") {
                $start = 0;
            }
            $str = substr($str, $start, $cont);
            echo $str;
            echo "\t[".str_replace(array("="," "), "", $aData)."]";
            echo "\t".$file;
            echo "\n";

            $aLiterals[] = $str;
            $aLiteralsData[$str] = "[".str_replace(array("="," "), "", $aData)."]".$file;
        }
    }
}

$aLiterals = array_unique($aLiterals);

include_once(dirname(__FILE__).'/langFile.php');

$multiLangLiteralsKeys = array_keys($multiLangLiterals);

foreach ($aLiterals as $lit) {
    if (!in_array($lit, $multiLangLiteralsKeys)) {
        $multiLangLiterals[$lit] = array();
    }
}

$newData = "";
$newData.= "<?php\n\n";
$newData.= "\$multiLangLiterals = array(\n";
foreach ($multiLangLiterals as $key => $aData) {
    if (strpos($key, "'") !==false) {
        $newData.= "\t\"".$key."\" => ";
    } else {
        $newData.= "\t'".$key."' => ";
    }
    $info = "//".(isset($aLiteralsData[$key])? $aLiteralsData[$key]:'')."";
    if (is_array($aData) && sizeof($aData) > 0 ) {
        $newDatatmp = array();
        foreach ($aData as $l=>$v) {
            if (strpos($v, "'") !==false) {
                $newDatatmp[]= "'".$l."' => \"".$v."\"";
            } else {
                $newDatatmp[]= "'".$l."' => '".$v."'";
            }
        }
        $newData.= "array(\t\t".$info."\n\t\t".implode(",\n\t\t", $newDatatmp)."\n\t\t),";
    } else {
        $newDatatmp = array();
        $aData = $aLangs;
        foreach ($aData as $l=>$v) {
            $newDatatmp[]= "'".$l."' => false";
        }
        $newData.= "array(\t\t".$info."\n\t\t".implode(",\n\t\t", $newDatatmp)."\n\t\t),";
    }
    $newData.= "\n";
}
$newData.= ");\n";

$langFile = dirname(__FILE__).'/langFile.php';

$lfile = @fopen($langFile, "w+");
@fwrite($lfile, $newData);
@fclose($lfile);

exit();