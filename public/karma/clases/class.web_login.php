<?php
/**
 * Clase que pretende facilitar el proceso de login de la zona pública
 * Si el usuario está logueado, almacena el uid y pass del usuario en las variables de sesión
 * $_SESSION['uid'] y $_SESSION['pwd']
 *
 */
class web_login {
	protected $table;
	protected $uid_fld;
	protected $login_fld;
	protected $pass_fld;
	protected $alias_fld;
	protected $deleted_fld;
	protected $checked;

	protected $pass;

	public $uid;
	public $alias;
	public $user;

	/**
	 * Constructor de la clase web_login. Recibe como parámetros datos sobre la tabla de usuarios
	 *
	 * @param array $conf -- Array con la configuración de los campos necesarios para hacer login.
	 */
	function __construct($conf) {
		//Se abre una conexión a la base de datos para poder utilizar mysql_real_escape_string sin problemas
		con::foo();

		$this->checked = false;
		$this->table = $conf['tab'];
		$this->uid_fld = $conf['uid_fld'];
		$this->login_fld = $conf['login_fld'];;
		$this->pass_fld = $conf['pass_fld'];
		$this->alias_fld = $conf['alias_fld'];
		$this->deleted_fld = isset($conf['deleted_fld'])?$conf['deleted_fld']:"borrado";

	}

	/**
	 * Función de logout, se encarga de eliminar las variables de sesión
	 * $_SESSION['uid'] y $_SESSION['pwd']
	 *
	 */
	function doLogout(){
		$this->checked = false;
		unset($_SESSION['uid'],$_SESSION['pwd']);
	}

	public function checkPass($pass, $saltedpass){
		//Obtenemos el hash de la pass recibida
		list(,,$salt,) = explode("$",$saltedpass);
		$pwd = i::cifrar($pass,$salt);
		return ($pwd === $saltedpass);
	}

	/**
	 * Función de login, se encarga de confirmar que el usuario y password facilitados son correctos.
	 * Si no lo son se encarga de llamar automáticamente al logout.
	 *
	 * @param string $user
	 * @param string $pass
	 * @return array con los datos del usuario (id,user,pass,alias), false si el login ha fallado
	 */
	public function doLogin($user,$pass){
		$user = mysql_real_escape_string($user);
		$pass = mysql_real_escape_string($pass);

        $sql = "select ".$this->uid_fld." as __id, ".$this->pass_fld." as __pass, ".$this->login_fld." as __user, ".$this->alias_fld." as __alias ";
        $sql.= " from ".$this->table." where ".$this->login_fld." = '".$user."' and ".$this->deleted_fld." = '0' limit 1;";
		$con = new con($sql);

		if ($con->getNumRows()==1){
			$r = $con->getResult();

			//Si son iguales aceptamos el login seteamos las variables de sesión y devolvemos los datos
			if ($this->checkPass($pass,$r['__pass'])){
                $_SESSION['uid'] = $r['__id'];
                $_SESSION['pwd'] = $r['__pass'];

                $this->uid = $r['__id'];
                $this->user = $r['__user'];
                $this->pass = $r['__pass'];
                $this->alias = $r['__alias'];

                $returnValues = array(
                	"id" => $r['__id'],
                	"user" => $r['__user'],
                	"pass" => $r['__pass'],
                	"alias" => $r['__alias']
                );
				$this->checked = true;
                return $returnValues;
			}
		}

		$this->doLogout();
		return false;
	}

	/**
	 * Función de login, se encarga de confirmar que el uid y el hash almacenados en las variables de sesión
	 * $_SESSION['uid'] y $_SESSION['pwd'] son correctos. Si no lo son se encarga de llamar automáticamente al logout.
	 *
	 * @return array con los datos del usuario (id,user,pass,alias), false si el login ha fallado
	 */
	public function checkLoggedIn(){
		$uid = mysql_real_escape_string($_SESSION['uid']);
		$pwd = mysql_real_escape_string($_SESSION['pwd']);

        $sql = "select ".$this->uid_fld." as __id, ".$this->pass_fld." as __pass, ".$this->login_fld." as __user, ".$this->alias_fld." as __alias ";
        $sql.= " from ".$this->table." where ".$this->uid_fld." = '".$uid."' and ".$this->deleted_fld." = '0' limit 1;";
		$con = new con($sql);

		if ($con->getNumRows()==1){
			$r = $con->getResult();

			if ($pwd==$r['__pass']){
                $_SESSION['uid'] = $r['__id'];
                $_SESSION['pwd'] = $r['__pass'];

                $this->uid = $r['__id'];
                $this->user = $r['__user'];
                $this->pass = $r['__pass'];
                $this->alias = $r['__alias'];

                $returnValues = array(
                	"id" => $r['__id'],
                	"user" => $r['__user'],
                	"pass" => $r['__pass'],
                	"alias" => $r['__alias']
                );
				$this->checked = true;
                return $returnValues;
			}
		}

		$this->doLogout();
		return false;
	}

	public function isLoggedIn(){
		return $this->checked;
	}

	public function getPass(){
		return $this->pass;
	}

	public function load(){
		$sql = "select ".$this->uid_fld." as __id, ".$this->pass_fld." as __pass, ".$this->login_fld." as __user, ".$this->alias_fld." as __alias ";
        $sql.= " from ".$this->table." where ".$this->uid_fld." = '".$this->uid."'";
		$con = new con($sql);

		if (!$con->getError()){
			if ($con->getNumRows()==1){
				$r = $con->getResult();
                $this->uid = $r['__id'];
                $this->user = $r['__user'];
                $this->pass = $r['__pass'];
                $this->alias = $r['__alias'];
                return true;
			}else{
				return false;
			}
		}else{
			throw new Exception("Ocurrió un error al obtener el usuario de la BBDD.");
		}
	}

	public function load_from_user(){
		$sql = "select ".$this->uid_fld." as __id, ".$this->pass_fld." as __pass, ".$this->login_fld." as __user, ".$this->alias_fld." as __alias ";
        $sql.= " from ".$this->table." where ".$this->login_fld." = '".mysql_real_escape_string($this->user)."'";
		$con = new con($sql);

		if (!$con->getError()){
			if ($con->getNumRows()==1){
				$r = $con->getResult();
                $this->uid = $r['__id'];
                $this->user = $r['__user'];
                $this->pass = $r['__pass'];
                $this->alias = $r['__alias'];
                return true;
			}else{
				return false;
			}
		}else{
			throw new Exception("Ocurrió un error al obtener el usuario de la BBDD.");
		}
	}

	/**
	 * Función estática que devuelve un array con todos los usuarios del sistema
	 *
	 * @param array $conf -- Array con la configuración de los campos necesarios para hacer login. (Tablas, campos, etc..)
	 * @return array -- Array de objetos (web_login) con los usuarios del sistema.
	 */
	static function get_all_users($conf){
		$users = array();
		$table = $conf['tab'];
		$uid_fld = $conf['uid_fld'];

		$sql = "select ".$uid_fld." as __id";
        $sql.= " from ".$table." where ".$this->deleted_fld." = '0'";
		$con = new con($sql);
		if (!$con->getError()){
			if ($con->getNumRows() > 0){
				while($r = $con->getResult()){
					$user = new web_login($conf);
					$user->uid = $r["__id"];
					if($user->load())
						$users[] = $user;
				}
				return $users;
			}else{
				return false;
			}
		}else{
			throw new Exception("Ocurrió un error al buscar todos los usuarios del sistema.");
		}
	}

	/**
	 * Function to change the password. Ciphers the pass and inserts it into the database
	 *
	 * @param string $new_pass -- New pass to be inserted on the database.
	 * @return bool -- true if everything went ok, false if anything happened
	 */
	function change_pass($new_pass){

		$coded_pass = i::cifrar($new_pass);

        $sql = "update {$this->table}";
        $sql .= " set {$this->pass_fld} = '{$coded_pass}'";
        $sql .= " where {$this->uid_fld} = '{$this->uid}'";
		$con = new con($sql);

		if (!$con->getError()){
			return true;
		}else{
			return false;
		}
	}

}

?>