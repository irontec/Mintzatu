<?php


class tablonloginldap extends login {

    public $pl;
    public $cfg;
    public $con;
    public $aMenu;

    protected $_plt = null;
    protected $_createIfNotExists = false;
    protected $_createRolId = null;
    protected $_successLogin = false;
    protected $_kMenu;

    public function __construct($kMenu = false) {

	parent::__construct($kMenu);
        if (!$kMenu->menuSectionExists('main','loginfile')) {
            iError::error("No se encuentra el fichero de configuración de login.");
            return false;
        }



        $iniFile = dirname(__FILE__)."/../../../configuracion/" . $kMenu->getMenuSection('main', 'loginfile');
        $this->cfg = parse_ini_file($iniFile, true);
        $this->_kMenu = $kMenu;
        $this->_parseConfig();
        tablon::foo(); //para incluir las rutas de las plantillas
        $this->pl = new tablon_plantilla(RUTA_PLANTILLAS . $this->_plt);
    }

    protected function _parseConfig()
    {
        if (!isset($this->cfg['main']['plt'])) {
           iError::error('No se ha definido el plt para tablonloginldap');
        }
        $this->_plt = trim($this->cfg['main']['plt']);

        if (isset($this->cfg['ldap']['createIfNotExist'])
           && $this->cfg['ldap']['createIfNotExist']) {
           $this->_createIfNotExists = true;
        }

        if (isset($this->cfg['ldap']['createRoleID'])
           && !empty($this->cfg['ldap']['createRoleID'])) {
           $this->_createRolId = $this->cfg['ldap']['createRoleID'];
       }
    }

    public function createIfNotExists()
    {
        return $this->_createIfNotExists;
    }

    public function getCreateRolId()
    {
       return $this->_createRolId;
    }

    protected function _checkUser() {
        if ((isset($_COOKIE['logout'])) && ($_COOKIE['logout']=="out")) {
          return false;
        }

        if ($this->user !== false && $this->pass !== false) {
            if ($this->checkLDAP()) {
                $con = new con($this->getSQL());
                if( $con->error() ) {
                    iError::error($con->getError());
                    $this->drawErrors($con->getErrorNumber());
                } else {
                    if($con->getNumRows() !=1 ) {
                        if ($this->createIfNotExists()) {
                            $con = new con("insert into karma_usuarios (login) values ('" . $this->user . "')");
                            $idLast = $con->getLastId();
                            if ($idLast) {
                                $result['__ID'] = $idLast;
                                if (!is_null($this->getCreateRolId())) {
                                    $con = new con(
                                        "insert into karma_rel_usuarios_roles "
                                        . "(id_usuario,id_rol) values "
                                        . "('"
                                        . "'" . $idLast . "'"
                                        . ",'" . $this->getCreateRolId() . "'"
                                        . ")"
                                    );
                                }
                            }
                        } else {
                            return false;
                        }
                    }
                    else {
                        $result = $con->getResult();
                    }
                }
                $id_sesionUsr = $this->user;
                $_SESSION['__USER'] = $id_sesionUsr;
                $this->userID = $result['__ID'];
                $_SESSION['__ID'] = $result['__ID'];
                $this->aMenu = $this->getAccess();
                $this->_successLogin = true;
                return true;
            }
            return false;
        } else {
            if (!isset($_SESSION['__USER'])) return false;
        }
    }

    public function checkLDAP() {
        con::foo();
        $t_username = mysql_real_escape_string($this->user);
        $p_password = $this->pass;

        $t_ldap_uid_field = isset($this->cfg['ldap']['uid']) ? $this->cfg['ldap']['uid']:'uid';

        $t_ldap_organization = '';
        $t_search_filter = "(&$t_ldap_organization($t_ldap_uid_field=$t_username))";
        $t_search_attrs = array( $t_ldap_uid_field, 'dn' );

        /**
         * Intentamos conectarnos a través de kldapcon, si da alguna excepción
         * lo intentamos con la configuración de karma_usuarios.cfg
         */
        try {
            $ldapCon = kldapcon::getLdapCon();
            $t_ds1 = $ldapCon->getLdapConn();
            /*
             * Clonamos la conexión para que el unbind que viene luego no nos afecte...
             */
            $ldapCon = clone $ldapCon;
            $t_ds = $ldapCon->getLdapConn();
            $t_ldap_root_dn = $ldapCon->getDnBase();
        } catch (Exception $e) {
            $t_ldap_root_dn = $this->cfg['ldap']['root_dn'];
            $t_ds = ldap_connect($this->cfg['ldap']['server']);
            if (isset($this->cfg['ldap']['protocol_version'])) {
                ldap_set_option($t_ds, LDAP_OPT_PROTOCOL_VERSION, $this->cfg['ldap']['protocol_version']);
            }
        }

        # Search for the user id
        $t_sr   = ldap_search( $t_ds, $t_ldap_root_dn, $t_search_filter, $t_search_attrs );
        $t_info = ldap_get_entries( $t_ds, $t_sr );

        $t_authenticated = false;

        if ( $t_info ) {
                # Try to authenticate to each until we get a match
                for ( $i = 0 ; $i < $t_info['count'] ; $i++ ) {
                        $t_dn = $t_info[$i]['dn'];
                        # Attempt to bind with the DN and password
                        if ( @ldap_bind( $t_ds, $t_dn, $p_password ) ) {
                            $t_authenticated = true;
                            break; # Don't need to go any further
                        }
                }
        }

        ldap_free_result( $t_sr );
        ldap_unbind( $t_ds );

        return $t_authenticated;
    }

    protected function getCreateSQL() {
        $pl = &$this->pl;
        $sql = "create table ".$pl->getTab()."(\n".$pl->getID()." mediumint unsigned not null auto_increment";

        for ($i=0;$i<$pl->getNumFields();$i++) {
            if (($pl->fields[$i]->getIndex()==$this->cfg['main']['login'])||($pl->fields[$i]->getIndex()==$this->cfg['main']['pass'])) {
                $sql .= ",\n".$pl->fields[$i]->getSQL("",false)." ".$pl->fields[$i]->getSQLType();
                if ($pl->fields[$i]->hasSubFields()) {
                    for ($j=0;$j<$pl->fields[$i]->sizeofsubFields;$j++) {
                    }
                }
            }
        }
        if ($del = $pl->getDeletedFLD()) {
            $sql .= ",\n".$del." enum('0','1') not null default '0'";

        }
        $sql .= ",\nprimary key(".$pl->getID().")\n) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        return $sql;
    }

    protected function getFirstInsert() {
        $pl = &$this->pl;
        $sql= "insert into ".$pl->getTab()." (".$this->cfg['main']['login'].",".$this->cfg['main']['pass'].")  values ('".$this->user."','".i::cifrar($this->pass)."') ;";
        return "<textarea rows='3' cols='40' class='warn'>".$sql."</textarea>";
    }

    protected function drawErrors($errorNumber) {
        switch($errorNumber) {
            case 1146:
                iError::warn("<textarea rows='7' cols='40' class='warn'>".$this->getCreateSQL()."</textarea>");
                iError::warn("Propuesta de insert de superUsuario");
                iError::warn($this->getFirstInsert());
            break;
            case 1054:
                if (preg_match("/column '(.*)' in/",$this->conPrinc->getError(),$r)) {
                    $pl = &$this->plantillaPrincipal;
                    $warnAux = "";
                    for ($i=0;$i<$pl->getNumFields();$i++) {
                        if ($r[1] == $pl->fields[$i]->getIndex()) {
                            $warnAux =  $pl->fields[$i]->getSQLType();
                            break;
                        }
                    }
                    $warnText = "alter table ".$this->plantillaPrincipal->getTab()." add ".$r[1]." ".$warnAux;
                    iError::warn("<textarea rows='7' cols='40' class='warn'>".$warnText."</textarea>");
                }
            break;
        }
    }

    protected function getSQL() {
        con::foo();
        $sql = "select SQL_CALC_FOUND_ROWS ";
        $sql .= $this->pl->getTab().".".$this->pl->getID()." as __ID ";
        $sql .= ", ".$this->cfg['main']['login']." as __LOGIN ";
        $sql .= " from ".$this->pl->getTab()." where ".$this->pl->getTab().".".$this->cfg['main']['login']." = '".mysql_real_escape_string($this->user)."' limit 1";
        return $sql;
    }

    protected function getAccessSQL() {
        $pl = $this->cfg['main'];
        $sql = "select distinct(".$pl["groupsacctab"].".".$pl["groupsaccid"].") as __KEY , ";
        $sql.= " ".$pl["groupsacctab"].".".$pl["groupsacccond"].", ".$pl["groupsreltab"].".".$pl["groupsrelcond"]." ";
        $sql.= " from ".$pl["groupsacctab"]." ".$pl["groupsacctab"]." ";
        $sql.= " left join ".$pl["groupsreltab"]." ".$pl["groupsreltab"]." on (".$pl["groupsreltab"].".".$pl["groupsrelid"]."=".$pl["groupsacctab"].".".$pl["groupsacccond"].")";
        $sql.= " where ".$pl["groupsreltab"].".".$pl["groupsrelcond"]." = '".$this->userID."' ";
        $sql.= " group by __KEY;";
        return $sql;
    }

    protected function getAccess()
    {
        $sql = $this->getAccessSQL();
        $con = new con($sql);
        if ($con->getNumRows() > 0) {
            $ar_acc = array();
            while ($r = $con->getResult()) {
                $ar_acc[$r["__KEY"]] = $r["__KEY"];
            }
            return $ar_acc;
        }
        return false;
    }

    public function loginInfo() {
        return "Bienvenido <span id='karmaBarUser'>".$this->user."</span> | <a href='".i::base_url()."?logout'>Cerrar sesión</a>";
        /*  loginInfo previo a karmaBar return "<div id=\"cajaUsuario\"><img src=\"./icons/user.png\" title=\"Usuario\" />&nbsp;&nbsp;Conectado como usuario: ".$this->user." (".$_SESSION["__ID"].")&nbsp;&nbsp;<a href=\"".i::base_url()."?logout\" title=\"Logout\" ><img src=\"./icons/p_negativo.png\" title=\"Desconectar\" alt=\"Desconectar\"/></a></div>"; */
    }


}
