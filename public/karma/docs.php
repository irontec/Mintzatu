<?php
    if (!preg_match("/(Firefox|Iceweasel|Galeon|Iceape|SeaMonkey|Gecko|Chrome)/",$_SERVER["HTTP_USER_AGENT"])) {
        header("location: http://www.mozilla-europe.org/es/products/firefox/");
        exit();
    }
    $timeStart = microtime(true);
    clearstatcache();// Borrar cache del sistema de archivos
    session_name("karmaPrivate");
    session_start();
    $timeEnd = microtime(true);
    $time = $timeEnd - $timeStart;
    session_cache_limiter("private");
    header("Expires: 0");
    header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false); // HTTP/1.1
    header("Pragma: no-cache");// HTTP/1.0
    ob_start();
    define("CHK_KARMA",1); // Constante para comprobar que todos los ficheros son cargados desde index
    if (isset($_GET['DEBUG'])) define("DEBUG",1);
    require_once("./libs/autoload.php");
    $oError = new iError();
    $kMenu = new krm_menu();
    $kMenu->appendCss('./karmaDocs.css');
    $dateManager = new datemanager($kMenu);
    $dateManager->set_timezone();

    $l = new k_literal(KarmaRegistry::getInstance()->get('lang'));

    if (($loginClass = $kMenu->getLoginClass())!== false ) {
        $oLogin = new $loginClass($kMenu);
        $oLogin->doLogin($kMenu);
        if($oLogin->userID != $kMenu->getadminID()) $kMenu->updateMenu($oLogin->aMenu);
    }
    $fpMAIN = false;
    if (isset($kMenu->selectedConf['main']['fullPage'])){
        $fpMAIN = $kMenu->selectedConf['main']['fullPage'];
    }
    if (($className = $kMenu->getSelClass())!="") {
        $o = new $className($kMenu->selectedConf);
        $currentClassName = $o->getCurrentClassName();
        if (($currentClassName!==false) && ($currentClassName!=$className)) {
            $o = new $currentClassName($kMenu->selectedConf);
        }
    }
    echo '<div><input type="hidden" id="KARMA_LANG" value="'
        . KarmaRegistry::getInstance()->get('lang')
        . '" /><div>';
    $fpConf = $kMenu->getFullPage($o,$fpMAIN);
    $fpConf = "auto";
?>
        <div id="main">
            <h1 <?php echo ($fpConf&&$fpConf=="auto")?"id='autoFullPage'":"";?>><?php echo $kMenu->getSelTitle(); ?></h1>
            <?php if ($desc = $kMenu->getSelDesc()) echo '<h2>'.$desc.'</h2>';?>
            <div id="mainInner" class="manual">

            <h2><?php echo $l->l("KARMA :: USER MANUAL"); ?></h2>
            <?php



function gethoptions($o,$i,&$aKarmaModules){
        $html = "";
        if (!isset($o->selectedConf[$i]['ops'])) return false;
          $ops = explode(",",$o->selectedConf[$i]['ops']);
          $html.= '<ul>';
          foreach ($ops as $i){
        $aKarmaModules[$o->selectedConf[$i]['class']] = $o->selectedConf[$i]['class'];
           $html.= '<li>';
           if (isset($o->selectedConf[$i]['img'])) $html.= '<img src="./icons/'.$o->selectedConf[$i]['img'].'" alt="" />';
           $html.= $o->selectedConf[$i]['tit'];
           $info = new tablon_help($o->selectedConf[$i]['class']);
        $html.= ' [<a href="#'.$o->selectedConf[$i]['class'].'" >'.$info->title.'</a>] ';
        if (isset($o->selectedConf[$i]['desc']) && trim($o->selectedConf[$i]['desc'])!="") $html.= '<p class="desc">'.strip_tags($o->selectedConf[$i]['desc']).'</p>';
        if (isset($o->selectedConf[$i]['ops'])){
            $html.=  gethoptions($o,$i,$aKarmaModules);
        }
           $html.= '</li>';
          }
          $html.= '</ul>';
          return $html;
}

function getPICS($s,$direct=false,$class=""){


            if ($gestor = opendir($s)) {
                $html="";
                $aPics = array();
                while (false !== ($archivo = readdir($gestor))) {
                    switch($archivo){
                        case ".": case ".svn": case "..": break;
                        default:
                            switch(i::mime_content_type($s.$archivo)){
                                case "image/jpeg":
                                case "image/jpg":
                                case "image/png":
                                case "image/gif":

                                    $src = str_replace(dirname(__FILE__),"",$s).$archivo;

                                    //var_dump($src);
                                    $src = str_replace(dirname(__FILE__),"",$s.'resized/400_'.$archivo);
                                    $srcBIG = str_replace(dirname(__FILE__),"",$s.'resized/934'.$archivo);
                                        if (!file_exists($s.'resized/934'.$archivo)){
                                            $im = new i_image($s.$archivo);
                                            $no_resize_if_smaller = false;
                                            $im->setNewDim('934','0',$no_resize_if_smaller);
                                            $im->prepare();
                                            $od = $s.'resized/';
                                            if (!is_dir($od)){
                                                mkdir($od,0777);
                                                chmod($od,0777);
                                            }
                                            $im->imResize($s.'resized/934'.$archivo);
                                        }

                                    if ($class=="screenshotMINI"){
                                        if (!file_exists($s.'resized/400_'.$archivo)){
                                            $im = new i_image($s.$archivo);
                                            $no_resize_if_smaller = false;
                                            $im->setNewDim('400','0',$no_resize_if_smaller);
                                            $im->prepare();
                                            $od = $s.'resized/';
                                            if (!is_dir($od)){
                                                mkdir($od,0777);
                                                chmod($od,0777);
                                            }
                                            $im->imResize($s.'resized/400_'.$archivo);
                                        }

                                    }else{
                                        $src = $srcBIG;
                                    }



                                    $aPics[$archivo]=  '<a href=".'.$srcBIG.'" ><img class="screenshot '.$class.'" src=".'.$src.'" /></a>';
                                break;

                                default: break;
                            }
                        break;
                    }
                }
                sort($aPics);

                $html= implode(" ",$aPics);

                if ($direct===true){
                    if (sizeof($aPics)<=0) array();
                    return $aPics;
                }else{
                    if (sizeof($aPics)<=0) return "";
                    return '<div class="doccontents">'.$html.'</div><br />';
                }


                closedir($gestor);
            }else return "";
}

$html = "";
$aKarmaModules  =array();
$menu = $kMenu->getMenu();
foreach ($menu as $m=>$cont) {
    if ($m=="main") continue;
    if ($m=="&nbsp;") continue;
    $od = dirname(__FILE__).'/../tablon_helper_screenshots/'.i::clean($m).'/';
    $MENUPICS = $od;
    if (!is_dir($od)){
        mkdir($od,0777);
        chmod($od,0777);
    }
    $html.= '<br style="clear:both;" /><a id="'.$m.'"></a><h3>'.$m.'</h3>';
    if (isset($cont['desc'])) $html.= '<p>'.$cont['desc'].'</p>';

    $html.= getPICS($MENUPICS);


    $aIMGS=array();
    $inhtml = "";
    if (isset($kMenu->submenu[$m])) {
        foreach ($kMenu->submenu[$m] as $sm=>$cont) {
            $od = dirname(__FILE__).'/../tablon_helper_screenshots/'.i::clean($m).'/'.i::clean($sm).'/';
            $SUBMENUPICS = $od;
            if (!is_dir($od)){
                mkdir($od,0777);
                chmod($od,0777);
            }

            $inhtml.= '<br style="clear:both;" /><h4>';
            if (isset($cont['img'])) $inhtml.= '<img src="./icons/'.$cont['img'].'" alt="'.$sm.'" />';
            $inhtml.= $sm.'</h4>';
            if (isset($cont['desc'])) $inhtml.= '<p>'.$cont['desc'].'</p>';
            $kMenu->selected = array($m,$sm);
            $kMenu->loadSelected();
            if (($className = $kMenu->getSelClass())!="") {
                $o = new $className($kMenu->selectedConf);
                $currentClassName = $o->getCurrentClassName();
                if (($currentClassName!==false) && ($currentClassName!=$className)) {
                    $o = new $currentClassName($kMenu->selectedConf);
                }
                $aKarmaModules[$kMenu->selectedConf[0]['class']] = $kMenu->selectedConf[0]['class'];
                //$inhtml.= '<ul>';
                $ulHTML="";
                $ulHTML.= '<li>';
                $ulHTML.= $kMenu->selectedConf[0]['tit'];
                $info = new tablon_help($kMenu->selectedConf[0]['class']);
                $ulHTML.= ' [<a href="#'.$kMenu->selectedConf[0]['class'].'" >'.$info->title.'</a>] ';
                if (isset($kMenu->selectedConf[0]['desc'])&&trim($kMenu->selectedConf[0]['desc'])!="") $ulHTML.= '<p class="desc">'.strip_tags($kMenu->selectedConf[0]['desc']).'</p>';
                if (isset($kMenu->selectedConf[0]['ops'])){
                    $ulHTML.=  gethoptions($kMenu,0,$aKarmaModules);
                }
                $ulHTML.= '</li>';
                //$inhtml.= '</ul>';
                $aIMGS  =array();
                $aIMGS = getPICS($SUBMENUPICS,true,'screenshotMINI');
                $inhtml.= '<div class="ulcontainer '.((sizeof($aIMGS)<=0)? '':'colLEFT').'"><ul >'.$ulHTML.'</ul></div>';
                if (sizeof($aIMGS)>0){
                    foreach ($aIMGS as $img) {    $inhtml.= $img;    }
                }

            }

        }
    }
    $html.= '<div class="doccontents">';
    $html.= $inhtml;
    $html.= '<br style="clear:both;" /></div>';
    //$html.= '<div class="doccontents colRIGHT">';
    /*if (sizeof($aIMGS)>0){
        foreach ($aIMGS as $img) {    foreach ($img as $i) $html.= $i;    }
    }*/
    //$html.= '</div>';
}


$info = new tablon_help('intro');
echo '<h3>'.$info->title.'</h3>';
echo '<p>'.$info->desc.'</p>';


$info = new tablon_help('karma');
echo '<h3>'.$info->title.'</h3>';
echo '<p>'.$info->desc.'</p>';

echo '<div class="doccontents">';
foreach ($aKarmaModules as $foo){
    $info = new tablon_help($foo);
    echo '<a  id="'.$foo.'" ></a><h4>'.$info->title.'</h4>';
    echo '<p>'.$info->desc.'</p>';
}
echo '</div>';

$info = new tablon_help('index');
echo '<h3>'.$info->title.'</h3>';
echo '<p>'.$info->desc.'</p>';
echo '<div class="doccontents">';
echo '<ul>';
$menu = $kMenu->getMenu();
foreach ($menu as $m=>$cont) {
    if ($m=="main") continue;
    if ($m=="&nbsp;") continue;
    echo '<li><a href="#'.$m.'">'.$m.'</a></li>';
}
echo '</ul>';
echo '</div>';
echo $html;
    $buffer = ob_get_contents();
    ob_end_clean();
    require("cabecera.php");
    echo $buffer;
?>
            </div>
            <div id="pieMainInner"></div>
        </div>
        <!-- ==================== Fin Principal================ -->
        <div class="Clearer"></div>
    </div>
<?php
    require("pie.php");
        echo "<!-- MySQL query total time: ".sprintf("%.4f",$totalMYtime)." -->";


