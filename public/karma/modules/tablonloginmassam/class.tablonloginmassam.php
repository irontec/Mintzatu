<?php



class tablonloginmassam extends login {
    public $pl;
    public $cfg;
    public $con;
    public $aMenu;

    function __construct($kMenu = false) {
        if (!$kMenu->menuSectionExists('main','loginfile')) {
            iError::error("No se encuentra el fichero de configuración de login.");
            return false;
        }
        $this->cfg = parse_ini_file(dirname(__FILE__) . "/../../../configuracion/" . $kMenu->getMenuSection('main','loginfile'), true);
        tablon::foo(); //para incluir las rutas de las plantillas
        $this->pl = new tablon_plantilla(RUTA_PLANTILLAS . $this->cfg['main']['plt']);
    }

    protected function _checkUser() {
        define("CHK_PUBLIC",1);
        if ((isset($_COOKIE['logout'])) && ($_COOKIE['logout']=="out")) return false;

        ini_set("soap.wsdl_cache_enabled", 0);
        //$cliente = new SoapClient($_SERVER['SAM_WSDL'],array("trace"=> 1,"exceptions" => 1));
        $cliente = new samService(false);

        if ($this->user!==false && $this->pass!==false) {
            $clienteSOAPtemp = new SoapClient($_SERVER['SAM_WSDL'],array("trace"=> 1,"exceptions" => 1));
            $id_sesionUsr = $cliente->validar_usuario($this->user,$this->pass);
            $id__sesionUsr = $clienteSOAPtemp->validar_usuario($this->user,$this->pass);
            //$id_sesionUsr = $cliente->validar_usuario($this->user,$this->pass);

            if(($id__sesionUsr=="-1"||$id__sesionUsr=="0"))    return false;

            $_SESSION['id_ws_karma'] = $id__sesionUsr;




            $_SESSION['__USER'] = $id__sesionUsr;

            $AUsr = get_object_vars($cliente->getDatosUsuario($id_sesionUsr,$this->user));



            $id_Usr = $AUsr['id'];
            $this->userID = $id_Usr;
            $_SESSION['__ID'] = $id_Usr;


            $this->aMenu = $this->getAccess();






            return true;

        } else {
            //if (!isset($_SESSION['__USER'])) return false;
            if (!isset($_SESSION['id_ws_karma'])&&!isset($_SESSION['__USER'])) return false;
        }



    }

    /*function _checkUser($u,$p) {
        ini_set("soap.wsdl_cache_enabled", 0);
        //$cliente = new SoapClient($_SERVER['SAM_WSDL'],array("trace"=> 1,"exceptions" => 1));
        $cliente = new samService(false);
        if ($u!==false && $p!==false) {
            $id_sesionUsr = $cliente->validar_usuario($u,$p);
            $_SESSION['id_ws_karma'] = $id_sesionUsr;
        } else {
            if (!isset($_SESSION['id_ws_karma'])) return false;
        }

        if ($user = $cliente->validar_autenticado($_SESSION['id_ws_karma'])) {
            $datosCliente = get_object_vars($cliente->getDatosUsuario($_SESSION['id_ws_karma'],$user));
            if ($datosCliente['esAdmin']) {
                return true;
            }
        }
        return false;
    }
*/


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

    protected function drawErrors() {
        iError::error($this->con->getError());
        switch($this->con->getErrorNumber()) {
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
        $sql .= ", ".$this->cfg['main']['pass']." as __PASS ";
        $sql .= " from ".$this->pl->getTab()." where ".$this->pl->getTab().".".$this->cfg['main']['login']." = '".mysql_real_escape_string($this->user)."' limit 1";
        return $sql;
    }


    protected function getAccessSQL() {
        //var_dump($this->cfg);
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
/*    loginInfo previo a karmaBar        return "<div id=\"cajaUsuario\"><img src=\"./icons/user.png\" title=\"Usuario\" />&nbsp;&nbsp;Conectado como usuario: ".$this->user." (".$_SESSION["__ID"].")&nbsp;&nbsp;<a href=\"".i::base_url()."?logout\" title=\"Logout\" ><img src=\"./icons/p_negativo.png\" title=\"Desconectar\" alt=\"Desconectar\"/></a></div>";    */
    }


}

?>
