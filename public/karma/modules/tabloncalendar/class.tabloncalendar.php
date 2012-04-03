<?php
/**
 * Fichero principal de la clase tablon,
 *
 *
 * @author Eider Bilbao <eider@irontec.com>
 * @version 1.0
 * @package karma
 */

class tabloncalendar extends tablon {
	public $fldDatePrinc = null;
	public $aFldDatePrinc = array();
	public $aPltPrinc = array();
	public $aPlantillaPrincipal = array();
	
	/**
	 * método constructor para tablón.
	 *
	 * @param array $conf datos provenientes del fichero de configuración
	 */
	function __construct(&$conf) {
		parent::__construct($conf);
		foreach ($this->conf as $sec => $cont) {
			if(isset($this->conf[$sec]['fldDatePrinc'])){
				$this->fldDatePrinc = $this->conf[$sec]['fldDatePrinc'];
			}
			if(isset($this->conf[$sec]['plt_mult'])){
				$this->aPltPrinc = explode("|",$this->conf[$sec]['plt_mult']);
				if(isset($this->conf[$sec]['fldDatePrinc_mult'])){
					$this->aFldDatePrinc = explode("|",$this->conf[$sec]['fldDatePrinc_mult']);
				}
			}
		}
		new con('select 1');
		$this->aJs[] = "../modules/tabloncalendar/scripts/jquery.livequery.js";
		$this->aJs[] = "../modules/tabloncalendar/scripts/jquery.contextmenu.r2.js";
		$this->aJs[] = "../modules/tabloncalendar/scripts/jquery-ui-personalized-1.5.3.js";
		$this->aJs[] = "../modules/tabloncalendar/scripts/tabloncalendar.js";
		$this->aCss = array(
			"../modules/tabloncalendar/css/tabloncalendar.css",
			"../modules/tabloncalendar/css/extra_impromptu.css",
			"../modules/tabloncalendar/css/ui.all.css"
		);
	}
	

	protected function _getAPlt($sec, $_plt) {
		if (!file_exists($this->rutaPlantillas.$_plt)) return false;
		return $this->rutaPlantillas.$_plt;
	}
	
	protected function getAPlt($_plt) {
		return $this->_getAPlt($this->currentSection,$_plt);
	}
	
	public function drawContextMenu(){
		$aEntidad = array();
		$aGenero = array();
		$extraHtmlMenu = '<input type="hidden" name="morethanonefield" id="morethanonefield" value="false" />';
		$noencontrada = false;
		if(sizeof($this->aPltPrinc)>0){
			foreach($this->aPltPrinc as $idP=>$plt){
				if (!$plantilla = $this->getAPlt($plt)) {
					$noencontrada = true;
					continue;
				}
				$this->aPlantillaPrincipal[$idP] = new tablon_plantilla($plantilla);
				$aPlts[] = $plt."::".$this->aFldDatePrinc[$idP];
				$aEntidad[] = $this->aPlantillaPrincipal[$idP]->getEntidad();
				$aGenero[] = $this->aPlantillaPrincipal[$idP]->getGenero();
			}
		}else{
			$plt = $this->conf[$this->currentSection]['plt'];
			if (($plantilla = $this->getPlt())!==false) {
				$this->aPlantillaPrincipal[0] = new tablon_plantilla($plantilla);
				$aPlts[0] = $plt."::".$this->fldDatePrinc;
				$aEntidad[0] = $this->aPlantillaPrincipal[0]->getEntidad();
				$aGenero[0] = $this->aPlantillaPrincipal[0]->getGenero();
				$numFieldsPlt = $this->aPlantillaPrincipal[0]->getNumFields();
				if($numFieldsPlt>1){
					$extraHtmlMenu = '<input type="hidden" name="morethanonefield" id="morethanonefield" value="true" />';
				}
			}else{
				$noencontrada = true;
			}
		}
		$submenus = array();
		if(!$noencontrada){
			if(sizeof($aEntidad)==1){
				$textos['ver'] = '<li id="ver_eventos_0" class="'.$aPlts[0].'::'.$aEntidad[0].'"><img src="./icons/page_find.png" />Ver '.$aEntidad[0].'s';
				$textos['nuevo'] = '<li id="nuevo_evento_0" class="'.$aPlts[0].'::'.$aEntidad[0].'"><img src="./icons/page_find.png" />Añadir '.$aEntidad[0];
				$textos['vaciar'] = '<li id="vaciar_dia_0" class="'.$aPlts[0].'::'.$aEntidad[0].'"><img src="./icons/page_delete.png" />Eliminar '.$aEntidad[0];
				
			}elseif(sizeof($aEntidad)>1){
				$textos['ver'] = '<li id="ver_eventos"><img src="./icons/page_find.png" />Ver...';
				$textos['nuevo'] = '<li id="nuevo_evento"><img src="./icons/page_find.png" />Añadir...';
				$textos['vaciar'] = '<li id="vaciar_dia"><img src="./icons/page_delete.png" />Eliminar...';
				$submenus['ver'] = '<ul id="menuniv1ver">';
				$submenus['nuevo'] = '<ul id="menuniv1nuevo">';
				$submenus['vaciar'] = '<ul id="menuniv1vaciar">';
				for($i=0;$i<sizeof($aEntidad);$i++){
					$submenus['ver'] .= '<li id="ver_eventos_'.$i.'" class="'.$aPlts[$i].'::'.$aEntidad[$i].'">Ver '.$aEntidad[$i].'</li>';
					$submenus['nuevo'] .= '<li id="nuevo_evento_'.$i.'" class="'.$aPlts[$i].'::'.$aEntidad[$i].'">Añadir '.$aEntidad[$i].'</li>';
					$submenus['vaciar'] .= '<li id="vaciar_dia_'.$i.'" class="'.$aPlts[$i].'::'.$aEntidad[$i].'">Eliminar '.$aEntidad[$i].'(e)s</li>';
				}
				$submenus['ver'] .= '</ul>';
				$submenus['nuevo'] .= '</ul>';
				$submenus['vaciar'] .= '</ul>';
			}else{
				iError::error("No se puede contruir el menú contextual");
				return false;
			}
			if(isset($this->conf[$this->currentSection]['menuContextOps'])){
				$aMC = explode("|",$this->conf[$this->currentSection]['menuContextOps']);
			}else{
				$aMC = array("all");
			}
			$htmlmenu = '
			<div class="contextMenu" id="myContextMenu">
				<ul id="menuniv0">';
			if(in_array("ver",$aMC) || in_array("all",$aMC))
				$htmlmenu .= $textos['ver'].((empty($submenus))?'':$submenus['ver']).'</li>';
			if(in_array("nuevo",$aMC) || in_array("all",$aMC))
				$htmlmenu .= $textos['nuevo'].((empty($submenus))?'':$submenus['nuevo']).'</li>';
			if(in_array("vaciar",$aMC) || in_array("all",$aMC))
				$htmlmenu .= $textos['vaciar'].((empty($submenus))?'':$submenus['vaciar']).'</li>';
			$htmlmenu .= '</ul>
				<ul id="triggerNew"><li id="triggerNew"><span id="newInline"></span></li></ul>
			</div>'.$extraHtmlMenu;
			return $htmlmenu;
		}else{
			iError::error("Plantilla no encontrada");
			return false;
		}
	}
	
	public function draw() {
		$noencontrada = false;
		$amostrar = "";
		if(sizeof($this->aPltPrinc)>0){
			foreach($this->aPltPrinc as $idP=>$plt){
				if (!$plantilla = $this->getAPlt($plt)) {
					$noencontrada = true;
					continue;
				}
				$this->aPlantillaPrincipal[$idP] = new tablon_plantilla($plantilla);
				$pl = &$this->aPlantillaPrincipal[$idP];
				if ($pl->hasJs())
					while ($this->aJs[] = $pl->getJS()) {}
			
				if ($pl->hasCss())
					while ($this->aCss[] = $pl->getCSS()) {}
					
				$amostrar .= '
				<input type="hidden" name="hddFldDatePrinc_'.$idP.'" value="'.$plt.'::'.$this->aFldDatePrinc[$idP].'" id = "hddFldDatePrinc_'.$idP.'" />
				<input type="hidden" name="hddPlt_'.$idP.'" value="'.$plt.'" id = "hddPlt_'.$idP.'" />
				';
			}
		}else{
			if (($plantilla = $this->getPlt())===false) {
					$noencontrada = true;
			}else{
				$this->aPlantillaPrincipal[0] = new tablon_plantilla($plantilla);
				$pl = &$this->aPlantillaPrincipal[0];
				if ($pl->hasJs())
					while ($this->aJs[] = $pl->getJS()) {}
			
				if ($pl->hasCss())
					while ($this->aCss[] = $pl->getCSS()) {}
				$plt = $this->conf[$this->currentSection]['plt'];
				$amostrar .= '
				<input type="hidden" name="hddFldDatePrinc_0" value="'.$plt.'::'.$this->fldDatePrinc.'" id = "hddFldDatePrinc_0" />
				<input type="hidden" name="hddPlt_0" value="'.$plt.'" id = "hddPlt_0" />
				';
				if(isset($this->selectedConf['newonclick']) && !empty($this->selectedConf['newonclick'])){
					$amostrar .= '<input type="hidden" name="newonclick" id="newonclick" value="true" />';
				}else{
					$amostrar .= '<input type="hidden" name="newonclick" id="newonclick" value="false" />';
				}
			}
		}
		if($noencontrada){
			iError::error("Plantilla no encontrada");
			return false;
		}
		echo $amostrar.$this->drawTitle().'
		<div id="tablonContainer">'.$this->redraw().'</div>
		'.$this->drawContextMenu();
		echo '<form id="hiddenFields">';

		//IVOZ-NG
		if ( isset($this->selectedConf['logingrupocond']) && $_SESSION["__ID"]!='1' && !in_array($_SESSION["__ID"],explode("|",$this->selectedconf['adminarray']))) {
			echo '<input type="hidden" name="'.$this->selectedConf['logingrupocond'].'" value="'.$_SESSION["__GRUPO_VINCULADO"].'" />';
	        } elseif ( isset($this->selectedConf['logingrupocond']) && !in_array($_SESSION["__ID"],explode("|",$this->selectedconf['adminarray']))) {
			echo '<input type="hidden" name="'.$this->selectedConf['logingrupocond'].'" value="'.$this->selectedConf['dfltLoginGrupoForAdmin'].'" />';
        	}

		if (isset($this->selectedConf['idcond']))  {
			echo '<input type="hidden" name="'.$this->selectedConf['idcond'].'" id="'.$this->selectedConf['idcond'].'" value="'.$this->currentValue.'" />';
			//
		}
		echo '</form>';
		echo '<input type="hidden" name="idcond" id="idcond" value="'.$this->selectedConf['idcond'].'" />';
	}
	
	static function redraw(){
		return '
			<div id="tabloncalendar" class = "tabloncalendar">
			</div>
		';
	}
}
?>
