<?php


class tablongallery extends tablon {

	var $tmpGal="";

	function __construct(&$conf) {
		parent::__construct($conf);
		//$this->aJs[] = "../modules/tablon/scripts/date.js";
		$this->aCss[] = "../modules/tablongallery/css/gallery.css";
		$this->aJs[] = "../scripts/jquery/jquery.scrollTo-min.js";
		$this->aJs[] = "../modules/tablongallery/scripts/gallery.js";
	}


	protected function drawTableContents($checkBox = false,$csv = false) {
		$pl = &$this->plantillaPrincipal;
		$par = 0;
		$o="";
		while ($r = $this->conPrinc->getResult()) {

			$o.= '<tr';
			$o.= ($par%2==0)?' class="par"':' class="impar"';
			$par++;
			if (!$csv) $o.= ' id="'.$pl->getBaseFile().'::'.$r['__ID'].'" ';
			$o.= '>';
			if ($checkBox) {
				if (is_string($checkBox)) { // Estoy en multiselect, lo dibujo por cojones
					$o.= '<td index="0" class="multiselect" id="ms::'.$r['__ID'].'"><input type="checkbox"';
					if ($r[$checkBox] != NULL) $o.= ' checked="checked" value="'.$r[$checkBox].'"';
					$o.= '  /></td>';
				} else {
					if (!$csv) { // tablon normal, dibujo si no estoy en modo csv
						$o.= '<td index="0" class="multiselect" id="ms::'.$r['__ID'].'"><input type="checkbox" /></td>';
					}
				}
			}
			$cont = 1;
			for ($i=0;$i<$pl->getNumFields();$i++) {
				if ($this->isNoShown($pl->fields[$i]->getIndex())) continue;
				if ($pl->fields[$i]->getRealType() == "IMG"){
					$this->tmpGal.='<div class="thumb" trval ="'.$pl->getBaseFile().'::'.$r['__ID'].'"  >'.$pl->fields[$i]->getImg($r[$pl->fields[$i]->getAlias()]).'</div>';
				}

				$pl->fields[$i]->setCurrentID($r['__ID']);
				$o.= '<td index="'.($cont).'" ';
				$cont++;
				if (!$csv) $o.= ' id="'.basename($pl->getFile()).'::'.$pl->fields[$i]->getSQLFLD().'::'.$r['__ID'].'" class="';
					$lres = array();
					if(isset($this->selectedConf['translate'])){
							$lres =  $this->translatefield($pl->fields[$i]->getSQLFLD());

					}
				if (($this->isPlaceEditable())  && (!$this->isNoEdit($pl->fields[$i]->getIndex())) && (!$csv)) {

					if ($pl->fields[$i]->getCl()&&(trim($pl->fields[$i]->getCl())!="")) $o.= $pl->fields[$i]->getCl();

					else{
							$o.= 'editable ';
							if (isset($lres['0'])){ $o.= $lres['0']; }
					}
					if ($pl->fields[$i]->isRequired()){ $o.= ' required ';}

					if ($pl->fields[$i]->getOCl()){ $o.= ' '.$pl->fields[$i]->getOCl().' ';}

					$o.= ' "';
					$o.= ' type="' . $pl->fields[$i]->getType().'"  '.((isset($lres['1']))? 'lang="'.$lres['1'].'"':'').' >';
				} else {
					if (isset($lres['0'])){ $o.= $lres['0']; }
					if (!$csv) $o.= '">';
					else $o.= '>';

				}

				if($this->isShowLimit($pl->fields[$i]->getIndex())){
					$o.= text_utils::text_limit($pl->fields[$i]->drawTableValue($r[$pl->fields[$i]->getAlias()]),$this->showLimitFields[$pl->fields[$i]->getIndex()],"...");
				}else{
					$o.= $pl->fields[$i]->drawTableValue($r[$pl->fields[$i]->getAlias()]);
				}
				$o.= '</td>';
				for ($j=0;$j<$pl->fields[$i]->sizeofsubFields;$j++) {
					if (in_array($pl->fields[$i]->subFields[$j]->getIndex(),$this->getNoShownFields())) continue;
					$o.= '<td index="'.$cont.'" ';
					$cont++;
					if (!$csv) $o.= ' id="'.basename($pl->getFile()).'::'.$pl->fields[$i]->subFields[$j]->getSQLFLD().'::'.$r['__ID'].'"';
					$o.= '>';
					$o.= $pl->fields[$i]->subFields[$j]->drawTableValue($r[$pl->fields[$i]->subFields[$j]->getAlias()]).'</td>';
				}
			}
			if (($this->hasOptions()) && (!$csv)) {
				if ($opts = $this->drawOptions($r['__ID'])) $o.= '<td>'.$opts.'</td>';
			}
			$o.= '</tr>';
		}
		if (!$csv) $this->drawTRModel($checkBox,true);
		return $o;
	}

	public function draw() {
		echo $this->drawHelpDesc();
		if (!$plantilla = $this->getPlt()) {
			iError::error("Plantilla no encontrada");
			return false;
		}
		$this->plantillaPrincipal = new tablon_plantilla($plantilla);
		if (isset($this->selectedConf['addplt'])) {
			$pltaux = $this->selectedConf['addplt'];
			$this->plantillaPrincipal->addPlt($this->rutaPlantillas,$pltaux);
		}
		$pl = &$this->plantillaPrincipal;
		if ($pl->hasJs())			while ($this->aJs[] = $pl->getJS()) {}
		if ($pl->hasCss())			while ($this->aCss[] = $pl->getCSS()) {}
		$multiops = ( ($this->selected_have_opts_tablon_edit()) || (isset($this->selectedConf['del'])) || (isset($this->selectedConf['csv'])));
		$csv = ((isset($_GET['CSV'])) && ($_GET['CSV']=="1"));
		$generalops = ( (sizeof($this->history)>0) || (isset($this->selectedConf['new'])));
		$sql = $this->doSQL();
		if (isset($this->selectedConf['search'])) {
			echo '<p id="__user_filtrado"><img src="./icons/viewmag.png" alt="'.$this->l->l('ut_filtrado').'" />'.$this->l->l('act_filtrado').'</p>';
			$this->drawSearcher();
		}
		if (isset($this->selectedConf['upLimit'])) { $this->drawPageLimit(); }
		if (isset($this->selectedConf['translate'])) {		$this->translate();	}
		echo '<div id="tablonContainer">';
		if (isset($this->selectedConf['timezoneselect']) && $this->selectedConf['timezoneselect']=="true"){
			$this->timezones = datemanager::get_timezones();
			$this->drawTimezoneSelect();
		}
		if ($csv) {
				ob_end_clean();
			 	header("Content-type: application/vnd.ms-excel");
		         header("Content-Disposition:  filename=\"".i::clean($this->selectedConf['tit']).".xls\";");
		}


		$o =  '<table'.(($csv)? '>':' id="tablon" class="tablon"  genero="'.$pl->getGenero().'" entidad="'.$pl->getEntidad().'">');
 		if (!$csv) $o.= $this->plantillaPrincipal->drawHead(($this->hasOptions()&&($opts = $this->drawOptions("%id%"))),$this->getNoShownFields(),$multiops,krm_menu::getURL().$this->getHistoryURL());
		if (!$sql) {
			$this->drawErrors();
		} else {

		$o.= 	$this->drawTableContents($multiops,$csv);

		}
		$o.= '</table>';


		echo '<div id="gallery" >'.$this->tmpGal.'</div>';

		echo $o;

		if ($csv) exit();



		$this->drawGeneralOpsTablon($pl);





		if ($multiops) {
			echo '<ul id="optsTablon">';
			echo ' <li><img src="icons/flechita.png" class="flechita" alt="Seleccione multiples opciones para actualización masiva" /></li>';
			if (($ops = $this->selected_have_opts_tablon_edit())!==false) {
				$mOps=array();
				foreach ($ops as $op){
					$url = krm_menu::getURL();
					$url .= $this->getHistoryURL();
					$url .= 'tSec['.$this->currentSection.'::'.$op.']=%id%';
					$class="opts";
					$mas = "";
					if ($this->conf[$op]['class'] == "tablon_edit"){
						$li=  '<li class="opts multiopsh hidden"><a href="'.$url.'" '.$mas.' class="'.$class.'" title="'.$this->conf[$op]['tit'].'">';
						if (isset($this->conf[$op]['img'])) {
							//echo '<img src="./icons/'.$this->conf[$op]['img'].'" alt="'.$this->conf[$op]['tit'].'" />';
							$li.= '<img src="./icons/_edit.png" alt="'.$this->conf[$op]['tit'].'" />';
						}
						$li.= $this->conf[$op]['tit'];
						$li.= '</a></li>';
						$mOps[]=$li;
					}
					if ($this->conf[$op]['class'] == "tablon_multiedit"){
						$class = " tablon_multiedit";
						$mas = 'campo="'.$this->conf[$op]['campo'].'"';
						$mas.= 'plt="'.$this->conf[$op]['plt'].'"';
						$li=  '<li class="opts multiopsh hidden"><a href="'.$url.'" '.$mas.' class="'.$class.'" title="'.$this->conf[$op]['tit'].'">';
						if (isset($this->conf[$op]['img'])) {

							//Modificamos el icono para que el botón de editar siempre sea el mismo. Si queremos hacer que lo coja desde archivo de configuración cambiamos la linea por la que está dentro de este comentario
							//echo '<img src="./icons/'.$this->conf[$op]['img'].'" alt="'.$this->conf[$op]['tit'].'" />';
							$li.= '<img src="./icons/_edit.png" alt="'.$this->conf[$op]['tit'].'" />';
						}
						$li.= $this->conf[$op]['tit'];
						$li.= '</a></li>';
						$mOps[]=$li;
					}
				}
				$size = sizeof($mOps);
				if ($size<=2){

					echo str_replace("hidden","",implode(" ",$mOps));

				}else{
					echo str_replace("hidden","",$mOps[0]);
					echo "<ul id=\"hiddenMultiops\">";
					echo str_replace("","",$mOps[0]);
					for($i=1;$i<$size;$i++){
						echo $mOps[$i];
					}
					echo "</ul>";
					echo '<li class="optsMore" ><a class="optsLink" title="'.$this->l->l('Desplegar opciones de actualización masiva.').'" ><img id="moreopts" src="./icons/window_nofullscreen.png" csrc="./icons/window_nofullscreen.png" osrc="./icons/window_fullscreen.png" alt=""/></a></li>';

				}

			}
			if ((isset($this->selectedConf['csv'])) && ((bool)$this->selectedConf['csv'])) {
				$url = krm_menu::getURL();
				$url .= $this->getHistoryURL();
				echo '<li class="opts">'.
				'<a href="'.$url.'&amp;CSV=1" class="tablon_edit" title="'.$this->l->lstr('csv ',false,$pl->getEntidad()).'">'.
				'<img src="./icons/_download.png" alt="Download" />'.
				$this->l->lstr('csv ',false,$pl->getEntidad()).
				'</a></li>';
			}


			if ($pl->anyFLDemail&&$this->email_system){


				echo '<li class="opts"><a class="optsLink" title="'.$this->l->l('Enviar Email').'" ><img src="./icons/_delete.png" alt="" id="multiSendEmail" />'.$this->l->l('Enviar Email').'</a></li>';
			}
			if ((isset($this->selectedConf['del'])) && ((bool)$this->selectedConf['del'])) {
				echo '<li class="opts"><a class="optsLink" title="'.$this->l->lstr('Eliminar',false,$pl->getEntidad()).'" ><img src="./icons/_delete.png" alt="Eliminar" id="multiDelete"  objetoElim="'.$pl->getEntidad().'" />'.$this->l->lstr('Eliminar',false,$pl->getEntidad()).'</a></li>';
			}
			if(!$generalops) echo '</ul>';
		}


		if ($generalops) {
			if(!$multiops){
				echo '<ul id="optsTablon">';
				echo '<li class="optsIni " >&nbsp;</li>';
			}else{
				echo '<li class="sep " >&nbsp;</li>';

			}
			if (isset($this->selectedConf['new'])) {
				$aNoEdit = $this->getNoEditFields();
				if(!empty($aNoEdit)){
					$param3 = implode(",",$this->getNoEditFields());
				}
				else{
					$param3 = '0';
				}
				$newOpts = explode(',',$this->selectedConf['new']);
				foreach ($newOpts as $newOpt){
					switch($newOpt) {
						case 'inline':
							echo '<li class="opts" id="new::'.basename($pl->getFile()).'::'.$param3.'"><a class="optsLink" title="'.$this->l->lstr('Nuev',$pl->getGenero(),$pl->getEntidad()).'"><img src="./icons/_new.png" alt="'.$this->l->l('Nuevo').'" id="newInline" />'.$this->l->lstr('Nuev',$pl->getGenero(),$pl->getEntidad()).'</a></li>';
						break;
						case 'toline':
							echo '<li class="opts" id="new::'.basename($pl->getFile()).'::'.$param3.'"><a class="optsLink" title="'.$this->l->lstr('Nuev',$pl->getGenero(),$pl->getEntidad()).'"><img src="./icons/_new.png" alt="'.$this->l->l('Nuevo').'" id="newToline" />'.$this->l->lstr('Nuev',$pl->getGenero(),$pl->getEntidad()).'</a></li>';
						break;
						case 'editPlt':
							$opcionEdit = $this->selectedConf['editPlt'];
							$pltEdit = basename($this->conf[$opcionEdit]['plt']);
							echo '<li class="opts" id="new::'.$pltEdit.'::show::'.basename($pl->getFile()).'::'.$param3.'"><a class="optsLink" title="'.$this->l->lstr('Nuev',$pl->getGenero(),$pl->getEntidad()).'"><img src="./icons/_new.png" alt="'.$this->l->l('Nuevo').'" id="newInlineEdit" />'.$this->l->lstr('Nuev',$pl->getGenero(),$pl->getEntidad()).'</a></li>';
						break;
						case 'zip':
							echo '<li class="opts" id="newzip::'.basename($pl->getFile()).'::'.$param3.'"><a class="optsLink" id="newZip" title="'.$this->l->lstr('Nuev',$pl->getGenero(),$pl->getEntidad()." Zip").'"><img src="./icons/_newZip.png" alt="Nuevo"  />'.$this->l->lstr('Nuev',$pl->getGenero(),$pl->getEntidad()." Zip").'</a></li>';
						break;
					}
				}

			}
			echo '<li class="optsEnd">&nbsp;</li>';
			if (sizeof($this->history)>0) {
				echo '<li class="opts backButton">'.$this->drawBackLink().'</li>';
			}


			echo '</ul>';
		}

		$this->drawPageLimit();

		/*
			hidden fields for ajax new.
		*/
		//LANDER
		if (isset($this->selectedConf['onnew'])) {
			echo '<input type="hidden" name="tablononnew" id="tablononnew" value="'.$this->selectedConf['onnew'].'" />';
		}
		echo '<form id="hiddenFields">';
		if (isset($this->selectedConf['idcond']))  {
			echo '<input type="hidden" name="'.$this->selectedConf['idcond'].'" value="'.$this->currentValue.'" />';
		}


		if (isset($this->selectedConf['idprecond']))  {
			echo '<input type="hidden" name="'.$this->selectedConf['idprecond'].'" value="'.$this->lastValue.'" />';
		}

		if ( isset($this->selectedConf['logincond']) && in_array('insert',explode('|',$this->selectedConf['logincondVConf'])))  {
			echo '<input type="hidden" name="'.$this->selectedConf['logincond'].'" value="'.$_SESSION["__ID"].'" />';
		}
               if ( isset($this->selectedConf['logincondOnInsert'])) {
			switch (true) {

				case (isset($this->selectedConf['logincondOnInsertSessionIdxName'])):
		                        echo '<input type="hidden" name="'.$this->selectedConf['logincondOnInsert'].'" value="'.$_SESSION[$this->selectedConf['logincondOnInsertSessionIdxName']].'" />';
				break;
				default:
					// ToDo otras jartadas varias...
				break;
			}
                }


		if (isset($this->selectedConf['pltCond'])){
			$pltCondiciones = explode("|",$this->selectedConf['pltCond']);
			if(sizeof($pltCondiciones)>0){
				for($i=0;$i<sizeof($pltCondiciones);$i++){
					//$aConds[] = ' '.$pl->getTab().'.'.$pltCondiciones[$i];
					/* TODO mirar el igual y el distinto de en las condiciones*/
					$fr = explode('is',$pltCondiciones[$i],2);

					if (isset($fr[1])&&trim($fr[1])=="null"){


					}else{
						if (preg_match("/=/",$pltCondiciones[$i])){
							list($c,$v)=explode('=',$pltCondiciones[$i],2);
							echo '<input type="hidden" name="'.$c.'" value="'.trim($v).'" />';
						}
						if (preg_match("/is/",$pltCondiciones[$i])){
							list($c,$v)=explode('is',$pltCondiciones[$i],2);
							echo '<input type="hidden" name="'.trim($c).'" value="'.trim($v).'" />';
						}
					}
				}
			}
		}
		echo '</form>';

		echo '</div>';
	}

}


?>
