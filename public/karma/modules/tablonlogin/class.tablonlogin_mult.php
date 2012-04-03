<?php
class tablonlogin_mult extends tablonlogin {
	public $aJs = array();
	
	function __construct(&$conf) {
		parent::__construct($conf);
		new con('select 1');
		//$this->aJs[] = "../modules/tablonlogin/scripts/tablonlogin_mult.js";
	}
	
	protected function getSQL() {
		$sqlLogin = "";
		con::foo();
		if(isset($this->cfg['main']['login_mult'])){
			$aLogins = explode("|",$this->cfg['main']['login_mult']);
			if(is_array($aLogins) && sizeof($aLogins)>0){
				$sqlWhere = " where ";
				$condWhere = "";
				$sqlLogin .= ", case ";
				for($i=0;$i<sizeof($aLogins);$i++){
					if(!empty($aLogins[$i])){
						$sqlLogin .= " when ".$this->pl->getTab().".".$aLogins[$i]." is not null then ".$this->pl->getTab().".".$aLogins[$i]." ";
						if($i>0){
							$condWhere .= " or ";
						}
						$condWhere .= $this->pl->getTab().".".$aLogins[$i]." = '".mysql_real_escape_string($this->user)."' ";
					}
				}
				$sqlLogin .= " ELSE NULL END as __LOGIN ";
				$sqlWhere .= $condWhere; 
			}elseif(!empty($aLogins)){
				$sqlLogin .= ", ".$aLogins." as __LOGIN ";
				$sqlWhere = " where ".$this->pl->getTab().".".$aLogins." = '".mysql_real_escape_string($this->user)."' ";
			}
		}else{
			$sqlLogin .= ", ".$this->cfg['main']['login']." as __LOGIN ";
			$sqlWhere = " where ".$this->pl->getTab().".".$this->cfg['main']['login']." = '".mysql_real_escape_string($this->user)."' ";
		}
		$sql = "select SQL_CALC_FOUND_ROWS ";
		$sql .= $this->pl->getTab().".".$this->pl->getID()." as __ID ";
		$sql .= $sqlLogin;
		$sql .= ", ".$this->cfg['main']['pass']." as __PASS ";
		if (isset($this->cfg['main']['id_grupo_vinculado'])) {
			$sql .= ", ".$this->cfg['main']['id_grupo_vinculado']." as __GRUPO_VINCULADO ";
		}
		$sql .= " from ".$this->pl->getTab(); 
		$sql .= $sqlWhere;
		$sql .= " limit 1";
		return $sql;		
	}
}
?>
