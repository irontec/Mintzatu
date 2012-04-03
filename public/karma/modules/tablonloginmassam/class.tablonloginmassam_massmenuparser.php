<?php
/**
 * Fichero principal de la clase tablon_edit,
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

class tablonloginmassam_massmenuparser extends tablon {
	protected $aIds = array();
	protected $massPlt;
	protected $plantillaMass;
	protected $relcon;


	function __construct(&$conf) {
		$this->conf = $conf;
		$this->fixOps();
		$this->setCurrentSection();
		$this->massPlt = $this->selectedConf['massplt'];

		$this->l = new k_literal(KarmaRegistry::getInstance()->get('lang'));

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
		return $sql;
	}
	protected function getrelSQL() {
		$pl=$this->plantillaMass;

		$sql=" select ".$this->plantillaPrincipal->getrelID()." as __IDREL, ";
		$sql.= $this->plantillaPrincipal->getID()." as __ID, ";
		$sql.= $pl['::main']['id']." as __IDMASS ";
		$sql.=" from ".$this->plantillaPrincipal->getrelTAB();
		$sql.=" where ".$this->plantillaPrincipal->getID()." = '".$this->currentValue."' ";
		return $sql;
	}

	protected function doParse() {
		$pl=$this->plantillaMass;

		$f = $this->rutaPlantillas.'../'.$pl['::main']['parsefile'];


		if (!file_exists($f)) return array();
		$aFields = parse_ini_file($f,true);
		$plr = array();

		foreach($aFields as $k => $arr) {
			if($k == "main") continue;
			if (!isset($arr[$pl['::main']['parseid']])) continue;

			$p = array();

			$p['__ID'] = $arr[$pl['::main']['parseid']];



			foreach ($pl as $cam=>$v){
				if($cam == "::main") continue;
				if($cam == "menu") {
					$p[$cam] = $k;
					continue;
				}
				$p[$cam] = isset($arr[$v['campo']]) ? $arr[$v['campo']]: false;

			}
			$plr[] = $p;
		}
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

	protected function doSQL() {
		$this->conPrinc = new con($this->getSQL());
		return !$this->conPrinc->error();
	}

	protected function drawTableContents() {
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