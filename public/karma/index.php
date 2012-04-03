<?php
/**
 * Fichero principal de la Intranet Karma
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */
    $timeStart = microtime(true);
    clearstatcache();// Borrar cache del sistema de archivos
    /* headers para que no cachee en local el navegador */
    session_name("karmaPrivate");


    session_start();
    $timeEnd = microtime(true);
    $time = $timeEnd - $timeStart;
    //echo 'Tiempo de ejecución: '.sprintf("%.4f",$time).' segs.';

    session_cache_limiter("private");
    header("Expires: 0");
    header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false); // HTTP/1.1
    header("Pragma: no-cache");// HTTP/1.0
    /* fin de header anti cache */

    if (!isset($_SESSION['noBrowserCheck'])) {
        if (!isset($_GET['skipbrowser'])) {
            if (
                !preg_match(
                    "/(Firefox|Iceweasel|Galeon|Iceape|SeaMonkey|Gecko|Chrome|MSIE|Opera)/",
                    $_SERVER["HTTP_USER_AGENT"]
                )
            ) {
                header("Location: ./noCertifiedBrowser.php");
                exit();
            }
        } else {
            $_SESSION['noBrowserCheck'] = 1;
            //header("Location: ./");
        }
    }

    $ajaxRequest = false;
    if( (
        !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        )
        || (isset($_GET['ajaxRequest']) && $_GET['ajaxRequest']=='true')
    ) {
        $ajaxRequest = true;
    }

    ob_start();
    // Constante para comprobar que todos los ficheros son cargados desde index
    define("CHK_KARMA", 1);
    if (isset($_GET['DEBUG'])) {
        define("DEBUG", 1);
    }
    // Cargamos el "cargador" de classes
    require_once("./libs/autoload.php");
    spl_autoload_register('__autoload');

    $remoteIp = $_SERVER['REMOTE_ADDR'];

    /**
     * Gestionamos el nivel de errores que se quiere mostrar.
     * 3 muestra todos los errores (Valor por defecto en acceso localhost)
     * 0 no muestrar errores (Valor por defecto en IP's remotas)
     *
     * Para configurar un nivel de error menor en desarrollo, configurar en el .htaccess
     */
    $iErrorLevel = 0;
    if (!is_numeric($devErrorLevel = getEnv('DEV_IERROR_LEVEL'))) {
        $devErrorLevel = 3;
    }

    //Poblamos $aDevIps con todas las Ips de desarrollo (localhost siempre es desarrollo)
    $aDevIps = array();
    if ($devIps = getEnv('DEV_IPS')) {
        $aDevIps = explode('|', $devIps);
    }
    $aDevIps[] = '127.0.0.1';
    $aDevIps[] = '::1';

    if (in_array($remoteIp, $aDevIps)) {
        $iErrorLevel = $devErrorLevel;
    }

    // Instanciamos objeto error para crear el trigger de errores a nuestro objeto.
    $oError = new iError($iErrorLevel);

    $kMenu = new krm_menu(false);

    $dynLang = false;
    if ($kMenu->menuSectionExists('main', 'langDbUser')) {
        $dynLang = $kMenu->getMenuSection('main', 'langDbUser');
    }

    if (!$dynLang) {
        $dateManager = new datemanager($kMenu);
        $dateManager->set_timezone();
        $l = new k_literal($kMenu->getLang());
    }

    if (($loginClass = $kMenu->getLoginClass())!== false ) {
        $oLogin = new $loginClass($kMenu);
        $oLogin->doLogin($kMenu);
    } else {
        $oLogin = false;
    }

    if ($dynLang) {
        $uLang = $oLogin->getUserLang();
        $kMenu->setLang($uLang);

        $dateManager = new datemanager($kMenu);
        $dateManager->set_timezone();
        $l = new k_literal($uLang);
        $oLogin->l = $l;
    } else {
        $l = new k_literal($kMenu->getLang());
        $oLogin->l = $l;
    }

    $kMenu->parseMenu($oLogin);

    $fpMAIN = false;
    if (isset($kMenu->selectedConf['main']['fullPage'])) {
           $fpMAIN = $kMenu->selectedConf['main']['fullPage'];
    }

    if (($className = $kMenu->getSelClass())!="") {
        $o = new $className($kMenu->selectedConf);
        $currentClassName = $o->getCurrentClassName();

        if (($currentClassName!==false) && ($currentClassName!=$className)) {
              $o = new $currentClassName($kMenu->selectedConf);
        }

        // Ejecutar métodos en $o definidos en los externalModules de krmMenu :)
        $kMenu->register_autoClass($o);
    }

    $drawSimple = false;
    if (method_exists($o, "isSimpleDrawing")) {
        $drawSimple = $o->isSimpleDrawing();
    }

    if (!$ajaxRequest) {
        echo '<div><input type="hidden" id="KARMA_LANG" value="'
            . KarmaRegistry::getInstance()->get('lang')
            .'" /><div>';
    }

    // fullpage mode
    $fpConf = $kMenu->getFullPage($o, $fpMAIN);

    if ($drawSimple === false && !$ajaxRequest):
?>
    <div id="quickMenu">
        <?php
            $kMenu->drawQuickMenu(); // WTF?!?!?!?!
        ?>
    </div>
    <div id="newMenu">
        <div class="menuInner">
            <?php
                if ($logo = $kMenu->getLogo()) {
                    echo '<a href="' . i::base_url() . '" ><img src="'
                         . $logo . '" class="logoc" alt="'
                         . $kMenu->getTitle().'" /></a>';
                }
            ?>
        </div>
        <div class="newMenuInner">
            <?php
                $kMenu->drawNewMenu(); // WTF?!?!?!?!
            ?>
            <img src="./icons/error.png"
                 title="Hay errores en la ejecución de la página"
                 id="megaErroresPendientes"
                 alt="Errores pendientes"/>
            <img src="./icons/messagebox_warning.png"
                 title="Hay errores en la ejecución de la página"
                 id="erroresPendientes"
                 alt="Errores pendientes"/>
            <img src="./icons/bookmark.png"
                 title="Hay mensajes en la ejecución de la página"
                 id="mensajesPendientes"
                 alt="Mensajes pendientes"/>
        </div>
    </div>
    <!-- =================== Fin Menu =============== -->

    <!-- ==================== Main ==================== -->

          <div id="mainHeader">
        <span id="mainDesc">
        <?php
            echo $kMenu->getDesc();
        ?>
        </span>
        <h1 <?php echo ($fpConf && $fpConf=="auto")?"id='autoFullPage'":"";?>>
            <?php
                echo $kMenu->getSelTitle();
                if ($fpConf=="manual" || $fpConf=="auto") {
                    echo '<img id="fpbo" title="FullPage&nbsp;Switcher &lt;br /&gt; F + P &lt;em&gt; '
                         . $l->l('paracambiardemodo')
                         . '&lt;/em&gt;" src="./icons/window_fullscreen.png" alt="Full screen"/>';
                }
            ?>
        </h1>
        <div class="clearBoth"></div>
        </div>


    <div id="main">
        <?php
            if ($desc = $kMenu->getSelDesc()) echo '<h2>'.$desc.'</h2>';
            if ((isset($o)) && (is_object($o))) {
                if (method_exists($o, "drawMigas")) {
                    $o->drawMigas();
                }
            }
        ?>

        <div id="mainInner">

<?php
    endif; // fin if ($drawSimple === false);

    if ((isset($o)) && (is_object($o))) {
        if (!$ajaxRequest) {
            $o->draw();
        } else {
            $o->ajax();
        }
    }
    $buffer = ob_get_contents();
    ob_end_clean();

    if ($drawSimple === false && !$ajaxRequest) {
        require("cabecera.php");
    }

    echo $buffer;

    if ($drawSimple === false && !$ajaxRequest):
?>
        </div>
        <!--<div id="pieMainInner"></div>-->
    </div>
    <!--  Fin Main -->
    <div class="Clearer"></div>
</div>
<!-- ==================== Fin Principal================ -->
<?php
        require("pie.php");
        echo "<!-- MySQL query total time: ".sprintf("%.4f", con::$totalMYtime)." -->";
    endif; // fin if ($drawSimple === false);