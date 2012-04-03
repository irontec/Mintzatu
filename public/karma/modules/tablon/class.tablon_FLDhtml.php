<?php
/**
 * Fichero de clase para campo tipo SAFETEXT
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

class tablon_FLDhtml extends tablon_FLDsafetext {
    protected $_customConf = false;

	public function __construct($conf, $idx) {
	    parent::__construct($conf, $idx);
        if ($c = krm_menu::getinstance()->getMenuSection('main', 'tinyConf') ) {
            $this->_customConf = $c;
        }

	}

	public function getType() {
		return "textarea";
	}

	public function drawTableValueEdit($value,$clone=false,$disabled=false) {
        $value = str_replace("\\\"", "", $value);
		return '
		<a  href="javascript:toggleEditor(\''.$this->getSQLFLD().'_'.$this->getCurrentID().'\');" class="nomove">' . $this->l->l('Add/Remove editor') . '</a>' .
		(
		(isset($this->conf['url_preview']))?
		'&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;'.
		'<a class="html_preview nomove" href="'.i::base_url().'" rel="'.$this->getSQLFLD().'_'.$this->getCurrentID().'">' . $this->l->l('Preview') . '</a>'
		:''
		).
		'<textarea name="'.$this->getSQLFLD().'" id="'.$this->getSQLFLD().'_'.$this->getCurrentID().'" rows="12" cols="65" class=" ' . (($this->_customConf)? 'tiny':'tiny')  .(($this->isRequired())? ' required':'').'" >'.$this->drawTableValue($value).'</textarea>';

	}
	public function getMysqlValue($value) {
		$c = new con("select 1;");
		$this->setValue(tablon_FLD::cleanMySQLValue($value));
		return '\''.str_replace('\n','',$this->getValue()).'\'';
	}

	public function getConstantTypeAjaxUpload() {
		return "_POST";
	}

	public function loadJS(){
		$js[] = "../modules/tablon/scripts/tiny_mce/tiny_mce.js";
		if (isset($this->conf['tinyConf']) ){
			$js[] = "../../configuracion/js/".$this->conf['tinyConf'];
		} elseif ($this->_customConf !== false) {
		    $js[] = "../../configuracion/js/".$this->_customConf;
		} else {
			$js[] = "../modules/tablon/scripts/tiny_conf.js";
		}
		return $js;
	}

	public function getPreviewParams() {
		$ret = array();
		foreach($this->conf as $key => $value) {
			if (preg_match("/^preview_param/",$key)) {
				list($k,$v) =  explode("=",$value);
				$ret[$k] = $v;
			}
		}
		return $ret;
	}

	public function getURLPreview() {
		if (isset($this->conf['url_preview'])) return $this->conf['url_preview'];
		return false;
	}

}


?>
