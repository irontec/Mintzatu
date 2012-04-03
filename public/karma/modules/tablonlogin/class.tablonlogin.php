<?php
/*
 * Para incluir las rutas de las plantillas,
 * hacemos un include de tablon que es donde se declaran a lo constante global
 */
require_once(dirname(__FILE__) . '/../tablon/class.tablon.php');
class tablonlogin extends login
{
    public $pl;
    public $cfg;
    public $con;
    public $aMenu;
    public $l;

    public function __construct($kMenu = false)
    {
        parent::__construct($kMenu);
        if (!$kMenu->menuSectionExists('main', 'loginfile')) {
            iError::error("No se encuentra el fichero de configuración de login.");
            return false;
        }
        $this->cfg = parse_ini_file(
            dirname(__FILE__) . "/../../../configuracion/" . $kMenu->getMenuSection('main', 'loginfile'),
            true
        );
        $this->pl = new tablon_plantilla(RUTA_PLANTILLAS.$this->cfg['main']['plt']);
    }

    public function getUserRol()
    {
        //groupsrelcond
        $pl1 = &$this->pl;
        $pl2 = $this->cfg['main'];
        $sql = "select distinct(".$pl2["groupsreltab"].".".$pl2["groupsrelid"].") as __IDROL  ";
        $sql.= " from ".$pl2["groupsreltab"]." ".$pl2["groupsreltab"]." ";
        $sql.= " left join ".$pl1->getTab()." ".$pl1->getTab()." on (".$pl1->getTab().".".$pl1->getID()."=".$pl2["groupsreltab"].".".$pl2["groupsrelcond"].")";
        $sql.= " where ".$pl1->getTab().".".$pl1->getID()." = '".$this->userID."' ";
        $sql.= " group by __IDROL;";
        return $sql;
    }

    public function isGodRolID($rolIDArray)
	{
        // Build Query
		$cond = "";
		foreach ($rolIDArray as $rolID){
			if (!empty($cond))	$cond .= " OR ";
			$cond .= "id_rol = ".$rolID;
		}
        $sql = "select god from karma_roles where ".$cond;
		$con = new con($sql);

		// Check if any rolID has god set to 1
		while ($r = $con->getResult())
			if($r['god'] == 1 )
				return true;
		
		// If we reach here, none of the role has god flag active
		return false;
    }
	
	public function getFirstEmpresaID(){
		/* Get First empresa ID */
		$sql = "select id_empresa from shared_empresa ORDER BY id_empresa ASC LIMIT 1";
		$con = new con($sql);
		if ($r = $con->getResult())
			return $r['id_empresa'];
		return 0;
	}

    protected function _checkUser()
    {



        if ((isset($_COOKIE['logout'])) && ($_COOKIE['logout']=="out")) {
            return false;
        }
        if ($this->user !== false && ($this->pass !== false xor $this->tmppass !== false )) {
            $this->con = new con($this->getSQL());
            if ( $this->con->error() ) {
                $this->drawErrors();
            } else {
                if ($this->con->getNumRows() != 1) {
                    return false;
                }
                $result = $this->con->getResult();

                if (!$this->pass && $this->tmppass) {
                    $passCifrada = $this->tmppass;
                }else{
                    list(,,$salt,) = explode("$", $result['__PASS']);
                    $passCifrada = crypt($this->pass, '$1$' . $salt . '$');
                }
                if ($passCifrada != $result['__PASS']) {
                    return false;
                }
            }
            $idSesionUsr = $this->user;
            $_SESSION['__USER'] = $idSesionUsr;
            $_SESSION['__PW'] = $passCifrada;
            $this->userID = $result['__ID'];
            $_SESSION['__ID'] = $result['__ID'];
            $_SESSION['__ID'] = $result['__ID'];
	    if (isset($result['__GRUPO_VINCULADO'])) {
		    $_SESSION['__GRUPO_VINCULADO'] = $result['__GRUPO_VINCULADO'];
	    } else {
			// Check Empresa change if requested
			if (isset($_POST['empresa']))
			    	$_SESSION['__GRUPO_VINCULADO'] = (int)$_POST['empresa'];
			// If none is selected, select first 
			if (!isset($_SESSION['__GRUPO_VINCULADO']))
					$_SESSION['__GRUPO_VINCULADO'] = $this->getFirstEmpresaID();
	    }
            $this->aMenu = $this->getAccess();

            $rolid = $this->getUserRol();
            $con = new con($rolid);
            if ($con->getNumRows() > 0) {
                $this->aUserROLID = array();
                while ($r = $con->getResult()) {
                    $this->aUserROLID[] = $r['__IDROL'];
                }
                $_SESSION['__IDROL'] = $this->aUserROLID;
            }

		// Check User roles
 		$_SESSION['__ISGOD'] = $this->isGodRolID($_SESSION['__IDROL']);

            return true;

        } else {
            if (!isset($_SESSION['__USER'])) return false;
        }

    }
    protected function getCreateSQL()
    {
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

    protected function getFirstInsert()
    {
        $pl = &$this->pl;
        $sql= "insert into ".$pl->getTab()." (".$this->cfg['main']['login'].",".$this->cfg['main']['pass'].")  values ('".$this->user."','".i::cifrar($this->pass)."') ;";
        return "<textarea rows='3' cols='40' class='warn'>".$sql."</textarea>";
    }
    protected function drawErrors()
    {

        iError::error($this->con->getError());
        switch($this->con->getErrorNumber()) {
            case 1146:
                iError::warn("<textarea rows='7' cols='40' class='warn'>".$this->getCreateSQL()."</textarea>");
                iError::warn("Propuesta de insert de superUsuario");
                iError::warn($this->getFirstInsert());
                break;
            case 1054:
                if (preg_match("/column '(.*)' in/",$this->con->getError(),$r)) {
                    $pl = &$this->plantillaPrincipal;
                    $warnAux = "";
                    for ($i=0;$i<$pl->getNumFields();$i++) {
                        if ($r[1] == $pl->fields[$i]->getIndex()) {
                            $warnAux =  $pl->fields[$i]->getSQLType();
                            break;
                        }
                    }
                    $warnText = "alter table ".$this->plantillaPrincipal->getTab()." add ".$r[1]." ".$warnAux;
                    iError::error("<textarea rows='7' cols='40' class='warn'>".$warnText."</textarea>");
                }
                break;
            case 1045:
                echo "ERROR en la BBDD!!!<br><strong>".$this->con->getErrorNumber()." - ".$this->con->getError()."</strong><br>Revise el fichero de acceso y los permisos de la BBDD";
                die();
                break;
        }
    }

    protected function getSQL()
    {
        con::foo();
        $sql = "select SQL_CALC_FOUND_ROWS ";
        $sql .= $this->pl->getTab().".".$this->pl->getID()." as __ID ";
        $sql .= ", ".$this->cfg['main']['login']." as __LOGIN ";
        $sql .= ", ".$this->cfg['main']['pass']." as __PASS ";
        $sql .= " from ".$this->pl->getTab()." where ".$this->pl->getTab().".".$this->cfg['main']['login']." = '".mysql_real_escape_string($this->user)."' limit 1";
        return $sql;
    }


    protected function getAccessSQL()
    {
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

    public function loginInfo()
    {
        return $this->l->l('Welcome')." <span id='karmaBarUser' title=\"session id => ".$_SESSION["__ID"]."\" >".$this->user."</span> | <a href='".i::base_url()."?logout' class=\"tooltip\" title=\"".$this->l->l('desconectar')."\">".$this->l->l('desconectar')."</a>";
    }

    public function getUserLang()
    {

        $sql = "select  ";
        $sql .= $this->pl->getTab().".lang as __LANG ";
        $sql .= " from ".$this->pl->getTab()." where ".$this->pl->getTab().".".$this->pl->getID()." = '".$_SESSION["__ID"]."' limit 1";
        $c= new con($sql);
        if ($c->getNumRows()<=0) return false;
        $r = $c->getResult();
        return $r['__LANG'];
    }


}
