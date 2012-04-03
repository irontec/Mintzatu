<?php
/**
 * Conector a base de datos MySQL
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

$aConectores = array();
$contadorQueries = 0;
$totalMYtime = 0;
define("MAXSQL",1000000);

class con {
	protected $result;
	private $pointer = 0;
	private $error;
	protected $sql;
	private $aResult = array();
	private $rType = MYSQL_ASSOC;
	public $fConf;
	public $link;
	public $error_number;

	function __construct($sql,$link = "default") {
		global $aConectores,$contadorQueries,$totalMYtime;
		$time_start = microtime(true);
		$this->link = $link;

		if ( $ent = getenv('conf_entorno')  ){ //sirve para crear diferentes configuraciones de conexión a bd mediante variables de entorno
			$fConf = parse_ini_file(dirname(__FILE__)."/../../db/access".$ent.".cfg",true);
		}else{
			$fConf = parse_ini_file(dirname(__FILE__)."/../../db/access.cfg",true);
		}


		//$fConf = parse_ini_file("access.cfg",true);
		$fConf = $fConf[$this->link];

		if ((!isset($aConectores[$this->link])) || (mysql_ping($aConectores[$this->link]))) {
			$aConectores[$this->link] = mysql_connect(((isset($fConf['host']))? $fConf['host']:"localhost"),$fConf['user'],$fConf['pass']) or die("Imposible Conectar con el servidor de base de datos");
		}
		mysql_select_db($fConf['db'],$aConectores[$this->link]) or die("Imposible conectar con la base de datos");
		$this->sql = $sql;
		$contadorQueries++;
		if (!mysql_error($aConectores[$this->link])) {
			if (!$this->result = mysql_query($sql,$aConectores[$this->link])) {
				$this->error = mysql_error($aConectores[$this->link])."[".mysql_errno($aConectores[$this->link])."]";
				$this->error_number = mysql_errno($aConectores[$this->link]);
			}
		} else {
			$this->error = mysql_error($aConectores[$this->link])."[".mysql_errno($aConectores[$this->link])."]";
			$this->error_number = mysql_errno($aConectores[$this->link]);
		}
		 $time_end = microtime(true);
                $time = $time_end - $time_start;
			$totalMYtime+=$time;
	}
	static function ping() {
		return @mysql_ping();
	}
	public function reset() {$this->pointer = 0;}
	public function free() { global $aConectores; @mysql_free_result($this->result); }
	public function error() { return (!empty($this->error)); }
	public function getError() { return $this->error; }
	public function getErrorNumber() { return $this->error_number; }
	public function getNumRows() { global $aConectores; return mysql_affected_rows($aConectores[$this->link]); }
	public function setResultType($type) {
		switch($type) {
            case 'ASSOC':
                $this->rType = MYSQL_ASSOC;
                break;
            case 'NUM':
                $this->rType = MYSQL_NUM;
                break;
            case 'BOTH':
                $this->rType = MYSQL_BOTH;
                break;
			case MYSQL_ASSOC:
			case MYSQL_NUM:
			case MYSQL_BOTH:
				$this->rType = $type;
				return true;
			default: return false;
		}
	}

	public function getId() { global $aConectores; return mysql_insert_id($aConectores[$this->link]); }
	public function getLastId() { return $this->getId(); }

	public function dump() {
		echo "<br /><br />".$this->sql;
		echo ($this->error())? $this->getError():"SIN ERRORES";
	}

	public function getResult($mem=false) {
		global $aConectores,$contadorQueries,$totalMYtime;
	         $time_start = microtime(true);

		if (!$row=@mysql_fetch_array($this->result,$this->rType)) {
			$this->free();
    $time_end = microtime(true);
                $time = $time_end - $time_start;
                        $totalMYtime+=$time;

			return false;
		}

		if ($mem) $this->aResult[$this->pointer] = $row;
			$this->pointer++;
    $time_end = microtime(true);
                $time = $time_end - $time_start;
                        $totalMYtime+=$time;

			return $row;
	}

	public function getID_or_create_ifExists($tab,$id,$cmp,$vlr) {
		$con = new con("select ".$id." from ".$tab." where ".$cmp."='".$vlr."'");
 		echo "select ".$id." from ".$tab." where ".$cmp."='".$vlr."'";
		if ($con->error()) return false;
		if ($con->getNumRows()==1) {
			$ret = $con->getResult();
			return $ret[$id];
		}
		$con = new con("insert into ".$tab."(".$cmp.") values('".$vlr."')");
		if (defined("DEBUG")) echo "insert into ".$tab."(".$cmp.") values('".$vlr."')";
		if ($con->error()) return false;
		return $con->getId();
	}
	static function getDetails($link = "default") {
		$fConf = parse_ini_file(dirname(__FILE__)."/../../db/access.cfg",true);
		$fConf = $fConf[$link];
		return $fConf;
	}
	static function foo() {
		$c = new con("select 1");
	}

}


?>
