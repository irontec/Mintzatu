<?php
    $_args = explode("/", $_GET['args']);
    $args = array("img","karma_img.plt","img_binario", $_args[2]);
    $sizes = explode("_", $_args[1]);
    $publico = array(
        "w"=> $sizes[0],
        "h"=> $sizes[1],
        "prefix"=> $sizes[0].'_'.$sizes[1].'_'
    );
    define("CHK_KARMA",1);
    require_once("../karma/libs/autoload.php");
    tablon::generateCache(implode("/",$args), $publico);