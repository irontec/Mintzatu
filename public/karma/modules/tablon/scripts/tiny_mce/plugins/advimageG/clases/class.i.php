<?php

if ( (!defined("SERVER_NAME")) && (isset($_SERVER) && array_key_exists("HTTP_HOST", $_SERVER)) ) {
    if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
        define("SERVER_NAME", $_SERVER['HTTP_X_FORWARDED_HOST']);
    } else {
        define("SERVER_NAME", $_SERVER['HTTP_HOST']);
        //.(($_SERVER['SERVER_PORT']!="80")? ":".$_SERVER['SERVER_PORT']:""));
    }

}

class i
{
    static function random($i, $tipo=3)
    {
        $cadena="";
        for ($j=0;$j<$i;$j++) {
            switch(rand(1, $tipo)) {
                case 1: //0-9
                    $cadena.=chr(rand(48, 57));
                    break;
                case 2: //A-Z
                    $cadena.=chr(rand(65, 90));
                    break;
                case 3: //a-z
                    $cadena.=chr(rand(97, 122));
                    break;
            }
        }
        return $cadena;
    }

    static function htmlfriendly($var)
    {
        $chars = array(
            128 => '&#8364;',
            130 => '&#8218;',
            131 => '&#402;',
            132 => '&#8222;',
            133 => '&#8230;',
            134 => '&#8224;',
            135 => '&#8225;',
            136 => '&#710;',
            137 => '&#8240;',
            138 => '&#352;',
            139 => '&#8249;',
            140 => '&#338;',
            142 => '&#381;',
            145 => '&#8216;',
            146 => '&#8217;',
            147 => '&#8220;',
            148 => '&#8221;',
            149 => '&#8226;',
            150 => '&#8211;',
            151 => '&#8212;',
            152 => '&#732;',
            153 => '&#8482;',
            154 => '&#353;',
            155 => '&#8250;',
            156 => '&#339;',
            158 => '&#382;',
            159 => '&#376;'
        );
        $var = str_replace(
            array_map('chr', array_keys($chars)),
            $chars,
            htmlentities(stripslashes($var), ENT_QUOTES, 'utf-8')
        );
        return $var;
    }

    /**
     *
     * Comprueba si el email es correcto
     *
     * @param string $email
     * @param int $extricto Si está a 1 entonces se comprueba si el host existe.
     * @return mixed Si extricto es 1 entonces devuelve true o false dependiendo si el mail es válido o no.
     *               Si extricto es 0 entonces devuelve el mail filtrado o false si el mail no es válido???
     *
     * FIXME: Lo que devuelve esta función es completamente inconsistente...
     */
    static function checkMail($email, $extricto = 1)
    {
        $filteredMail = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (false !== $filteredMail) {
            if ($extricto == 0) {
                return $filteredMail;
            }

            list($name, $host) = explode('@', $filteredMail);

            if (false !== getmxrr($host, $mxhosts) || gethostbyname($host) !== $host) {
                return true;
            }
        }
        return false;
    }

    /**
     * Limpiar una cadena para que sea válida en una URL. Devuelve la cadena limpia.
     *
     * @param string $str Cadena a limpiar
     * @param bool $espacio Si está a true, no transforma los espacios en _
     * @param bool $singleUnderScore Si está a true, sustituye _+ por _,
     *      es decir ____________ por _, y ^_ y _$ por vacio
     *      (ni empezar ni acabar con guion bajo)
     * @return string
     */
    static function clean($str, $espacios = false,$singleUnderScore = false)
    {
        /* Set character encoding detection order */
        $ary[] = "UTF-8";
        $ary[] = "ISO-8859-1";
        mb_detect_order($ary);

        $str = html_entity_decode($str);

        if ($espacios) $pregStr = "/([^a-z0-9_ ])/";
        else $pregStr = "/([^a-z0-9_])/";

        $car = array(
            "á"=>"a","é"=>"e","í"=>"i","ó"=>"o","ú"=>"u",
            "Á"=>"A","É"=>"E","Í"=>"I","Ó"=>"O","Ú"=>"U",
            "ñ"=>"n","Ñ"=>"N","ü"=>"u","Ü"=>"U","\""=>"","'"=>"","-"=>"_");
        $aKeys = array_keys($car);
        // Get the strict encodign of the passed string and array keys
        $encodingStr = mb_detect_encoding($str, mb_detect_order(), true);
        $encondingKeys = mb_detect_encoding($aKeys[0], mb_detect_order(), true);
        /*
         * Encoding bug fix:
         *  If encodings are different (probably because it comes from image or file upload),
         *  change the string encoding.
         */
        if ($encodingStr != $encondingKeys && $encodingStr == "ISO-8859-1" && $encondingKeys == "UTF-8") {
            $str = utf8_encode($str);
        }
        $intermedio = mb_strtolower(str_replace($aKeys, $car, $str));
        $retorno = preg_replace($pregStr, "_", $intermedio);
        if ($singleUnderScore) {
                $retorno = preg_replace("/_+/", "_", $retorno);
                if (substr($retorno, 0, 1) == "_") $retorno = substr($retorno, 1);
                if (substr($retorno, -1) == "_") $retorno = substr($retorno, 0, -1);
        }
        return $retorno;


    }

    static function tamFich($filesize)
    {
        $type = Array ('b', 'kb', 'mb', 'gb');
        for ($i = 0; $filesize > 1024; $i++)
          $filesize /= 1024;
        return round($filesize, 2) . " " . $type[$i];
    }

    static function cifrar($p, $salt="")
    {
        $salt = ($salt=="")? i::random(8):$salt;
        return (crypt($p, '$1$' . $salt . '$'));
    }

    static function checkETAG($etag)
    {
        $headers = getallheaders();
        if ( (isset($headers['If-None-Match'])) && (preg_match("/{$etag}/", $headers['If-None-Match'])) ) {
            header('HTTP/1.1 304 Not Modified');
            exit();
        }
    }

    /**
     * Devuelve la ruta real hasta el directorio base de karma con / final (directorio base de la zona pública)
     * Obviamente si se modifica la ruta a las clases, habrá que modificar esta función
     *
     * @return Ruta real hasta el directorio base.
     */
    static function base_path()
    {
        $d = realpath(dirname(__FILE__)."/../../");
        return $d . ((substr($d, -1) == "/")? "" : "/");
    }

    /**
     * Devuelve la ruta real hasta el directorio karma con / final (directorio base de la zona privada)
     * Obviamente si se modifica la ruta a las clases, habrá que modificar esta función
     *
     * @return Ruta real hasta el directorio karma.
     */
    static function karma_path()
    {
        $d = realpath(dirname(__FILE__)."/../");
        return $d . ((substr($d, -1) == "/")? "" : "/");
    }

    /**
     * Devuelve el base_url de la web con la barra (/) al final (p.e.: '/', '/web/', '/public/')
     * @return string
     */
    static function base_url()
    {
        if (defined("BASE_URL_PREVIEW")) return BASE_URL_PREVIEW;
        $d = dirname($_SERVER['PHP_SELF']);
        return $d .((substr($d, -1) == "/")? "" : "/");
    }

    static function rewrite_current_full($g=true, $e=false)
    {
        return "http://" . SERVER_NAME . self::rewrite_current($g, $e);
    }

    static function rewrite_current_full_logout()
    {
        return "http://logout:logout@" . SERVER_NAME . self::rewrite_current(false, true);
    }

    /**
     * Reescribe la "dirección" actual con los argumentos indicados
     * (según la función rewrite) y con los parámetros GET concatenados
     *
     * @param string $gets: Si existe, indica la cadena de parámetro GET a concatenar.
     * @param string $excludes: Si existe, indica qué parámetros GET existentes ya no
     *                  serán tomados en cuenta (unseteo de parámetros get antiguos...)
     * @return string
     */
    static function rewrite_current($gets = true, $excludes = false, $ext = 'html')
    {
        $auxGet = $_GET;

        /*
         * Escribimos la parte "bonita" de la URL http://base/modulo/arg1/arg2/arg...
         */
        $url = i::base_url();
        if (isset($auxGet['modulo'])) {
            $url .= $auxGet['modulo'];
        }
        $newGets = array();
        for ($i=0; $i < 9; $i++) {
             // No tenemos en cuenta argumentos que estén vacios o correspondan a la extensión
            if (!isset($auxGet['arg' . $i]) || trim($auxGet['arg' . $i]) == ""
               || $auxGet['arg' . $i] == "html" || $auxGet['arg' . $i] == $ext) {
                unset($auxGet['arg' . $i]);
                continue;
            }
            $newGets[] = $auxGet['arg' . $i];
            unset($auxGet['arg' . $i]);
        }
        if (sizeof($newGets) > 0) {
            $url .= '/' . implode('/', $newGets);
        }
        if ($ext != '' && isset($auxGet['modulo'])) {
            $url .= '.' . $ext;
        }
        unset($auxGet['modulo']);
        /*
         * Fin de la parte "bonita"
         */

        if ($gets) {
            $newGets = array();
            if (!$excludes||!is_string($excludes)||$excludes!="*") {
                foreach ($auxGet as $getKey => $getValue) {
                    //Si está en el array de exclusión no lo reescribimos
                    if (is_array($excludes) && in_array($getKey, $excludes)) {
                        continue;
                    }
                    if (is_array($getValue)) {
                        foreach($getValue as $key => $value) {
                            $newGets[$getKey . '[' . $key . ']'] = $getKey . '[' . $key . ']=' .urlencode($value);
                        }
                    } else {
                        $newGets[$getKey] = $getKey . "=" . urlencode($getValue);
                    }
                }
            }
            /*
             * Sobreescribimos cualquier parámetro con el valor recibido en $gets
             */
            if (is_string($gets)) {
                $aGets = explode("&", $gets);
                foreach ($aGets as $value) {
                    $aParam = explode("=", $value);
                    $newGets[$aParam[0]] = $value;
                }
            }
            /*
             * Si hay parámetros los escribimos al final
             */
            if (sizeof($newGets) > 0) {
                $url .= "?";
                $url .= implode('&amp;', $newGets);
            }
        }
        return $url;
    }

    /**
     * Método para el protocolo https.
     * Devuelve una "pretty url" direccionando al módulo con los parametros especificados.
     * Devuelve una url de la forma https://www.ejemplo.org/base/modulo/arg1/arg2/argx.ext
     *
     * @param string $modulo    Nombre del módulo ("index" por defecto)
     * @param string $arg1
     * @param string $arg2
     * @param string $arg3
     * @param string $arg4
     * @param string $arg5
     * @param string $arg6
     * @param string $arg7
     * @param string $arg8
     * @param string $ext        Extensión que se quiere aplicar al final de la url ("html" por defecto)
     * @return string
     */
    static function rewrites($modulo = "index", $arg1 = "", $arg2 = "", $arg3 = "", $arg4 = "", $arg5 = "", $arg6 = "", $arg7 = "", $arg8 = "", $ext="html")
    {
        return "https://"
        .SERVER_NAME.i::base_url()
        .i::_rewrite(
            $modulo,
            $arg1,
            $arg2,
            $arg3,
            $arg4,
            $arg5,
            $arg6,
            $arg7,
            $arg8,
            $ext
        );
    }

    /**
     * Método para el protocolo http, devuelve una "pretty url" direccionando
     * al módulo con los parametros especificados.
     * Devuelve una url de la forma http://www.ejemplo.org/base/modulo/arg1/arg2/argx.ext
     *
     * @param string $modulo    Nombre del módulo ("index" por defecto)
     * @param string $arg1
     * @param string $arg2
     * @param string $arg3
     * @param string $arg4
     * @param string $arg5
     * @param string $arg6
     * @param string $arg7
     * @param string $arg8
     * @param string $ext        Extensión que se quiere aplicar al final de la url ("html" por defecto)
     * @return string
     */
    static function rewrite($modulo = "index", $arg1 = "", $arg2 = "", $arg3 = "", $arg4 = "", $arg5 = "", $arg6 = "", $arg7 = "", $arg8 = "", $ext="html")
    {
        return "http://"
        . SERVER_NAME
        . i::base_url()
        . i::_rewrite(
            $modulo,
            $arg1,
            $arg2,
            $arg3,
            $arg4,
            $arg5,
            $arg6,
            $arg7,
            $arg8,
            $ext
        );
    }

    /**
    * Método idéntico a rewrite, pero que devuelve la URL en forma directorio, sin .html
    * @return string
    */
    static function rewrite_dir($modulo = "index", $arg1 = "", $arg2 = "", $arg3 = "", $arg4 = "", $arg5 = "", $arg6 = "", $arg7 = "", $arg8 = "")
    {
        return i::rewrite(
            $modulo,
            $arg1,
            $arg2,
            $arg3,
            $arg4,
            $arg5,
            $arg6,
            $arg7,
            $arg8,
            ""
        );
    }

    static function rewrite_base()
    {
        return "http://".SERVER_NAME.i::base_url();
    }

    /*
     * Función que devuelve una url, dependiendo del parámetro.
     * @param $public
     *      FALSE: Se llama a rewrite_base.
     *      TRUE: Se elimina toda la parte de la url de 'karma' (incluido) en
     *          adelante, para obetener la url de la parte pública.
     */
    static function rewrite_base_cond($public=false)
    {
        if ($public===false) return i::base_url();
        $base = i::base_url();
        if (preg_match("/karma/i", $base)) {
            $base = preg_replace("/karma.*$/i", "", $base);
        }
        return "http://".SERVER_NAME.$base;
    }

    static function _rewrite($modulo = "index", $arg1 = "", $arg2 = "", $arg3 = "", $arg4 = "", $arg5 = "", $arg6 = "", $arg7 = "", $arg8 = "", $ext="html")
    {
        $url = "";
        $url .= $modulo;
        if ($arg1!=="") $url .= "/".$arg1;
        if ($arg2!=="") $url .= "/".$arg2;
        if ($arg3!=="") $url .= "/".$arg3;
        if ($arg4!=="") $url .= "/".$arg4;
        if ($arg5!=="") $url .= "/".$arg5;
        if ($arg6!=="") $url .= "/".$arg6;
        if ($arg7!=="") $url .= "/".$arg7;
        if ($arg8!=="") $url .= "/".$arg8;

        if ($ext != "") {
            $url .= ".".$ext;
        } else {
            $url .= "/";
        }

        return $url;
    }

    static function set(&$var, $ret = "")
    {
        if (isset($var)) return $var;
        return $ret;
    }

    static function _404($message = null)
    {
        header(' ', true, 404);
        $f = dirname(__FILE__)."/../../errores/404.php";

        if (file_exists($f)) {
            require(dirname(__FILE__)."/../../errores/404.php");
        } else {
            if (!is_null($message)) {
                echo '<h1>' . $message . '</h1>';
            } else {
                echo '<h1>Fichero no encontrado (404)</h1>';
            }
            echo "<p><a href=\"".i::base_url()."\">Volver</a></p>";

        }
        exit();
    }

    static function mime_content_type($file)
    {
        if (!function_exists('mime_content_type')) {
            if (function_exists('exec') && file_exists('/usr/bin/file')) {
                return exec("/usr/bin/file -bi " . escapeshellarg($file));
            }
        } else {
            if (file_exists($file)) {
              return mime_content_type($file);
            }
        }

        return false;
    }

    static function getIp()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        if ($_SERVER['REMOTE_ADDR']) {
            return $_SERVER['REMOTE_ADDR'];
        }

    }

    static function getLongIp()
    {
        $ip = (isset($_SERVER['HTTP_CLIENT_IP']))? $_SERVER['HTTP_CLIENT_IP'] : $_SERVER['REMOTE_ADDR'];
        $long = sprintf("%u", ip2long($ip));
        return $long;
    }

    static function add_spans($data, $words, $clase="fuenteroja")
    {
            if (is_array($data)) {
                $data=join('', $data);
            }
            $data= ">" . $data . "<";
            foreach ($words as $word) {
                if (trim($word)=="") {
                   continue;
                }
                $data = preg_replace(
                    "/((?=>)[^<]*?)(" . preg_quote($word) . ")([^<>]*)/i",
                    "$1<span class=\"" . $clase . "\">" . strtoupper($word) . "</span>$3",
                    $data
                );
            }
            $data = substr($data, 1, strlen($data) - 2);
            return $data;
    }

    static function getServerName()
    {
        return SERVER_NAME;
    }

    static function getMimeTypeIcon($mimetype, $nombre, $pathDestinoCache="", $srcPath = "")
    {
        $icono = "empty.png";
        if ($srcPath == "") $srcPath = dirname(__FILE__)."/../mimetypes_icons/";
        if ($pathDestinoCache == "") {
            $pathDestinoCache = dirname(__FILE__)."/../../cache/mimeCache/";
            if (!file_exists($pathDestinoCache)) {
                        umask(0000);
                        if (!mkdir($pathDestinoCache, 0755)) {
                                iError::warn(
                                    "Imposible crear directorio cache para mimetypes ["
                                    . basename($pathDestinoCache) . "]."
                                );
                                return false;
                        }
            }
        }
        if (!is_dir($pathDestinoCache)) {
            iError::warn("Existe el elemento [".basename($pathDestinoCache)."] pero es un fichero.");
            return false;
        }
        if (!is_writeable($pathDestinoCache)) {
            iError::warn("El directorio [".basename($srcPath)."] no es escribible.");
            return false;
        }

        if (!$mimetype || $mimetype == "application/msword" || $mimetype == "application/vnd.ms-office") {
            $nombre = strtolower($nombre);
            //Obtenemos la extensión
            if (preg_match("/.*\.([^\.]+)/", $nombre, $coincidencias)) {
                switch($coincidencias[1]) {
                    case "xls":
                        $icono = "application-vnd.ms-excel";
                        break;
                    case "doc":
                        $icono = "application-msword";
                        break;
                    case "ppt":
                        $icono = "application-vnd.ms-powerpoint";
                        break;
                    case "mdb":
                        $icono = "application-vnd.ms-access";
                        break;
                    default:
                        $icono = "application-msword";
                        break;
                }
            }

        } else {
            $icono = str_replace("/", "-", trim($mimetype));
        }

        $icono = $icono.".png";
        if (!file_exists($srcPath . $icono)) {
            $arrIcono = explode("-", $icono);
            $icono = $arrIcono[0] . ".png";
            if (!file_exists($srcPath . $icono)) {
                $icono = "empty.png";
            }
        }

        if (!file_exists($pathDestinoCache . $icono)) {
            $img = new i_image($srcPath . $icono);
            $img->setNewDim(25, 25, true);
            $img->prepare();
            $img->imResize($pathDestinoCache.$icono);
        }


        return $icono;
    }



    static function detectUTF8($string)
    {
            return preg_match(
                '%(?:
                [\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
                |\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
                |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
                |\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
                |\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
                |[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
                |\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
                )+%xs',
                $string
            );
    }

    /**
     * Autogenerador de contraseñas
     * @author: Got from Totally PHP (http://www.totallyphp.co.uk/code/create_a_random_password.htm)
     */
    function gen_pass()
    {
        $chars = "abcdefghijkmnopqrstuvwxyz023456789";
        srand((double)microtime()*1000000);
        $i = 0;
        $pass = '' ;
        while ($i <= 7) {
            $num = rand() % 33;
            $tmp = substr($chars, $num, 1);
            $pass = $pass . $tmp;
            $i++;
        }
        return $pass;
    }

    /**
     * Devuelve true si apc está instalado y podemos obtener datos de la subida de un fichero
     */
    public static function CanGetUploadStatus()
    {
        if (!extension_loaded('apc'))
            return false;
        if (!function_exists('apc_fetch'))
            return false;
        return ini_get('apc.enabled') && ini_get('apc.rfc1867');
    }

    public function getUploadStatus($id)
    {
        // sanitize the ID value
        $id = preg_replace('/[^a-z0-9]/i', '', $id);
        if (strlen($id) == 0)
            return;

        // ensure the uploaded status data exists in the session
        if (!array_key_exists($id, $_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY][$id] = array(
                'id'       => $id,
                'finished' => false,
                'percent'  => 0,
                'total'    => 0,
            );
        }

        // retrieve the data from the session so it can be updated and returned
        $ret = $_SESSION[self::SESSION_KEY][$id];

        // if we can't retrieve the status or the upload has finished just return
        if (!self::CanGetUploadStatus() || $ret['finished'])
            return $ret;

        // retrieve the upload data from APC
        $status = apc_fetch('upload_' . $id);

        // false is returned if the data isn't found
        if ($status) {
            $ret['finished'] = (bool) $status['done'];
            $ret['total']    = $status['total'];
            $ret['complete'] = $status['current'];

            // calculate the completed percentage
            if ($ret['total'] > 0)
                $ret['percent'] = $ret['complete'] / $ret['total'] * 100;

            // write the changed data back to the session
            $_SESSION[self::SESSION_KEY][$id] = $ret;
        }

        return $ret;
    }





    /**
     *
     *  Tipo:      ???      NIF      CIF      NIE
     *  Correcto:         1       2       3
     *  Incorrecto:     0       -1       -2       -3
     */
    public static function valida_nif_cif_nie($cif)
    {
        //Copyright ©2005-2008 David Vidal Serra. Bajo licencia GNU GPL.
        //Este software viene SIN NINGUN TIPO DE GARANTIA; para saber mas detalles
        //puede consultar la licencia en http://www.gnu.org/licenses/gpl.txt(1)
        //Esto es software libre, y puede ser usado y redistribuirdo de acuerdo
        //con la condicion de que el autor jamas sera responsable de su uso.
        //Returns: 1 = NIF ok, 2 = CIF ok, 3 = NIE ok, -1 = NIF bad, -2 = CIF bad, -3 = NIE bad, 0 = ??? bad
        $cif = strtoupper($cif);
        for ($i = 0; $i < 9; $i ++) {
            $num[$i] = substr($cif, $i, 1);
        }

        //si no tiene un formato valido devuelve error
        if (!preg_match('/((^[A-Z]{1}[0-9]{7}[A-Z0-9]{1}$|^[T]{1}[A-Z0-9]{8}$)|^[0-9]{8}[A-Z]{1}$)/', $cif)) {
            return 0;
        }

        //comprobacion de NIFs estandar
        if (preg_match('/(^[0-9]{8}[A-Z]{1}$)/', $cif)) {
            if ($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr($cif, 0, 8) % 23, 1)) {
                return 1;
            } else {
                return -1;
            }
        }

        //algoritmo para comprobacion de codigos tipo CIF
        $suma = $num[2] + $num[4] + $num[6];
        for ($i = 1; $i < 8; $i += 2) {
            $suma += substr((2 * $num[$i]), 0, 1) + substr((2 * $num[$i]), 1, 1);
        }
        $n = 10 - substr($suma, strlen($suma) - 1, 1);

        //comprobacion de NIFs especiales (se calculan como CIFs)
        if (preg_match('/^[KLM]{1}/', $cif)) {
            if ($num[8] == chr(64 + $n)) {
                return 1;
            } else {
                return -1;
            }
        }

        //comprobacion de CIFs
        if (preg_match('/^[ABCDEFGHJNPQRSUVW]{1}/', $cif)) {
            if ($num[8] == chr(64 + $n) || $num[8] == substr($n, strlen($n) - 1, 1)) {
               return 2;
            } else {
               return -2;
            }
        }
        //comprobacion de NIEs
        //T
        if (preg_match('/^[T]{1}/', $cif)) {
            if ($num[8] == preg_match('/^[T]{1}[A-Z0-9]{8}$/', $cif)) {
                return 3;
            } else {
                return -3;
            }
        }
        //XYZ
        if (preg_match('/^[XYZ]{1}/', $cif)) {
            if ($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr(str_replace(array('X','Y','Z'), array('0','1','2'), $cif), 0, 8) % 23, 1)) {
                return 3;
            } else {
                return -3;
            }
        }
        //si todavia no se ha verificado devuelve error
        return 0;
    }

    /**
     * Comprueba si es un número de Seguridad Social válido
     */
    public static function checkss($numero)
    {
        if (!is_numeric($numero)) {
            return false;
        }
        $a= substr($numero, 0, 2); //a los dos primeros numeros
        $c= substr($numero, -2);//a los dos ultimos
        $b= substr($numero, 2, -2);// al resto
        if ($b<10000000) {
            $d=(int)$b+(int)$a*10000000;
            //echo "<br>D b<10.000.000 ahora vale = ".$d." de tipo ".gettype($d);//ESTA LA HACE BIEN
        } else {
            //$bb=int($b);
            $d=$a.$b;
            //echo "<br>D b NO <10.000.000 ahora vale = ".$d." de tipo ".gettype($d); // ESTA LA HACE MAL
        }
        //echo "<br> valor de d (a+b)= ".$d." de tipo ".gettype($d);
        $cc=((double)$c);
        $dd=((double)$d);
        //echo "<br> valor de DD= ".$dd." de tipo ".gettype($dd);
        //$c = new con ("select (".(double)$dd."%97) as foo"); //bcmod((double)$dd,97)
        //$r= $c->getResult();
        //$resto=((double)$dd)%97;
        $resto = bcmod((double)$dd, 97);
        $resultado = round((($dd / 97) - round($dd / 97)) * 97);
        //echo "<BR>RESULTADO = ".$resultado;
        //echo "<br> el resto es ==== ".$resto." de tipo ".gettype($resto);
        //        COMPARACION
        //echo "<br>A COMPARA CON VAlor de C = ".$cc." de tipo ".gettype($cc);
        if ($cc==((double)$resto)) {
            //echo "<br>CORRsECTO";
            return true;
        } else {
            //echo "<br>NO CORRECTO deberia de ser ".$resto;
            return false;
        }
        if ( ($cc==$resultado) or ($cc==((double)$resto)) ) {
            //echo "<br>CORRECTO";
            return true;
        } else {
            //echo "<br><font color=red size=3>NO CORRECTO deberia de ser ".$resto;
            return false;
        }
    }

    /**
     * Comprueba que sea una cuenta corriente válida
     */
    public static function checkcc($value, $sep = "-",&$debug="")
    {
        //list($cc1, $cc2, $cc3, $cc4)
        if ($sep == '') {
            if (preg_match("/^([0-9]{4})([0-9]{4})([0-9]{2})([0-9]{10})$/", $value, $result)) {
                $o = array($result[1],$result[2],$result[3],$result[4]);
            } else {
                $debug = "no hay separador, y no cumple el patrón";
                return false;
            }
        } else {
            $o= explode($sep, $value, 4);
        }

        $o= explode($sep, $value, 4);
        if (sizeof($o) < 4) {
            $debug="4 valores numéricos divididos por " . $sep . "";
            return false;
        }

        list($cc1, $cc2, $cc3, $cc4) = $o;
        $cc1 = trim($cc1);
        if (!is_numeric($cc1)) {
            $debug=$cc1." no es un valor numérico";
            return false;
        }
        $cc2 = trim($cc2);
        if (!is_numeric($cc2)) {
            $debug=$cc2." no es un valor numérico";
            return false;
        }
        $cc3 = trim($cc3);
        if (!is_numeric($cc3)) {
            $debug=$cc3." no es un valor numérico";
            return false;
        }
        $cc4 = trim($cc4);
        if (!is_numeric($cc4)) {
            $debug=$cc4." no es un valor numérico";
            return false;
        }
        $parte1=$cc1."".$cc2;
        $parte2=$cc4;
        $cc="";
        if (($cc1<>"") && ($cc2<>"") && ($cc3<>"") && ($cc4<>"") ) {
                $aPesos = Array(1, 2, 4, 8, 5, 10, 9, 7, 3, 6); // Array de "pesos"
                $dc1 = 0;
                $dc2 = 0;
                $x = 8;
                while ($x > 0) {
                    $digito = $parte1[$x-1];
                    $dc1 = $dc1 + ($aPesos[$x + 2 - 1] * ($digito));
                    $x = $x - 1;
                }
                //$resto = $dc1%11;
                $resto = bcmod($dc1, 11);

                $dc1 = 11-$resto;
                if ($dc1 == 10) $dc1 = 1;
                if ($dc1 == 11) $dc1 = 0;              // Dígito control Entidad-Oficina

                $x=10;
                while ($x>0) {
                    $digito=$parte2[$x-1];
                    $dc2=$dc2+($aPesos[$x-1]*($digito));
                    $x = $x - 1;
                }
                //$resto = $dc2%11;
                $resto = bcmod($dc2, 11);
                $dc2=11-$resto;
                if ($dc2==10) $dc1=1;
                if ($dc2==11) $dc1=0;         // Dígito Control C/C

                $res2=($dc1)."".($dc2);   // los 2 números del D.C.


            $resultado=$res2;
            if ($resultado==$cc3||1==1) {

                return true;


            } else {
                $debug=" error dígito de control erróneo ";
                return false;
            }
        } else {
            $debug=" error gordo";
            return false;
        }


        return false;

    }

    public static function checkcp($value)
    {
        if (!is_numeric($value)) {
            return false;
        }
        if (strlen($value)!==5) {
            return false;
        }
        $a = substr($value, 0, 2); //a los dos primeros numeros
        if ((int)$a > 52) {
            return false;
        }
        $b = substr($value, 2, 3);// al resto*/
        return true;
    }

    public static function secToTime($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor($seconds % 3600 / 60);
        $seconds = $seconds % 60;
        return sprintf("%d:%02d:%02d", $hours, $minutes, $seconds);
    }

    public static function shortText($text, $length = 100, $options = array())
    {
        $delimiter = isset($options['delimiter'])? $options['delimiter'] : '(...)';
        /*
         * Permitimos que no haya separador, por lo que tenemos que comprobar si la clave existe,
         * no hace falta que esté seteada, sino no se aceptaría el valor null
         */
        $separator = key_exists('separator', $options)? $options['separator'] : ' ';

        $initlength = strlen($text);
        $text = $text . ' ';
        $text = substr($text, 0, $length);

        if (!is_null($separator)) {
            $text = substr($text, 0, strrpos($text, $separator));
        }

        if (strlen($text) < $initlength) {
            $text = $text . ' ' . $delimiter;
        }

        return $text;
    }

    /**
     * Obtained from: http://www.php.net/manual/en/function.glob.php#93669
     * Remove the directory and its content (all files and subdirectories).
     * @param string $dir the directory name
     */
    static public function rmrf($dir)
    {
        foreach (glob($dir) as $file) {
            if (is_dir($file)) {
                self::rmrf("$file/*");
                rmdir($file);
            } else {
                unlink($file);
            }
        }
    }

    /**
     * Parseador de inis personalizado para versiones anteriores de PHP5.3
     * procura dar las mismas funcionalidades que la última versión de PHP
     *
     * @param string $filename
     * @param bool $process_sections
     */
    static public function parse_ini_file_custom($filename, $processSections = false)
    {
        if ($processSections === false || version_compare(PHP_VERSION, '5.3.0', '>=')) {
            return parse_ini_file($filename, $processSections);
        }

        $array=array();
        $handle = fopen($filename, "r");
        if ($handle) {
            $currentKey = false;
            while (!feof($handle)) {
                $buffer = fgets($handle, 4096);
                $pc = substr($buffer, 0, 1);
                if ($pc == ";" || trim($buffer)=="") {
                    continue;
                }
                $o = preg_match("|(.*)\[(.*)\]|i", $buffer, $out);
                if ($o && $out) {
                    switch (true) {
                        case $out[1]=="" && $out[2]!="" :
                            $array[$out[2]] = array();
                            $currentKey = $out[2];
                            break;
                        case $out[1]==!"" && $out[2]!="" :
                            if (!isset($array[$currentKey][$out[1]])) {
                                $array[$currentKey][$out[1]] = array();
                            }
                            list(,$value) = explode("=", $buffer, 2);
                            $value = (string)substr(trim($value), 1, -1);
                            $array[$currentKey][$out[1]][$out[2]] = $value;
                            break;
                    }
                } else {
                    list($key,$value) = explode("=", $buffer, 2);
                    $value = (string) substr(trim($value), 1, -1);
                    $key = trim($key);
                    $array[$currentKey][$key] = $value;
                }
            }
        }
        return $array;
    }

    /*
    * Función para devolver un fichero con soporte para http_range (mp3 para <audio> en chrome por ejemplo, o mp4s para iphone...)
    *
    */
    static public function rangeDownload($file) {
		$fp = @fopen($file, 'rb');
	    $size   = filesize($file); // File size
		$length = $size;           // Content length
		$start  = 0;               // Start byte
		$end    = $size - 1;       // End byte
    // Now that we've gotten so far without errors we send the accept range header
    /* At the moment we only support single ranges.
     * Multiple ranges requires some more work to ensure it works correctly
     * and comply with the spesifications: http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
     *
     * Multirange support annouces itself with:
     * header('Accept-Ranges: bytes');
     *
     * Multirange content must be sent with multipart/byteranges mediatype,
     * (mediatype = mimetype)
     * as well as a boundry header to indicate the various chunks of data.
     */
		header("Accept-Ranges: 0-$length");
    	if (isset($_SERVER['HTTP_RANGE'])) {
			$c_start = $start;
			$c_end   = $end;
        	// Extract the range string
			list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
			// Make sure the client hasn't sent us a multibyte range
			if (strpos($range, ',') !== false) {
				header('HTTP/1.1 416 Requested Range Not Satisfiable');
	            header("Content-Range: bytes $start-$end/$size");
	           exit;
    	    }
        	// If the range starts with an '-' we start from the beginning
        	// If not, we forward the file pointer
        	// And make sure to get the end byte if spesified
        	if ($range0 == '-') {
				// The n-number of the last bytes is requested
				$c_start = $size - substr($range, 1);
        	} else {
				$range  = explode('-', $range);
				$c_start = $range[0];
				$c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
			}
	        /* Check the range and make sure it's treated according to the specs.
        	 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
    	     */
	        // End bytes can not be larger than $end.
        	$c_end = ($c_end > $end) ? $end : $c_end;
    	    // Validate the requested range and return an error if it's not correct.
	        if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {

            	header('HTTP/1.1 416 Requested Range Not Satisfiable');
            	header("Content-Range: bytes $start-$end/$size");
            	// (?) Echo some info to the client?
            	exit;
        	}
        	$start  = $c_start;
        	$end    = $c_end;
    	    $length = $end - $start + 1; // Calculate new content length
	        fseek($fp, $start);
        	header('HTTP/1.1 206 Partial Content');
    	}
    	// Notify the client the byte range we'll be outputting
    	header("Content-Range: bytes $start-$end/$size");
    	header("Content-Length: $length");

	    // Start buffered download
    	$buffer = 1024 * 8;
    	while(!feof($fp) && ($p = ftell($fp)) <= $end) {

        	if ($p + $buffer > $end) {

            	// In case we're only outputtin a chunk, make sure we don't
            	// read past the length
        	    $buffer = $end - $p + 1;
    	    }
	        set_time_limit(0); // Reset time limit for big files
        	echo fread($fp, $buffer);
        	flush(); // Free up memory. Otherwise large files will trigger PHP's memory limit.
    	}

	    fclose($fp);

    	exit();

	}

	/**
	 * Devuelve la ruta al directorio de configuraciones
	 * TODO: Podría obtenerse el dato de una variable de entorno seteada en el .htaccess
	 */
	public static function getConfigPath()
	{
	    return realpath(
    	    dirname(__FILE__)
    	    . DIRECTORY_SEPARATOR . '..'
    	    . DIRECTORY_SEPARATOR . '..'
    	    . DIRECTORY_SEPARATOR . 'configuracion'
	    );
	}

    function truncateHtml($html, $maxLength = 100)
    {
        $printedLength = 0;
        $position = 0;
        $tags = array();
        $retString = '';

        while ($printedLength < $maxLength && preg_match('{</?([a-z]+)[^>]*>|&#?[a-zA-Z0-9]+;}', $html, $match, PREG_OFFSET_CAPTURE, $position))
        {
            list($tag, $tagPosition) = $match[0];

            // Print text leading up to the tag.
            $str = substr($html, $position, $tagPosition - $position);
            if ($printedLength + strlen($str) > $maxLength)
            {
                $retString .= substr($str, 0, $maxLength - $printedLength);
                $printedLength = $maxLength;
                break;
            }

            $retString .= $str;
            $printedLength += strlen($str);

            if ($tag[0] == '&')
            {
                // Handle the entity.
                $retString .= $tag;
                $printedLength++;
            }
            else
            {
                // Handle the tag.
                $tagName = $match[1][0];
                if ($tag[1] == '/')
                {
                    // This is a closing tag.

                    $openingTag = array_pop($tags);
                    assert($openingTag == $tagName); // check that tags are properly nested.

                    $retString .= $tag;
                }
                else if ($tag[strlen($tag) - 2] == '/')
                {
                    // Self-closing tag.
                    $retString .= $tag;
                }
                else
                {
                    // Opening tag.
                    $retString .= $tag;
                    $tags[] = $tagName;
                }
            }

            // Continue after the tag.
            $position = $tagPosition + strlen($tag);
        }

        // Print any remaining text.
        if ($printedLength < $maxLength && $position < strlen($html))
            $retString .= substr($html, $position, $maxLength - $printedLength);

        $retString .= '...';
        // Close any open tags.
        while (!empty($tags))
            $retString .= sprintf('</%s>', array_pop($tags));

        return $retString;
    }

    public static function unzip($info, $tmpDir = null) {
        if (is_null($tmpDir)) {
            $tmpDir = sys_get_temp_dir();
        }

        $aFiles = array();
        $filename = $info['tmp_name'];
        $zip = new ZipArchive();
        if (!$zip->open($filename)) {

            return $aFiles;
        }
        $idx = 0;

        while ($name = $zip->getNameIndex($idx)) {
            $oname = str_replace("/", "_", $name);
            if ($contents = $zip->getFromIndex($idx)) {
                if ($fp = $zip->getStream($name)) {
                    $contents="";
                    while (!feof($fp)) {
                        $contents .= fread($fp, 1024);
                    }
                    fclose($fp);

                    file_put_contents($tmpDir . '/' . $oname, $contents);

                    $aFiles[] = array(
                        'tmp_name' => $tmpDir . '/' . $oname,
                        'name'=> $oname,
                        'size'=> filesize($tmpDir . '/' . $oname),
                        'type'=> i::mime_content_type($tmpDir . '/' . $oname)
                    );
                }
            }
            $idx++;
        }
        return $aFiles;
    }

    public static function unrar($info, $tmpDir = null) {
        if (is_null($tmpDir)) {
            $tmpDir = sys_get_temp_dir();
        }

        $aFiles = array();
        $filename = $info['tmp_name'];
        $rar = RarArchive::open($filename);
        if ($rar === false) {
            return $aFiles;
        }
        foreach ($rar as $entry) {
            $oname = str_replace("/", "_", $entry->getName());

            $entry->extract(null, $tmpDir . '/' . $oname);
            $aFiles[] = array(
                'tmp_name' => $tmpDir . '/' . $oname,
                'name'=> $oname,
                'size'=> filesize($tmpDir . '/' . $oname),
                'type'=> i::mime_content_type($tmpDir . '/' . $oname)
            );
        }
        return $aFiles;
    }

    /**
     * Untar gz. Depends on pear Archive_Tar
     */
    public static function untargz($info, $tmpDir = null) {
        if (is_null($tmpDir)) {
            $tmpDir = sys_get_temp_dir();
        }
        $aFiles = array();
        $filename = $info['tmp_name'];

        //Create temporary tar file
        $tmpFile = tempnam(sys_get_temp_dir(), 'tar');
        $zd = gzopen($filename, "r");
        while ($data = gzread($zd, 1000)) {
            file_put_contents($tmpFile, $data, FILE_APPEND);
        }
        gzclose($zd);

        $tar = new Archive_Tar($tmpFile);
        $contents = $tar->listContent();
        foreach ($contents as $content) {
            if ($content['typeflag'] == 0) {
                $oname = str_replace("/", "_", $content['filename']);
                file_put_contents($tmpDir . '/' . $oname, $tar->extractInString($content['filename']));
                $aFiles[] = array(
                    'tmp_name' => $tmpDir . '/' . $oname,
                    'name'=> $oname,
                    'size'=> filesize($tmpDir . '/' . $oname),
                    'type'=> i::mime_content_type($tmpDir . '/' . $oname)
                );
            }
        }
        return $aFiles;
    }

}
