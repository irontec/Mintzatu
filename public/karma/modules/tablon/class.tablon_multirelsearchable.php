<?php
/**
 * Fichero principal de la clase tablon_edit,
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

class tablon_multirelsearchable extends tablon {

	protected $plantillaTarget = false;

	function __construct(&$conf) {
		$this->l = new k_literal(KarmaRegistry::getInstance()->get('lang'));
		$this->conf = $conf;
		$this->fixOps();
		$this->setCurrentSection();
		$this->aJs[] = "../modules/tablon/scripts/tablon_multisearchable.js";
	}


	protected function _getMultiPlt($sec,$p) {
		if (!file_exists($this->rutaPlantillas.$this->conf[$sec][$p])) return false;
		return $this->rutaPlantillas.$this->conf[$sec][$p];
	}

	protected function getPltSearchable() {
		return $this->_getMultiPlt($this->currentSection,'searchable_plt');
	}

	protected function getPltTarget() {
		return $this->_getMultiPlt($this->currentSection,'target_plt');
	}


	protected function getCustomSelectFields() {
		if (!$this->plantillaTarget) return array();
		return array($this->plantillaTarget->getTab() . "." . $this->plantillaTarget->getId() .' as  __IDMULRELSEARCH' );
	}

	protected function showOnlySelected() {
		$show = false;
		if (isset($_POST['mrs_only_selected'])) {
			if ($_POST['mrs_only_selected']=="1") $show = true;
			else $show = false;
		} else {
			if (isset($_SESSION['multisearch'][i::clean($this->currentSection)])) $show = $_SESSION['multisearch'][i::clean($this->currentSection)];
			else $show = isset($this->conf[$this->currentSection]['only_selected']);
		}
		$_SESSION['multisearch'][i::clean($this->currentSection)] = $show;
		return $show;

	}


	protected function getCustomConds() {
		if  ($this->showOnlySelected()) {
			return array($this->plantillaTarget->getTab() . "." . $this->plantillaTarget->getId().' is not NULL');
		} else return array();
	}

	public function draw() {

		echo $this->drawHelpDesc();
		echo $this->drawTitle();

		if (!$plantilla = $this->getPltSearchable()) {
			iError::error("Plantilla no encontrada");
			return false;
		}

		echo '<input type="hidden" id="__mrs_target_plt" value="'.$this->conf[$this->currentSection]['target_plt'].'" />';
		echo '<input type="hidden" id="__mrs_target_selected_id" value="'.$this->conf[$this->currentSection]['target_selected_id'].'" />';
		echo '<input type="hidden" id="__mrs_target_searchable_id" value="'.$this->conf[$this->currentSection]['target_searchable_id'].'" />';
		echo '<input type="hidden" id="__mrs_target_selected_value" value="'.$this->currentValue.'" />';
		if (isset($this->conf[$this->currentSection]['extrafield'])) {
		    echo '<input type="hidden" id="__mrs_extrafield" value="'.$this->conf[$this->currentSection]['extrafield'].'" />';
		    echo '<input type="hidden" id="__mrs_extravalue" value="'.$this->conf[$this->currentSection]['extravalue'].'" />';
		}

		$this->plantillaPrincipal = new tablon_plantilla($plantilla);

		$pl = &$this->plantillaPrincipal;

		if ($pl->hasJs())
				while ($this->aJs[] = $pl->getJS()) {}

		if ($pl->hasCss())
				while ($this->aCss[] = $pl->getCSS()) {}

		$this->plantillaTarget = new tablon_plantilla($this->getPltTarget());

		$pl->setALeftTabs(
			$this->plantillaTarget->getTab(),
			$this->plantillaTarget->getTab() . "." .$this->conf[$this->currentSection]['target_selected_id']. '=\'' .$this->currentValue.'\' and '.$this->plantillaTarget->getTab() . "." . $this->conf[$this->currentSection]['target_searchable_id'] . " = " . $pl->getTab() . "." . $pl->getId(),
			'');

		$sql = $this->doSQL();

		if (isset($this->selectedConf['search'])) {
			echo '<p id="__user_filtrado"><img src="./icons/viewmag.png" alt="'.$this->l->l('ut_filtrado').'" />'.$this->l->l('act_filtrado').'</p>';
			$this->drawSearcher();
		}

		echo '<form id="mrs_form_selected" action="'.krm_menu::getURL().$this->getHistoryURL().'" method="post">';
		echo '<input type="checkbox" id="__multisearchable_showselected" '.(($this->showOnlySelected())? ' checked="checked"':'').' />' . $this->l->l('Show only selected rows') . '.';
		echo '<input type="hidden" name="mrs_only_selected" />';
		echo '</form>';


		echo '<table id="tablon" class="tablon"  genero="'.$pl->getGenero().'" entidad="'.$pl->getEntidad().'">';

		echo $pl->drawHead(($this->hasOptions()&&($opts = $this->drawOptions("%id%"))),$this->getNoShownFields(),"multisearchable",krm_menu::getURL().$this->getHistoryURL());

		if (!$sql) {
			$this->drawErrors();
		} else {
			// Paso multiselect al id de la relación (getCustomSelectFields), para que dibuje el checkbox en función del campo devuelto y csv a true (para no mostrar cloneInfo)
			$this->drawTableContents("__IDMULRELSEARCH",true);
		}
		echo '</table>';


		$this->drawPageLimit();

		echo '<ul id="optsTablon">';
		echo '<li class="optsIni">&nbsp;</li>';
		echo '<li class="opts optsEnd">&nbsp;</li>';
		if (sizeof($this->history)>0) {
			echo '<li class="opts backButton">'.$this->drawBackLink().'</li>';
		}
		echo '</ul>';

	}


	static function foo() {}
}


?>
