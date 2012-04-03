<?php
/*
 ======================================================================
 lastRSS 0.9.1

 Simple yet powerfull PHP class to parse RSS files.

 by Vojtech Semecky, webmaster @ webdot . cz

 Latest version, features, manual and examples:
 	http://lastrss.webdot.cz/

 ----------------------------------------------------------------------
 LICENSE

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License (GPL)
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.

 To read the license please visit http://www.gnu.org/copyleft/gpl.html
 ======================================================================
*/

/**
* lastRSS
* Simple yet powerfull PHP class to parse RSS files.
*/
class lastRSS {
	// -------------------------------------------------------------------
	// Public properties
	// -------------------------------------------------------------------
	var $default_cp = 'UTF-8';
	var $CDATA = 'nochange';
	var $cp = '';
	var $items_limit = 0;
	var $stripHTML = False;
	var $date_format = '';

	var $encoding;

	// -------------------------------------------------------------------
	// Private variables
	// -------------------------------------------------------------------
	var $channeltags = array ('title', 'link', 'description', 'language', 'copyright', 'managingEditor', 'webMaster', 'lastBuildDate', 'rating', 'docs');
	var $itemtags = array('title', 'link', 'description', 'author', 'category', 'comments', 'enclosure', 'guid', 'pubDate', 'source');
	var $imagetags = array('title', 'url', 'link', 'width', 'height');
	var $textinputtags = array('title', 'description', 'name', 'link');

	// -------------------------------------------------------------------
	// Parse RSS file and returns associative array.
	// -------------------------------------------------------------------
	function Get ($rss_url) {
		// If CACHE ENABLED
		if ($this->cache_dir != '') {
			$cache_file = $this->cache_dir . '/rsscache_' . md5($rss_url);
			if (file_exists($cache_file) && $tm = @filemtime($cache_file)){


			}else{

				$tm = 0;
			}
			$timedif = @(time() - $tm);
			if ($timedif < $this->cache_time) {
				// cached file is fresh enough, return cached array
				$result = unserialize(join('', file($cache_file)));
				// set 'cached' to 1 only if cached file is correct
				if ($result) $result['cached'] = 1;
			} else {
				// cached file is too old, create new
				$result = $this->Parse($rss_url);
				$serialized = serialize($result);
				if ($f = @fopen($cache_file, 'w')) {
					fwrite ($f, $serialized, strlen($serialized));
					fclose($f);
					chmod($cache_file,0777);
				}
				if ($result) $result['cached'] = 0;
			}
		}
		// If CACHE DISABLED >> load and parse the file directly
		else {
			$result = $this->Parse($rss_url);
			if ($result) $result['cached'] = 0;
		}

			$this->encoding = $result['encoding'];
		// return result
		return $result;
	}

	// -------------------------------------------------------------------
	// Modification of preg_match(); return trimed field with index 1
	// from 'classic' preg_match() array output
	// -------------------------------------------------------------------
	function my_preg_match ($pattern, $subject) {
		// start regullar expression
		preg_match($pattern, $subject, $out);

		// if there is some result... process it and return it
		if(isset($out[1])) {
			// Process CDATA (if present)
			if ($this->CDATA == 'content') { // Get CDATA content (without CDATA tag)
				$out[1] = strtr($out[1], array('<![CDATA['=>'', ']]>'=>''));
			} elseif ($this->CDATA == 'strip') { // Strip CDATA
				$out[1] = strtr($out[1], array('<![CDATA['=>'', ']]>'=>''));
			}

			// If code page is set convert character encoding to required
			if ($this->cp != '')
				//$out[1] = $this->MyConvertEncoding($this->rsscp, $this->cp, $out[1]);
				$out[1] = iconv($this->rsscp, $this->cp.'//TRANSLIT', $out[1]);
			// Return result
			return trim($out[1]);
		} else {
		// if there is NO result, return empty string
			return '';
		}
	}

	// -------------------------------------------------------------------
	// Replace HTML entities &something; by real characters
	// -------------------------------------------------------------------
	function unhtmlentities ($string) {
		// Get HTML entities table
		$trans_tbl = get_html_translation_table (HTML_ENTITIES, ENT_QUOTES);
		// Flip keys<==>values
		$trans_tbl = array_flip ($trans_tbl);
		// Add support for &apos; entity (missing in HTML_ENTITIES)
		$trans_tbl += array('&apos;' => "'");
		// Replace entities by values
		return strtr ($string, $trans_tbl);
	}

	// -------------------------------------------------------------------
	// Parse() is private method used by Get() to load and parse RSS file.
	// Don't use Parse() in your scripts - use Get($rss_file) instead.
	// -------------------------------------------------------------------
	function Parse ($rss_url) {
		$aMeses = array();
		for($i=1;$i<=12;$i++) $aMeses[date('M',mktime(1, 1, 1, $i, 10, 1997))] = $i;
		// Open and load RSS file
		if ($f = @fopen($rss_url, 'r')) {
			$rss_content = '';
			while (!feof($f)) {
				$rss_content .= fgets($f, 4096);
			}
			fclose($f);

			// Parse document encoding
			$result['encoding'] = $this->my_preg_match("'encoding=[\'\"](.*?)[\'\"]'si", $rss_content);



			// if document codepage is specified, use it
			if ($result['encoding'] != '')
				{ $this->rsscp = $result['encoding']; } // This is used in my_preg_match()
			// otherwise use the default codepage
			else
				{ $this->rsscp = $this->default_cp; } // This is used in my_preg_match()

			// Parse CHANNEL info
			preg_match("'<channel.*?>(.*?)</channel>'si", $rss_content, $out_channel);
			foreach($this->channeltags as $channeltag)
			{
				$o = (isset($out_channel[1]))? $out_channel[1]:false;
				$temp = $this->my_preg_match("'<$channeltag.*?>(.*?)</$channeltag>'si", $o);
				if ($temp != '') $result[$channeltag] = $temp; // Set only if not empty
			}
			// If date_format is specified and lastBuildDate is valid
			$lbd = (isset($result['lastBuildDate']))? $result['lastBuildDate']:false;
			if ($this->date_format != '' && ($timestamp = strtotime($lbd)) !==-1) {
						// convert lastBuildDate to specified date format
						$result['lastBuildDate'] = date($this->date_format, $timestamp);
			}

			// Parse TEXTINPUT info
			preg_match("'<textinput(|[^>]*[^/])>(.*?)</textinput>'si", $rss_content, $out_textinfo);
				// This a little strange regexp means:
				// Look for tag <textinput> with or without any attributes, but skip truncated version <textinput /> (it's not beggining tag)
			if (isset($out_textinfo[2])) {
				foreach($this->textinputtags as $textinputtag) {
					$temp = $this->my_preg_match("'<$textinputtag.*?>(.*?)</$textinputtag>'si", $out_textinfo[2]);
					if ($temp != '') $result['textinput_'.$textinputtag] = $temp; // Set only if not empty
				}
			}
			// Parse IMAGE info
			preg_match("'<image.*?>(.*?)</image>'si", $rss_content, $out_imageinfo);
			if (isset($out_imageinfo[1])) {
				foreach($this->imagetags as $imagetag) {
					$temp = $this->my_preg_match("'<$imagetag.*?>(.*?)</$imagetag>'si", $out_imageinfo[1]);
					if ($temp != '') $result['image_'.$imagetag] = $temp; // Set only if not empty
				}
			}
			// Parse ITEMS
			preg_match_all("'<item(| .*?)>(.*?)</item>'si", $rss_content, $items);
			$rss_items = $items[2];
			$i = 0;
			$result['items'] = array(); // create array even if there are no items
			foreach($rss_items as $rss_item) {
				// If number of items is lower then limit: Parse one item
				if ($i < $this->items_limit || $this->items_limit == 0) {
					foreach($this->itemtags as $itemtag) {
						$temp = $this->my_preg_match("'<$itemtag.*?>(.*?)</$itemtag>'si", $rss_item);
						if ($temp != '') $result['items'][$i][$itemtag] = $temp; // Set only if not empty
					}
					// Strip HTML tags and other bullshit from DESCRIPTION
					if ($this->stripHTML && $result['items'][$i]['description'])
						$result['items'][$i]['description'] = strip_tags($this->unhtmlentities(strip_tags($result['items'][$i]['description'])));
					// Strip HTML tags and other bullshit from TITLE
					if ($this->stripHTML && $result['items'][$i]['title'])
						$result['items'][$i]['title'] = mysql_real_escape_string(strip_tags($this->unhtmlentities(strip_tags($result['items'][$i]['title']))));
					// If date_format is specified and pubDate is valid



					if ( ($this->date_format != '' ) && (isset($result['items'][$i]['pubDate'])) ) {
						// convert pubDate to specified date format
						//$result['items'][$i]['pubDate'] = date($this->date_format, $timestamp);
						$dat = $result['items'][$i]['pubDate'];
						preg_match('/.*([0-9]{2}).([0-9]{2}|[^0-9]{3}).([0-9]{4}).*([0-9]{2}).([0-9]{2}).([0-9]{2}).*/', $dat, $coincidencias);

	
						$aKeys  =array_keys($aMeses);
						if (in_array($coincidencias[2],$aKeys)){
							$coincidencias[2] = $aMeses[$coincidencias[2]];
						}
		
	
						$timestamp = mktime($coincidencias[4],$coincidencias[5],$coincidencias[6],$coincidencias[2], $coincidencias[1],$coincidencias[3]);
						$result['items'][$i]['pubDate'] = date($this->date_format, $timestamp);

					}else{
						if ($this->date_format == '') $this->date_format = "Y/m/d";
						$result['items'][$i]['pubDate'] = date($this->date_format);
					}
					// Item counter
					$i++;
				}
			}

			$result['items_count'] = $i;

			return $result;
		}
		else // Error in opening return False
		{
			return False;
		}
	}
}


class tablonfeeds extends tablon {
	var $feedUrl;

	var $l;


	function __construct(&$conf) {
		$this->l = new k_literal(KarmaRegistry::getInstance()->get('lang'));
		$this->rutaPlantillas = "".dirname(__FILE__)."/../../../configuracion/tablon/";
		$this->conf = $conf;
		$this->fixOps();
		$this->setCurrentSection();

		$this->aJs[] = "../modules/tablon/scripts/date.js";
		$this->aJs[] = "../modules/tablon/scripts/jquery.autocomplete.js";
		//$this->aJs[] = "../modules/tablon/scripts/jquery.datePicker.js";
		$this->aJs[] = "../modules/tablon/scripts/tablon_dateformat.js";
		$this->aJs[] = "../modules/tablon/scripts/jqueryMultiSelect.js";
		$this->aCss[] = "../modules/tablon/css/jqueryMultiSelect.css";


		if (isset($this->conf[$this->currentSection]['oJs'])){
			$this->aJs[] = $this->conf[$this->currentSection]['oJs'];
		}
		//jqueryMultiSelect
		//$this->aCss[] = "../modules/tablon/css/farbtastic.css";
		//$this->aJs[] = "../modules/tablon/scripts/farbtastic.js";
	}

	static function foo(){ return true; }




	public function loadFeed($pl){

		$feed = $this->conf[$this->currentSection]['feed'];


		$condiciones = $this->buildConds();
		$condiciones = $condiciones[0];
		$sql = " select ".$feed." as feedurl from ".$pl->getTab() ;
		$sql = $sql." ".$condiciones;
		$c = new con($sql);
		$r = $c->getResult();
		$this->feedUrl = $r['feedurl'];
	}

	function swencoding($str,$e){
		switch($e){
			case "ISO-8859-1":
			case "iso-8859-1":

					return utf8_encode($str);

			break;
			default: return $str; break;

		}
	}
	public function drawFeeds(){
		$url_flux_rss = $this->feedUrl;

		$limite       = 25; // nombre d'actus à afficher

		// on crée un objet lastRSS
		$rss = new lastRSS;

		// options lastRSS

		$rss->cache_dir   = dirname(__FILE__).'/../../../cache/rssCache'; // dossier pour le cache
		if (is_dir($rss->cache_dir)===false){
		    mkdir($rss->cache_dir, 0777, true);
		}
		$rss->cache_time  = 10;      // fréquence de mise à jour du cache (en secondes)
		$rss->date_format = 'Y/m/d';     // format de la date (voir fonction date() pour syntaxe)
		$rss->CDATA       = 'content'; // on retire les tags CDATA en conservant leur contenu

		// lecture du flux
		/*if ($rs = $rss->get($url_flux_rss))
		{
		  for($i=0;$i<$limite;$i++)
		  {

		  	var_dump($rs['items'][$i]);


		  	//break;
		    // affichage de chaque actu
		    echo '<strong>'.$rs['items'][$i]['pubDate'].'</strong> &middot; <a href="'.$rs['items'][$i]['link'].'">'.$rs['items'][$i]['title'].'</a><br />';
		  }
		}
		else
		{
		  die ('Flux RSS non trouvé');
		}*/

		if ($rs = $rss->get($url_flux_rss)){


			//var_dump($rs);
				//exit();

			$l = ($limite >sizeof($rs))? sizeof($rs):$limite;
			for($i=0;$i<$l;$i++){
				if (!isset($rs['items'][$i]['pubDate']) || $rs['items'][$i]['pubDate']=="") continue;

				//$id = i::clean(trim(html_entity_decode($this->swencoding($rs['items'][$i]['title'],$rss->encoding))));
				$id = i::clean(trim(html_entity_decode($this->swencoding($rs['items'][$i]['title'],$rss->encoding)))).'_'.i::clean($rs['items'][$i]['pubDate']);
				echo '<tr>';
				echo '<td class="vals_v_'.$id.'" id="pubdate_v_'.$id.'">'.$rs['items'][$i]['pubDate'].'</td>';
				echo '<td class="vals_v_'.$id.'" id="title_v_'.$id.'"> ';
				echo '<a  class="vals_v_'.$id.'" id="link_v_'.$i.'" href="'.$rs['items'][$i]['link'].'" >';
                                echo mysql_real_escape_string(html_entity_decode($this->swencoding($rs['items'][$i]['title'],$rss->encoding)));
				echo '</a></td>';
				echo '<td class="vals_v_'.$id.'" id="description_v_'.$id.'">'.html_entity_decode($this->swencoding((isset($rs['items'][$i]['description'])? $rs['items'][$i]['description']:"" ) ,$rss->encoding)).'</td>';

				if ($this->conf[$this->currentSection]['bot']){
					echo '<td id="v_'.$id.'">
					<a href="'.((isset($this->conf[$this->currentSection]['cFich']))? base64_encode($this->conf[$this->currentSection]['cFich']):false ).'"  class="feedButtom opts" ref="'.$this->currentValue.'"  title="'.$this->conf[$this->currentSection]['bottitle'].'" >
					<img src="./icons/'.$this->conf[$this->currentSection]['bot'].'" /></a></td>';
				}



				echo '</tr>';
			}
		}else{
			echo "No hay datos";

		}

	}


	public function draw() {



		echo $this->drawTitle();
		if (!$plantilla = $this->getPlt()) {
			iError::error("Plantilla no encontrada");
			return false;
		}

		$this->plantillaPrincipal = new tablon_plantilla($plantilla);
		$pl = &$this->plantillaPrincipal;

		$this->loadFeed($pl);

		$multiops = ( ($this->selected_have_opts_tablon_edit()) || (isset($this->selectedConf['del'])));
		$csv = ((isset($_GET['CSV'])) && ($_GET['CSV']=="1"));
		$generalops = ( (sizeof($this->history)>0) || (isset($this->selectedConf['new'])));
		/*filtrado¿?*/
		$sql = $this->doSQL();

		if (isset($this->selectedConf['search'])) {
			echo '<p id="__user_filtrado"><img src="./icons/viewmag.png" alt="utilizar filtrado" />Activar Filtrado de campos</p>';
			$this->drawSearcher();
		}
		/*filtrado¿?*/
		echo '<div id="tablonContainer">';

		if ($csv) {
				ob_end_clean();
			 	header("Content-type: application/vnd.ms-excel");
	         header("Content-Disposition:  filename=\"".i::clean($this->selectedConf['tit']).".xls\";");
		}
		echo '<table'.(($csv)? '>':' id="tablon" class="tablon"  genero="'.$pl->getGenero().'" entidad="'.$pl->getEntidad().'">');
		//if (!$csv) echo $this->plantillaPrincipal->drawHead($this->hasOptions(),$this->getNoShownFields(),$multiops,krm_menu::getURL().$this->getHistoryURL());
		$ret = '<tr class="tablon_tr_header">';
		$ret .= '<th>';
		$ret .= $this->l->l('fecha');
		$ret .= '</th>';
		$ret .= '<th>';
		$ret .= $this->l->l('título');
		$ret .= '</th>';
		$ret .= '<th>';
		$ret .= $this->l->l('descripción');
		$ret .= '</th>';
		if ($this->conf[$this->currentSection]['bot']){
			$ret .= '<th></th>';
		}
		$ret .= '</tr>';
		echo $ret;




		if (!$this->doSQL()) {
			$this->drawErrors();
		} else {

			$this->drawFeeds();

		}
		echo '</table>';

		if ($csv) exit();
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

			if ((isset($this->selectedConf['del'])) && ((bool)$this->selectedConf['del'])) {
				echo '<li class="opts"><a class="optsLink" title="'.$this->l->lstr('Eliminar',false,$pl->getEntidad()).'" ><img src="./icons/_delete.png" alt="Eliminar" id="multiDelete" objetoElim="'.$pl->getEntidad().'" />'.$this->l->lstr('Eliminar',false,$pl->getEntidad()).'</a></li>';
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

		if ( (isset($this->selectedConf['limit'])) && ($this->totalRows>$this->selectedConf['limit']) ) {
			$url = krm_menu::getURL().$this->getHistoryURL();
			if (isset($_GET['order'])&&$_GET['order']!="") $url.='order='.$_GET['order'].'&amp;';
			if (isset($_GET['orderType'])&&$_GET['orderType']!="") $url.='orderType='.$_GET['orderType'].'&amp;';

			$url.='pag=';
			echo '<ul class="paginado">';
			echo '<li><a href="'.$url.'1">&lt;&lt;</a></li>';
			echo '<li><a href="'.$url.(($this->getPag(true)<=1)? 1:($this->getPag(true)-1)).'">&lt;</a></li>';
			for($i=($this->getPag(true)-2);$i<($this->getPag(true)+3);$i++) {
				if ($i<1) continue;
				if ($i>ceil($this->totalRows/$this->selectedConf['limit'])) break;
				if ($i==$this->getPag(true)) {
					echo '<li><strong>'.$i.'</strong></li>';
					continue;
				}
				echo '<li><a href="'.$url.$i.'">'.$i.'</a></li>';


			}
			echo '<li><a href="'.$url.(($this->getPag(true)==ceil($this->totalRows/$this->selectedConf['limit']))? ceil($this->totalRows/$this->selectedConf['limit']):($this->getPag(true)+1)).'">&gt;</a></li>';
			echo '<li><a href="'.$url.ceil($this->totalRows/$this->selectedConf['limit']).'">&gt;&gt;</a></li>';
			echo '</ul>';
		}


		/*
			hidden fields for ajax new.
		*/
		echo '<form id="hiddenFields">';
		if (isset($this->selectedConf['idcond']))  {
			echo '<input type="hidden" name="'.$this->selectedConf['idcond'].'" value="'.$this->currentValue.'" />';
		}

		if ( isset($this->selectedConf['logincond']) && in_array('insert',explode('|',$this->selectedConf['logincondVConf'])))  {
//			$aConds[] = ' '.$pl->getTab().'.'.$this->selectedConf['logincond'].'=\''.$_SESSION["__ID"].'\'';
			echo '<input type="hidden" name="'.$this->selectedConf['logincond'].'" value="'.$_SESSION["__ID"].'" />';
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
						list($c,$v)=explode('=',$pltCondiciones[$i],2);
						echo '<input type="hidden" name="'.$c.'" value="'.trim($v).'" />';
					}
				}
			}
		}
		echo '</form>';

		echo '</div>';
	}



}


?>
