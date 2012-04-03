<?php
/**
 * Conector que trata de generar una abstracción para el acceso LDAP
 * @author eider
 * @author alayn
 *
 */
class LdapCon
{
    protected $_link;
    //public $fConf = false;

    protected $_dnBase = false;
    protected $_hostname = false;
    protected $_port = false;
    protected $_user = false;
    protected $_pass = false;
    protected $_ldapconn = false;
    protected $_ldapbind = false;
    public $searchResult = false;

    /**
     * Constructor
     * @param array $conf Configuración de la conexión
     * @return bool
     */
    function __construct(array $conf)
    {
        if (!sizeof($conf)) {
            throw new Exception('EmptyLdapLinkConfFound');
        }

        $this->_loadConf($conf);
        return $this->_connectLDAP();
    }

    public function getLink()
    {
        return $this->_link;
    }

    public function getDnBase()
    {
        return $this->_dnBase;
    }

    public function getHostname()
    {
        return $this->_hostname;
    }

    public function getPort()
    {
        return $this->_port;
    }

    public function getUser()
    {
        return $this->_user;
    }

    public function getPass()
    {
        return $this->_pass;
    }

    public function getLdapConn()
    {
        return $this->_ldapconn;
    }

    public function getLdapBind()
    {
        return $this->_ldapbind;
    }

    /**
     * Carga la configuración de la conexión/LDAP desde
     * db/ldap_access[entorno].cfg
     */
    protected function _loadConf($conf)
    {
        if (!isset($conf['hostname']) || empty($conf['hostname'])) {
            throw new Exception("Ningún servidor de LDAP indicado en '{$filePath}'");
        }
        if (!isset($conf['dnBase']) || empty($conf['dnBase'])){
            throw new Exception("No existe ruta (DN) base en '{$filePath}'");
        }
        if (!isset($conf['cnUser']) || empty($conf['cnUser'])) {
            throw new Exception("No hay ningún usuario administrador de LDAP en '{$filePath}'");
        }

        $this->_dnBase = $conf['dnBase'];
        $this->_hostname = $conf['hostname'];
        $this->_user = $conf['cnUser'];

        if (isset($conf['port']) && !empty($conf['port'])) {
            $this->_port = $conf['port'];
        }
        if (isset($conf['cnPass']) && !empty($conf['cnPass'])) {
            $this->_pass = $conf['cnPass'];
        }

    }

    public function doSearchDN($_dn = "")
    {
        if ($_dn == "") {
            return $this->_dnBase;
        } else {
            if (preg_match("/" . $this->_dnBase."$/", $_dn)) {
                return $_dn;
            } else {
                return $_dn . "," . $this->_dnBase;
            }
        }
    }

    /**
     * Realiza la conexión a LDAP
     * @return bool
     */
    protected function _connectLDAP()
    {
        if ($this->_port !== false) {
            $ldapconn = ldap_connect($this->_hostname, $this->_port)
                or die("No se puede establecer la conexión a ".$this->_hostname);
        } else {
            $ldapconn = ldap_connect($this->_hostname)
                or die("No se puede establecer la conexión a ".$this->_hostname);
        }
        $this->_ldapconn = $ldapconn;

        $user = $this->_user . "," . $this->_dnBase;
        $ldapbind = ldap_bind($this->_ldapconn, $user, $this->_pass);

        if ($ldapbind === false) {
            $msg = "
                Error al enlazar con el directorio con
                los datos de usuario especificados" . ldap_error($ldapconn);
            die ($msg);
        }

        $this->_ldapbind = $ldapbind;

        return true;
    }

    /**
     * Devuelve el objeto de la conexión a LDAP, por si queremos reutilizarlo en algún sitio.
     * @param string $link Todavía no sirve para nada, pero probablemente podamos usarlo para coger la conexión de lectura o escritura
     */
    public function getConnection($link = 'default')
    {
        return $this->_ldapconn;
    }

    /**
     * Realiza una búsqueda sobre el directorio indicado según filtros
     * especificados
     * @param $_dnSearch: directorio desde el que realizar
     * la búsqueda (se concatena al diretorio base de conexión)
     * @param $_filters : cadena de filtros de búsqueca
     * @return array : resultados de la búsqueda en caso de éxito.
     */
    public function doSearch($_dnSearch, $_filters)
    {
        if (is_array($_dnSearch)) {
            $dnSearch = implode(",", $_dnSearch) . "," . $this->_dnBase;
        } else {
            $dnSearch = $this->doSearchDN($_dnSearch);
        }

        $searchRes = ldap_search($this->_ldapconn, $dnSearch, $_filters) or die(ldap_error($this->_ldapconn));
        $result = ldap_get_entries($this->_ldapconn, $searchRes);
        if (isset($result) && is_array($result) && count($result)>0
           && isset($result['count']) && $result['count']>0) {
            $this->searchResult = $result;
        } else {
            $this->searchResult = false;
        }
        return $this->searchResult;
    }

    /**
     * Comprueba si un campo dado es realmente un campo de un objeto de LDAP
     * @param $_fld: campo a buscar
     * @param $_obj: objeto en el que buscar
     * @return boolean
     */
    public function fldExistsInObj($_fld, $_obj)
    {
        $dnSearch = $this->doSearchDN();
        $filter = "(&(objectClass=".$_obj."))";
        $attributes = array($_fld);
        $searchRes = ldap_search($this->_ldapconn, $dnSearch, $filter, $attributes, 0, 0, 0, 0)
            or die(ldap_error($this->_ldapconn));
        $entry = ldap_first_entry($this->_ldapconn, $searchRes);
        $attrs = ldap_get_attributes($this->_ldapconn, $entry);
        if (array_key_exists($_fld, $attrs)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Comprueba si un campo dado es realmente resultado de una búsqueda
     * @param $_fld: campo a buscar
     * @param $_obj: objeto en el que buscar
     * @return boolean
     */
    public function fldExistsInSearch($_fld, $_dnSearch, $_filter)
    {
        $dnSearch = $this->doSearchDN($_dnSearch);
        $filter = $_filter;
        $attributes = array($_fld);

        $searchRes = ldap_search($this->_ldapconn, $dnSearch, $filter, $attributes, 0, 0, 0, 0)
            or die(ldap_error($this->_ldapconn));
        $entry = ldap_first_entry($this->_ldapconn, $searchRes);
        $attrs = ldap_get_attributes($this->_ldapconn, $entry);
        if (array_key_exists($_fld, $attrs)) {
            return true;
        } else {
            return false;
        }
    }

    /* TODO
     * Error handler...
     * ldap_error
     * ldap_errno
     * */

    /**
     * Cierre de conexión a LDAP
     */
    public function closeCon()
    {
        $close = ldap_unbind($this->_ldapconn);
    }

    /**
     * Realiza una búsqueda (la indicada) de los atributos indicados sobre el
     * directorio indicado según filtros especificados
     * @param string $_dnS: directorio desde el que realizar
     * la búsqueda (se concatena al diretorio base de conexión)
     * @param string $_filters : cadena de filtros de búsqueca
     * @param array $_attrs: array con los atributos a devolver en la búsqueda
     * @return array : resultados de la búsqueda en caso de éxito.
     */
    public function find_ldap($_dnS, $_filters, $_attrs, $_action, $_order = null)
    {
        if (is_array($_dnS)) {
            $dnS = implode(",", $_dnS);
        } else {
            $dnS = $_dnS;
        }
        $dnS = $this->doSearchDN($dnS);
        $con = $this->_ldapconn;
        switch ($_action) {
            case 'read':
                $searchRes = ldap_read($con, $dnS, $_filters, $_attrs) or die(ldap_error($con));
                break;
            case 'list':
                $searchRes = ldap_list($con, $dnS, $_filters, $_attrs) or die(ldap_error($con));
                break;
            case 'search':
                $searchRes = ldap_search($con, $dnS, $_filters, $_attrs) or die(ldap_error($con));
            default:
                break;
        }
        if (!is_null($_order)) {
            $result = $this->order_resultSet($searchRes, $_order);
        }
        $result = ldap_get_entries($this->_ldapconn, $searchRes);
        if (isset($result) && is_array($result) && count($result)>0
           && isset($result['count']) && $result['count']>0) {
            $this->searchResult = $result;
        } else {
            $this->searchResult = false;
        }
        return $this->searchResult;
    }


    /**
     * Realiza una búsqueda completa de los atributos indicados sobre el
     * directorio indicado según filtros especificados
     * @param $_dnSearch: directorio desde el que realizar
     * la búsqueda (se concatena al diretorio base de conexión)
     * @param $_filters : cadena de filtros de búsqueca
     * @param array $_attrs: array con los atributos a devolver en la búsqueda
     * @param string $_order: campo por el ordernar los resultados
     * @return array : resultados de la búsqueda en caso de éxito.
     */
    public function search_ldap($_dnSearch, $_filters, $_attrs, $_order = null)
    {
        $ret = $this->find_ldap($_dnSearch, $_filters, $_attrs, "search", $_order);
        return $ret;
    }

    /**
     * Realiza una búsqueda en lista de los atributos indicados sobre el
     * directorio indicado según filtros especificados
     * @param $_dnSearch: directorio desde el que realizar
     * la búsqueda (se concatena al diretorio base de conexión)
     * @param $_filters : cadena de filtros de búsqueca
     * @param array $_attrs: array con los atributos a devolver en la búsqueda
     * @param string $_order: campo por el ordernar los resultados
     * @return array : resultados de la búsqueda en caso de éxito.
     */
    public function list_ldap($_dnSearch, $_filters, $_attrs, $_order = null)
    {
        $ret = $this->find_ldap($_dnSearch, $_filters, $_attrs, "list", $_order);
        return $ret;
    }

    /**
     * Realiza una lectura del objeto (los atributos) indicados sobre el
     * directorio indicado según filtros especificados
     * @param $_dnSearch: directorio desde el que realizar
     * la búsqueda (se concatena al diretorio base de conexión)
     * @param $_filters : cadena de filtros de búsqueca
     * @param array $_attrs: array con los atributos a devolver en la búsqueda
     * @return array : resultados de la búsqueda en caso de éxito.
     */
    public function read_ldap($_dnSearch, $_filters, $_attrs)
    {
        $ret = $this->find_ldap($_dnSearch, $_filters, $_attrs, "read");
        return $ret;
    }

    public function order_resultSet($rs, $_order)
    {
        if ($rs !== false) {
            return ldap_sort($this->_ldapconn, $rs, $_order);
        } else {
            return $rs;
        }
    }

    /**
     * Halla el valor máximo de un atributo de un objeto
     * @param $filtro
     * @param $attrs
     * @return unknown_type
     */
    public function get_max_value($_dnSearch, $_filter, $_attrs)
    {
        $res = $this->search_ldap($_dnSearch, $_filter, array($_attrs));
        $total = $res['count'];
        $max = 0;
        for ($i=0; $i<$total; $i++) {
            $val = $res[$i][$_attrs];
            $num = $val['count']-1;
            $val = $val[$num];
            if ($val > $max) {
                $max = $val;
            }
        }
        if ($max < 500) {
            $max = 500;
        }
        return $max;
    }
    /**
     * Inserta un nuevo objeto en LDAP
     * @param $_dnNew: el dn para el nuevo objeto
     * @param $_data: datos del nuevo objeto
     * @return boolean
     */
    public function newLdapObject($_dnNew, $_data)
    {
        if (is_array($_dnNew)) {
            $dnNew = implode(",", $_dnNew);
        } else {
            $dnNew = $_dnNew;
        }
        $dnN = $this->doSearchDN($dnNew);
        //$dnN = "cn=".$_data['cn'][0].",".$dnN;
        $ret = ldap_add($this->_ldapconn, $dnN, $_data);
        return $ret;
    }

    /**
     * Modifica un objeto en LDAP
     * @param $_dnMod: el dn para del objeto
     * @param $_data: datos del objeto
     * @return boolean
     */
    public function modify_ldapObject($_dnMod, $_data, $_rdnFld)
    {
        if (is_array($_dnMod)) {
            $dnMod = implode(",", $_dnMod);
        } else {
            $dnMod = $_dnMod;
        }
        $dnN = $this->doSearchDN($dnMod);
        $conn = $this->_ldapconn;
        if (isset($_data[$_rdnFld])) {
            $cmpFld = $_rdnFld;
            $cmpVal = $_data[$_rdnFld][0];
            $resultado = ldap_compare($conn, $dnN, $cmpFld, $cmpVal);
            if ($resultado !== true) {
                $hijos = $this->_countSons($dnN);
                /*if ($hijos > 0) {
                    return array("errno"=>"Error", "error"=>"No se puede renombrar un objeto con hijos");
                } else {*/
                    $newRdn = $cmpFld."=".$cmpVal;
                    $ress = ldap_rename($conn, $dnN, $newRdn, NULL, TRUE);
                    if ($ress === true) {
                        $expl = explode(",", $dnN);
                        $first = array_shift($expl);
                        array_unshift($expl, $newRdn);
                        $dnN = implode(",", $expl);
                    }
                //}
            }
        }
        $ret = ldap_modify($conn, $dnN, $_data);
        return $ret;
    }

    /**
     * Elimina un objeto en LDAP
     * @param $_dnMod: el dn para del objeto
     * @param $_data: datos del objeto
     * @return boolean
     */
    public function delete_ldapObject($_dnDel)
    {
        if (is_array($_dnDel)) {
            $dnMod = implode(",", $_dnDel);
        } else {
            $dnMod = $_dnDel;
        }
        if (empty($_dnDel) || is_null($_dnDel)) {

        }
        $dnN = $this->doSearchDN($_dnDel);
        $ret = ldap_delete($this->_ldapconn, $dnN);
        return $ret;
    }

    public function getIfError()
    {
        if (ldap_errno($this->_ldapconn) == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function getError()
    {
        $error = array(
            "errno" => ldap_errno($this->_ldapconn),
            "error" => ldap_error($this->_ldapconn)
        );
        return $error;
    }

    public function modificaAdd($_dn, $_datos)
    {
        $ret = ldap_mod_add($this->_ldapconn, $_dn, $_datos);
        return $ret;
    }

    public function modificaDel($_dn, $_datos)
    {
        return ldap_mod_del($this->_ldapconn, $_dn, $_datos) or die(ldap_error($this->_ldapconn));
    }

    public function modificaRepl($_dn, $_datos)
    {
        return ldap_mod_replace($this->_ldapconn, $_dn, $_datos);
    }

    /**
     * Cuenta a ver si un objeto tiene hijos
     * @param $_dn
     * @return unknown_type
     */
    protected function _countSons($_dn)
    {
        $rs = ldap_list($this->_ldapconn, $_dn, "(&(objectClass=*))", array("count"));
        $res = ldap_get_entries($this->_ldapconn, $rs);
        return $res['count'];
    }

    public function __clone()
    {
        $this->_connectLDAP();
    }
}