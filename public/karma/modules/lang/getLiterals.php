#!/usr/bin/php
<?php
echo "\n========Get Literals========\n";


$aTodo = array(
    array(
        'command'=>'grep -iR "l->l(" ../* | grep -v \.svn',
        'regExp'=>'/l->l\(([^\)]+)\)/'
    )
    ,
    array(
        'command'=>'grep -iR "Exception" ../* | grep "throw" | grep -v \.svn ',
        'regExp'=>'/\(([^\)]+)\)/'
    )
    ,
    array(
        'command'=>'grep -iR \$message\ \= ../* |  grep -v \.svn',
        'regExp'=>'/\=\s([^\;]+)\;/'
    )

);

$aLiterals = array();

$aLiteralsVariables = array();

foreach ($aTodo as $aData) {



    $command = $aData['command'];

    $output;

    $returnVar;

    $exec = exec($command, &$output, &$returnVar);

    foreach ($output as $i=>$line) {

        if (strpos($line, '.phtml')) {
            list($tmpfile, $grep) = explode(".phtml:", $line);
        } else {
            list($tmpfile, $grep) = explode(".php:", $line);
        }


        $file = $tmpfile.".php";

        $ret = false;

        $preg = @preg_match_all($aData['regExp'], $grep, $ret);

        if ($preg>=0) {

            foreach ($ret[1] as $n => $match) {

                $match = trim($match);

                if ($match=="") continue;

                if (strpos($match, "$") !==false) {
                    $aLiteralsVariables[] = $match;
                    continue;
                }

                $l = strlen($match);

                $str = substr($match, 1, $l-2);

                $aLiterals[] = $str;

                //echo "\tin".$file;
                //echo "\n\n";
            }
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







    if (is_array($aData) && sizeof($aData) > 0 ) {
        $newDatatmp = array();
        foreach ($aData as $l=>$v) {
            if (strpos($v, "'") !==false) {
                $newDatatmp[]= "'".$l."' => \"".$v."\"";
            } else {
                $newDatatmp[]= "'".$l."' => '".$v."'";
            }
        }
        $newData.= "array(\n\t\t".implode(",\n\t\t", $newDatatmp)."\n\t\t),";
    } else {
        $newDatatmp = array();
        $aData = array('es'=>false,'en'=>false,'eu'=>false);
        foreach ($aData as $l=>$v) {
            $newDatatmp[]= "'".$l."' => false";
        }
        $newData.= "array(\n\t\t".implode(",\n\t\t", $newDatatmp)."\n\t\t),";
    }

    $newData.= "\n";


}
$newData.= ");\n";



//var_dump($multiLangLiterals);
//echo $newData;

$langFile = dirname(__FILE__).'/langFile.php';

$lfile = @fopen($langFile, "w+");
@fwrite($lfile, $newData);
@fclose($lfile);


exit();


$data = '<?php

    $lang_'.$lang.' = array(
    ';
$adata  = array();
foreach ($kv as $k=>$v) {

    $adata[]='\''.$k.'\'=>\''.$v.'\'';


}

$data.=implode(",\n\t", $adata);
    $data.='
    );';
//var_dump($multiLangLiterals);


exit();