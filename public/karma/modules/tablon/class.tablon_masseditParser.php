<?php
/**
 * Fichero principal de la clase tablon_edit,
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

class tablon_masseditParser extends tablon {
	private $aIds = array();
	private $massPlt;
	private $plantillaMass;
	private $relcon;

	function __construct(&$conf) {
		$this->l = new k_literal(KarmaRegistry::getInstance()->get('lang'));
		$this->conf = $conf;
		$this->fixOps();
		$this->setCurrentSection();
		$this->massPlt = $this->selectedConf['massplt'];



		$this->plantillaMass = $this->parsemassplt($this->rutaPlantillas.$this->massPlt);
		//var_dump($this->plantillaMass);
		$this->aJs[] = "../modules/tablon/scripts/tablon_mass.js";
	}

	protected function parsemassplt($f,$noFields= false) {
		if (!file_exists($f)) return false;
		$aFields = parse_ini_file($f,true);
		return $aFields;
	}

	protected function getmassSQL() {
		$pl=$this->plantillaMass;
		$sql=" select ".$pl['::main']['id']." as __ID,";
		$asql=array();
		foreach ($pl as $id=>$v) {
			if ($id=="::main") continue;
			$asql[]=  $id." as '".$v['alias']."'";
		}
		$sql.= implode(',',$asql);
		$sql.= " from ".$pl['::main']['tab'];
		if(isset($pl['::main']['lefttabs'])){
			$leftTabs = explode("|",$pl['::main']['lefttabs']);
			$leftConds = explode("|",$pl['::main']['leftconds']);
			$leftWhere = explode("|",$pl['::main']['leftwhere']);
			if(sizeof($leftTabs)>0 && sizeof($leftConds)>0 && sizeof($leftTabs)==sizeof($leftConds)){
				for($i=0;$i<sizeof($leftTabs);$i++){
					if($i!=0){
						$sql.=" ";
					}
					$sql .= " left join ".$leftTabs[$i]." on(".$leftConds[$i].") ";
				}
			}
		}
		$sqlCond = "";
		if (isset($pl['::main']['deleted'])) {
			$sqlCond .= " where ".$pl['::main']['deleted']."='0'";

		}
		if($this->plantillaPrincipal->getrelExcludeCurrentID()!==false){
			if($sqlCond != ""){
				$sqlCond .= " and ".$pl['::main']['id']."!='".$this->currentValue."'";
			}else{
				$sqlCond .= " where ".$pl['::main']['id']."!='".$this->currentValue."'";
			}
		}
		if(isset($leftWhere) && is_array($leftWhere)){
			if($sqlCond != ""){
				$sqlCond .= ' and '.implode( "and ",$leftWhere)." ";
			}else{
				$sqlCond .= ' where '.implode(" and ",$leftWhere)." ";
			}
		}
		$sql .= $sqlCond;

		if(isset($pl['::main']['order']) && !empty($pl['::main']['order'])){
			$sql .= ' order by '.$pl['::main']['order'].' ';
		}

		return $sql;
	}
	protected function getrelSQL() {
		$pl=$this->plantillaMass;

		$sql=" select ".$this->plantillaPrincipal->getrelID()." as __IDREL, ";
		if($this->plantillaPrincipal->getrelIDmain()!==false){
			$sql.= $this->plantillaPrincipal->getrelIDmain()." as __ID, ";
		}else{
			$sql.= $this->plantillaPrincipal->getID()." as __ID, ";
		}
		if($this->plantillaPrincipal->getrelIDmass()!==false){
			$sql.= $this->plantillaPrincipal->getrelIDmass()." as __IDMASS ";
		}else{
			$sql.= $pl['::main']['id']." as __IDMASS ";
		}
		$sql.=" from ".$this->plantillaPrincipal->getrelTAB();
		if($this->plantillaPrincipal->getrelIDmain()!==false){
			$sql.=" where ".$this->plantillaPrincipal->getrelIDmain()." = '".$this->currentValue."' ";
		}else{
			$sql.=" where ".$this->plantillaPrincipal->getID()." = '".$this->currentValue."' ";
		}
		if (isset($_GET['DEBUG'])) iError::warn("<textarea>".$sql."</textarea>");
		return $sql;
	}

	protected function doParse() {
		$pl=$this->plantillaMass;
		var_dump($pl);
		$f = $this->rutaPlantillas.'../'.$pl['::main']['parsefile'];
		echo "<br /><br />";
		if (!file_exists($f)) return array();
		$aFields = parse_ini_file($f,true);
		$plr = array();

		var_dump($aFields,$pl['::main']['parseid']);
						echo "<br /><br />";
						echo "<br /><br />";

		foreach($aFields as $k => $arr) {
			if($k == "main") continue;
			if (!isset($arr[$pl['::main']['parseid']])) continue;

			$p = array();

			$p['__ID'] = $arr[$pl['::main']['parseid']];

			foreach ($pl as $cam=>$v){
				if($cam == "::main") continue;
				var_dump('===>',$cam);
				echo "<br /><br />";
				$p[$cam] =  $k;//((isset($arr[$v['alias']]))? $k[$v['alias']]:"");

			}


			echo "<br /><br />";
			var_dump($k,$p);
		//	var_dump($k,$arr,"<br /><br />",$p);


			//exit();
			$plr[] = $p;
		}
		/*exit();
				echo "<br /><br />";
				echo "<br /><br />";
						echo "<br /><br />";
		//$plr[0]=array('__ID'=>'asdg');
		var_dump($p);*/
		/*foreach($aFields as $key => $v) {
			$p = array();
			if ($key=="main") continue;
			if (isset($key[$pl['::main']['parseid']])) {

				$p['__ID'] = $v;

			}

			$plr[]=$p;
		}
		*/

		/*$plr[0] = array('__ID'=>'noseque.cfg',
						'menu'=>'lalalalal',
						'desripcion'=>'lololololo');
		*/
		return $plr;
	}

	protected function dorelSQL() {

		$this->relcon = new con($this->getrelSQL());
		return !$this->relcon->error();

	}
	protected function drawrelErrors() {
		iError::error($this->relcon->getError());
		switch($this->relcon->getErrorNumber()) {
			case 1146:
				iError::warn("<textarea rows='20' cols='80' class='warn'>".$this->getCreateRelSQL()."</textarea>");
			break;
		}
	}
	protected function getCreateRelSQL() {
		$pl=$this->plantillaMass;
		$cSQL="create table ".$this->plantillaPrincipal->getrelTAB()."(\n".$this->plantillaPrincipal->getrelID()." mediumint(8) unsigned not null auto_increment";
		$cSQL.= ",\n".$this->plantillaPrincipal->getID()." mediumint(8) unsigned not null ";
		$cSQL.=	",\n index(".$this->plantillaPrincipal->getID().") ";
		$cSQL.=	",\n foreign key(".$this->plantillaPrincipal->getID().") references ".$this->plantillaPrincipal->getTab()."(".$this->plantillaPrincipal->getID().") on delete cascade on update cascade\n";
		$cSQL.= ",\n".$pl['::main']['id']." mediumint(8) unsigned not null ";
		$cSQL.=	",\n index(".$pl['::main']['id'].") ";
		$cSQL.=	",\n foreign key(".$pl['::main']['id'].") references ".$pl['::main']['tab']."(".$pl['::main']['id'].") on delete cascade on update cascade\n";
		$cSQL.=",\nprimary key(".$this->plantillaPrincipal->getrelID().")\n) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		return $cSQL;

	}
	protected function buildConds() {
		$aConds = array();
		$pl = &$this->plantillaPrincipal;
		if ($del = $pl->getDeletedFLD()) {
			$aConds[] = ' '.$del.'=\'0\'';
		}

		$aIds = explode(",",$this->currentValue);

		$aMiniConds = array();
		for ($i=0;$i<sizeof($aIds);$i++) {
			$this->aIds[] = $aIds[$i];
			$aMiniConds[] = ' '.$pl->getTab().'.'.$this->plantillaPrincipal->getID().'=\''.$aIds[$i].'\'';
		}
		$aConds[] = '('.implode(" or ",$aMiniConds).')';
		if (sizeof($aConds)>0) return " where ".implode(' and ',$aConds);
		else return "";
	}

	private function doSQL() {
		$this->conPrinc = new con($this->getSQL());
		return !$this->conPrinc->error();
	}

	private function drawTableContents() {
		$pl = &$this->plantillaPrincipal;
		$flagEntrada = true;
		$rAux = false;
		$this->getrelSQL();
		//echo $this->getmassSQL();
		//$plr = new con($this->getmassSQL());

		/*if($plr->getNumRows()<=0){
			iError::warn("La tabla a relacionar no tiene contenidos.");
			return false;
		}*/
		$plr = $this->doParse();

		$aRel=array();
		while ($r = $this->relcon->getResult()) {

			$aRel[$r['__IDMASS']]=$r['__IDREL'];
		}
		//var_dump($plr->getResult());
		while ($r = $this->conPrinc->getResult()) {
//			var_dump($pl->getPreForm(),$r);
			$a = $pl->getPreForm();
			foreach($r as $al=>$vlr)  $a = str_replace($al,$vlr,$a);
				echo '<tr>';
				echo '<th class="cabecera">'.$pl->getPreForm().'</th>';
				$cont=1;
				/*echo '<td class="tablon_edit" >';
				echo 'Relacionar';
				echo '</td>';*/
				echo '<th class="multiselect" id="mlMASTER"><input type="checkbox" id="chk_msMASTERMASS"/></th>';
				foreach ($this->plantillaMass as $id=>$v) {
					if ($id=="::main") continue;
					echo '<th class="tablon_edit" >';
					echo $v['alias'];
					echo '</th>';
					$cont++;
				}
				echo '</ tr>';

			//while($r=$plr->getResult()){
			foreach($plr as $r){
				echo '<tr>';
				echo '<td class="cabecera">'.(($flagEntrada)? $a:"").'</td>';
				$flagEntrada=false;

				echo '<td class="multiselect" newid="0" id="'.basename($pl->getFile()).'::'.$this->massPlt.'::'.$r['__ID'].((isset($aRel[$r['__ID']]))? '::'.$aRel[$r['__ID']]:'::nulo').'::'.$this->currentValue.'"><input type="checkbox"';
				echo ((isset($aRel[$r['__ID']]))? 'checked="checked"':'');
				echo ' /></td>';

				foreach ($r as $id=>$v){
					if ($id=="__ID") continue;
					echo '<td class="tablon_edit" >';
					echo $v;
					echo '</td>';
				}
				echo '</ tr>';
			}

			$rAux = $r;
		}
		/*echo '<tr ><td class="tablon_edit" colspan="'.($cont+1).'">
					<span id="autosave"><input type="checkbox" id="autosaveButton">Auto-salvado</span>
					<span id="saveAllButton"><img src="./icons/save_all.png">Salvar Todos</span>
					</td></tr>';*/

		if ($rAux!==false) echo '<tr id="'.basename($pl->getFile()).'::'.$rAux['__ID'].'" >';
		else echo '<tr>';
		echo '<td class="tablon_edit" colspan="'.($cont+1).'">';

		//if (sizeof($this->aIds)==1) echo '<span><img class="'.(($rAux!==false)? 'deleteRow':'').'" src="./icons/eraser.png"><span>Eliminar</span></span>';
		echo $this->drawBackLink().'</td></tr>';

	}
	public function getNoShownFields() {
		if ($this->noShownFields===false) {
			if (isset($this->selectedConf['noshow'])) $this->noShowFields = explode(",",$this->selectedConf['noshow']);
			else $this->noShowFields = array();
		}
		return $this->noShowFields;

	}

	public function draw() {

		echo $this->drawTitle();
		if (!$plantilla = $this->getPlt()) {
			iError::error("Plantilla no encontrada");
			return false;
		}

		$this->plantillaPrincipal = new tablon_plantilla($plantilla);


		$pl = &$this->plantillaPrincipal;
		echo '<div id="tablonContainer">';
		echo '<table id="tablon_edit" class="tablon" genero="'.$pl->getGenero().'" entidad="'.$pl->getEntidad().'">';

		//echo $this->plantillaPrincipal->drawHead($this->hasOptions(),$this->getNoShownFields());

		if (!$this->doSQL()) {
			$this->drawErrors();
		} else {
			if (!$this->dorelSQL()) 	$this->drawrelErrors();

			$this->drawTableContents();
		}
		echo '</table></div>';

	}

	public function hola() { echo "hola";}



	static function foo() {}
}


?>
