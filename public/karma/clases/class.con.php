<?php
/**
 * Conector a base de datos MySQL -- mysqli based
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 *         Ivan Mosquera <ivan@irontec.com>
 * @version 2.1
 * @package karma
 */

class con
{
    private $_conResult;
    private $_conPointer = 0;
    private $_conError = false;
    private $_conErrorNumber = 0;
    private $_conSql;
    private $_conResultType = 'ASSOC';

    const MAXSQL = 1000000;

    protected $_link;

    public static $fConf = false;

    public static $contadorQueries = 0;
    public static $totalMYtime = 0;


    /**
     * Almacen de las conexiones a BBDD
     * Deprecated
     * @var array
     */
    public static $aConectores = array();


    /**
     * Almacen de los adaptadores (cada uno con su conexión a BBDD)
     * @var unknown_type
     */
    private static $_aAdapters = array();


    /**
     * Constructor
     * @param $sql: sentencia a realizar. Si está a null solo se crea el objeto y la conexión
     * @param $link: selector de conexión.
     * @return unknown_type
     */
    function __construct($sql = null,$link = "default")
    {
        $this->_link = $link;

        if (!self::_doCon($this->_link)) {
            $this->_checkError();
        }

        if (!is_null($sql)) {
            $this->query($sql);
        }
    }

    /**
     * Cerrar la conexión
     * @param string $link Nombre de la conexión
     */
    public static function close($link = 'default')
    {
        $retValue = null;
        if (isset(self::$_aAdapters[$link])) {
            $retValue = self::$_aAdapters[$link]->close();
            unset(self::$_aAdapters[$link]);
        }
        return $retValue;
    }

    /**
     * Función que ejecuta una query.
     * @param $sql: sentencia sql.
     */
    public function query($sql)
    {
        $timeStart = microtime(true);
        self::$contadorQueries++;
        $this->_conSql = $sql;

        // Lo utilizo como alias
        $oDbCon = self::$_aAdapters[$this->_link];

        // resetar las variables de error para que se limpien en cada query...
        $this->_conError = false;
        $this->_conErrorNumber = 0;

        $this->_conResult = $oDbCon->query($sql);
        $this->_checkError();

        if (!$this->_conError) {
            $this->reset();
        }
        self::$totalMYtime += microtime(true) - $timeStart;
    }

    /**
     * Getter para obtener atributos privados que antiguamente eran públicos
     * @param string $varName Nombre de la variable
     */
    public function __get($varName)
    {
        switch($varName) {
            case "error_number":
                iError::ok("El parámetro público 'error_number' de la clase 'con' está deprecated");
                return $this->_conErrorNumber;
                break;
            case "link":
                iError::ok("El parámetro público 'link' de la clase 'con' está deprecated");
                return $this->$varName;
                break;
        }
    }

    /**
     * Comprueba si han habido errores durante la ejecución de la consulta
     */
    protected function _checkError()
    {
        $oDbCon = self::$_aAdapters[$this->_link]; // Alias
        if ($oDbCon->getErrorNo() > 0) {
            $this->_conError = $oDbCon->getError();
            $this->_conErrorNumber = $oDbCon->getErrorNo();
        }
    }

    /**
     * Instancia el objeto mysqli en el contenedor estático de la clase
     */
    static function _doCon($link)
    {
        if (isset(self::$_aAdapters[$link])) {
            if (self::ping($link)) {
                return;
            }
        }

        self::_loadfConf();

        $host = self::$fConf[$link]['host'];
        $port = self::$fConf[$link]['port'];
        $user = self::$fConf[$link]['user'];
        $pass = self::$fConf[$link]['pass'];
        $db = self::$fConf[$link]['db'];

        $legacy = true;
        if (isset(self::$fConf[$link]['legacy']) && self::$fConf[$link]['legacy'] == false) {
            $legacy = false;
        }

        /*
         * A lo ñapa para poder seguir usando mysql_real_escape_string
         * FIXME: Deberíamos eliminar cualquier acceso de este tipo, pero las zonas públicas las tenemos vendidas :(
         */
        if ($legacy) {
            $hostLegacy = ($port != NULL)? $host . ':' . $port:$host;
            self::$aConectores["_old_" . $link ] = mysql_connect($hostLegacy, $user, $pass);
        }

        /*
         *  Establecemos el adaptador de la BBDD y realizamos la conexión
         */
        $adapterClass = 'con' . self::$fConf[$link]['adapter'] . 'Adapter';
        $resultClass = 'conResult' . self::$fConf[$link]['adapter'] . 'Adapter';
        include_once(dirname(__FILE__) . '/class.' . $adapterClass . '.php');
        include_once(dirname(__FILE__) . '/class.' . $resultClass . '.php');
        self::$_aAdapters[$link] = new $adapterClass($host, $user, $pass, $db, $port);

        /*
         * Deprecated, lo meto solo por razones histriónicas
         */
        self::$aConectores[$link] = self::$_aAdapters[$link]->getDbConnection();
        return !(self::$_aAdapters[$link]->getErrorNo());
    }

    /**
     * Alias de getDbObj
     * @deprecated
     * @param $nombre Nombre de la conexión
     */
    static function getMysqliObj($nombre = "default")
    {
        return self::getDbObj($nombre);
    }

    /**
     * Devuelve el objeto de conexión a BBDD ver
     * @param $nombre Nombre de la conexión
     */
    static function getDbObj($nombre = "default")
    {
        return self::$aConectores[$nombre];
    }

    /**
     * Comprueba que la conexión está activa y si no lo está intenta reconectarla
     * @param string $link Nombre de la conexión
     */
    protected static function ping($link = 'default')
    {
        return self::$_aAdapters[$link]->ping();
    }


    /**
     *
     * Inicializa el puntero del iterador del resultset a 0
     */
    public function reset()
    {
        $this->_conPointer = 0;
    }

    /**
     * Libera el recordset
     */
    public function free()
    {
        $this->_conResult->freeResult();
    }

    /**
     * Comprobación de errores
     * @return bool Devuelve true si ha habido algún error en la consulta
     */
    public function error()
    {
        return (!empty($this->_conError));
    }


    /**
     * Devuelve el mensaje de error
     * @return string
     */
    public function getError()
    {
        return $this->_conError;
    }


    /**
     * Devuelve el código de error
     * @return int
     */
    public function getErrorNumber()
    {
        return $this->_conErrorNumber;
    }


    /**
     * Devuelve el número de líneas devueltas o afectadas por la consulta
     * @return int
     */
    public function getNumRows()
    {
        return self::$_aAdapters[$this->_link]->getAffectedRows();
    }


    /**
     * Establece la forma de devolver un Resultset.
     * MYSQLI_ASSOC por defecto
     * @param $type [ASSOC|NUM|BOTH]
     */
    public function setResultType($type)
    {
        switch($type) {
            case 'ASSOC':
            case 'BOTH':
            case 'NUM':
                $this->_conResultType = $type;
                return true;
            default:
                return false;
        }
    }

    /**
     * Devuelve el último ID insertado (alias de getId())
     * @return int
     */
    public function getLastId()
    {
        return $this->getId();
    }

    /**
     * Devuelve el último ID insertado
     * @return int
     */
    public function getId()
    {
        return self::$_aAdapters[$this->_link]->getLastInsertId();
    }

    /**
     * Dumpea la consulta SQL y el error (en caso de que lo haya)
     * @return con
     */
    public function dump()
    {
        echo "<br /><br />" . $this->_conSql;
        echo ($this->error())? $this->getError():"SIN ERRORES";
        return $this;
    }


    /**
     * Devuelve la siguiente fila del resultado de una consulta
     * @return array
     */
    public function getResult()
    {
        if ($this->_conResult->isEmpty()) {
             return false;
        }

        if (!$row = $this->_conResult->fetchArray($this->_conResultType)) {
            $this->free();
            return false;
        }

        $this->_conPointer++;

        return $row;
    }


    /**
     * Carga la configuración de la BBDD desde db/access[entorno].cfg
     */
    static protected function _loadfConf()
    {
        /*
         * Si no se ha parseado el ini...
         */
        if (self::$fConf === false) {
            // sirve para crear diferentes configuraciones de conexión a bd mediante variables de entorno
            if ( $ent = getenv('conf_entorno')  ) {
                $iniFile = dirname(__FILE__) . "/../../db/access" . $ent . ".cfg";
            } else {
                $iniFile = dirname(__FILE__) . "/../../db/access.cfg";
            }
            if (!file_exists($iniFile)) {
                die("No se encuentra fichero de acceso a BBDD.");
            }
            self::$fConf = parse_ini_file($iniFile, true);
        }

        /*
         * Establecemos los valores por defecto si no se han indicado
         */
        foreach (self::$fConf as $link => $conf) {
            if (!isset(self::$fConf[$link]['adapter'])) {
                self::$fConf[$link]['adapter'] = 'Mysqli';
            }
            if (!isset(self::$fConf[$link]['host'])) {
                self::$fConf[$link]['host'] = 'localhost';
            }
            if (!isset(self::$fConf[$link]['port'])) {
                if (preg_match('/:([0-9]+)$/', self::$fConf[$link]['host'], $rPort)) {
                    self::$fConf[$link]['port'] = $rPort[1];
                    self::$fConf[$link]['host'] = str_replace(":" . self::$fConf[$link]['port'], "", self::$fConf[$link]['host']);
                } else {
                    self::$fConf[$link]['port'] = 3306;
                }
            }
        }
    }

    /**
     * Devuelve los datos de configuración de la conexión
     * @param string $link Nombre de la conexión
     */
    static public function getDetails($link = "default")
    {
        self::_loadfConf();
        return self::$fConf[$link];
    }

    /**
     * Servía para establecer una conexión antes de hacer un mysql_real_escape_string
     * en vez de esto usar el método con::escape
     * @deprecated No usar
     */
    static public function foo()
    {
        new con("select 1");
    }

    /**
     * Escapa el valor para insertarlo en MySQL
     * @param $value Valor a escapar
     * @param $link Nombre de la conexión
     * @return string
     */
    static function escape($value, $link = "default")
    {
        self::_doCon($link);
        return self::$_aAdapters[$link]->escapeString($value);
    }
}
