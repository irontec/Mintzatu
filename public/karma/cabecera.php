<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo KarmaRegistry::getInstance()->get('lang');?>" xml:lang="es">
<head>
    <title><?php echo $kMenu->getTitle()?></title>
    <link rel="shortcut icon" href="./icons/iconKarma.ico" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="title" content="Irontec :: Internet y Sistemas sobre GNU / Linux" />
    <meta name="description" content="Irontec :: Internet y Sistemas sobre GNU / Linux" />
    <meta name="keywords" content="Irontec :: Internet y Sistemas sobre GNU / Linux" />
    <meta name="robots" content="all" />
    <meta name="language" content="<?php echo KarmaRegistry::getInstance()->get('lang');?>" />
    <meta name="author" content="Irontec"/>
    <meta name="Copyright" content="Irontec" />
    <link rel="Index" href="inicio.html" />
    <meta http-equiv="X-UA-Compatible" content="chrome=1" />
    <script type="text/javascript">//<![CDATA[
        var js_language = '<?php echo KarmaRegistry::getInstance()->get('lang');?>';
        <?php
            if ($kMenu->isTinyMECmediaDisabled()) {
                ?>
                var disableTinyMCEmedia = true;
                <?php
            } else {
                ?>
                var disableTinyMCEmedia = false;
                <?php

            }
        ?>
    //]]></script>
<?php
    $aCSS = $kMenu->getCSS();
    if ((isset($o)) && (is_object($o))) {
        $aCSS = array_merge($aCSS, $o->getCss());
    }
    $append = $kMenu->get_appendCss();
    if( is_array($append) ) $aCSS = array_merge($aCSS, $append);
    $aCSS = array_unique($aCSS);
    foreach ($aCSS as $css) :
        if (empty($css)) continue;
?>
    <link rel="stylesheet" href="./css/<?php echo $css?>" type="text/css" media="all" />
<?php
    endforeach; //foreach($aCSS as $css)
    $aJs = $kMenu->getJs();
    if ((isset($o)) && (is_object($o))) {
        $aJs = array_merge($aJs, $o->getJs());
    }
    $aJs = array_unique($aJs);
    if ($kMenu->isloadJs()) :
        $alJs=array();
        foreach ($aJs as $js) {
            if (empty($js)) continue;
            if ($js=="jquery/jquery.js") continue;
            if ( preg_match('/tiny/', $js) ) {
                echo '<script type="text/javascript" src="./scripts/'.$js.'" ></script>';
                echo "\n";
            } else {
                $alJs[] = '"./scripts/'.$js.'"';
            }

        }
        echo '<script type="text/javascript" src="./scripts/jquery/jquery.js" ></script>';
        echo "\n";
        echo '<script type="text/javascript" >';
        echo "\n";
        echo 'var aJs = [';
        echo "\n";
        echo implode(",\n", $alJs);
        echo '
            ];
            for (var a in aJs){
                        $.getScript(aJs[a]);
            }
        ';
        echo '</script>';
        echo "\n";
    else :
        foreach($aJs as $js) :
            if (empty($js)) continue;

            if ( preg_match('/ABSOLUTE/', $js) ) {
                echo '<script type="text/javascript" src="'.str_replace("ABSOLUTE","",$js).'" ></script>';
                echo "\n";
                continue;
            }
            if ( is_array($js) ) {
                /*
                 * Pensado para pasar plantillas para jQuery tmpl
                 */
                echo '<script type="'.$js['type'].'" id="'.$js['id'].'" > ';
                require_once(dirname(__FILE__) . '/' . $js['src']);
                echo '</script>';
                echo "\n";
                continue;
            }

    ?>
        <script type="text/javascript" src="./scripts/<?php echo $js?>" ></script>
    <?php
        endforeach;
    endif;



?>
        <!--[if lt IE 8]>
        <script type="text/javascript"
                src="http://ajax.googleapis.com/ajax/libs/chrome-frame/1/CFInstall.min.js"> </script>
        <![endif]-->
    </head>
    <body>

    <div id="karmaBar">
        <div id="karmaBarOpts">
            <div id="karmaBarGeneralOpts">
<?php

        if ($kMenu->is_enabled_autoClass("hdKarma")) {

?>
            <ul>
<?php
            $hd = $kMenu->get_autoClass("hdKarma");
            echo $hd->drawFireButton("li");

?>
            </ul>
<?php
        }

        /* Kaian Hack... bad placed... */
        echo $kMenu->getEmpresaListBox();

        $lis = '';
        if ((isset($o)) && (is_object($o))) {
            if (method_exists($o, "isHelper")) {
                if ($helpInfo = $o->isHelper()) {
                    $clase = "karmaBarOption";
                    if (method_exists($o, "mustHelperStartOpened")) {
                        if ($o->mustHelperStartOpened()) {
                            $clase .= " startOpened";
                        }
                    }
                    $lis .= '<li id="ayudaContextualIcon" class="'.$clase.'">'
                         . $l->l('Ayuda').'&nbsp;<img src="icons/more.png" alt="más" /></li>';
                }
            }
        }

        if ($kMenu->is_enabled_autoClass("emailKarma")) {
            $m = $kMenu->get_autoClass("emailKarma");
            if ($m->isAllowed()) {
                $lis .= '<li class="karmaBarOption" id="EmailKarmaButton" >'
                     . $l->l('Karma Email').'&nbsp;<img src="icons/more.png" alt="más" /></li>';
            }
        }
        if ($kMenu->is_enabled_autoClass("externalSystem")) {
            $m = $kMenu->get_autoClass("externalSystem");
            if ($m->isAllowed()) {
                if ($menus = $m->getHeaderMenus()) {
                    $lis .= implode("\n", $menus);
                }
            }
        }

        if ($lis !== '') {
         echo "<ul>{$lis}</ul>";
        }
        ?>
        </div>
        <span id="karmaBarLogin">
        <?php
            if ((isset($oLogin))
                && (is_object($oLogin))
                && (($logininfo = $oLogin->loginInfo())!="")
            ) {
                echo $logininfo;
            }
        ?>
        </span>
        </div>
        <?php /*?>
        <div id="karmaBarVMenu">
        <ul>
        <?php
        echo '<li class="karmaBarOption" id="quickmenu" >'
                     .'<img src="icons/more.png" alt="más" /></li>';
        ?>
        </ul>
        </div>
        <?php */ ?>

        <div id="karmaBarLogo">
            <?php
            if (!$kMenu->isKarmaBarLogoDisabled()) {
                echo  '<a href="'.i::base_url().'" ><img src="icons/karmaLogo.png" alt="Karma 2.0 Irontec Cutting Edge development framework"/></a>';
            }

            ?>
        </div>

    </div>

    <div class="Clearer"></div>
    <?php if (isset($helpInfo)) : ?>
    <div id="ayudaContextual">
        <img src="icons/window_fullscreen.png"
             id="ayudaContextualIconCerrar"
             alt="Ocultar ayuda contextual"
             class="ocultar"/>
        <img src="icons/ayudaContextual.png"
             alt="Ayuda contextual"
             class="helper"/>
        <div class="helpContainer"><?php echo $helpInfo; ?></div>
        <div id="ayudaContextualHidden"></div>
    </div>
    <?php endif; ?>

    <!-- ==================== Principal ==================== -->
    <div id="principal">
