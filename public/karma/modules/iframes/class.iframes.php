<?php
class iframes extends contenidos {
	
	protected $currentSection = 0;
	protected $currentFather = false;
	protected $currentValue = false;
	protected $lastValue = false;
	protected $history = array();
	
	protected $rutaIframes = "../../../iframes/";
	
	public $tipoIframe = false;
	public $nomIframe = false;
	public $titulo = false;
	public $altura = false;
	public $anchura = false;
	public $selectedConf = null;

	/**
	 * método constructor para tablón.
	 *
	 * @param array $conf datos provenientes del fichero de configuración
	 */
	function __construct(&$conf) {
		$this->conf = $conf;
		if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
                        $this->nomIframe = str_replace("%hostip%",$_SERVER['HTTP_X_FORWARDED_HOST'],$this->conf["main"]['nombreIframe']);
                } else {
                        $this->nomIframe = str_replace("%hostip%",$_SERVER['HTTP_HOST'],$this->conf["main"]['nombreIframe']);
                }
		$this->titulo = $this->conf["main"]['tituloIframe'];
		$this->altura = $this->conf["main"]['alturaIframe'];
		$this->anchura = $this->conf["main"]['anchuraIframe'];
		$this->tipoIframe = $this->conf["main"]['tipoIframe'];
		$this->setCurrentSection();
		
		/*$this->aJs[] = "../modules/tablon/scripts/date.js";
		$this->aJs[] = "../modules/tablon/scripts/jquery.autocomplete.js";
		//$this->aJs[] = "../modules/tablon/scripts/jquery.datePicker.js";
		$this->aJs[] = "../modules/tablon/scripts/tablon_dateformat.js";*/
		$this->aJs[] = "../modules/iframes/scripts/iframes.js";

	}
	
	protected function getIframe(){
		//if (!file_exists($this->rutaPlantillas.$this->conf[$sec]['plt'])) return false;
		if($this->tipoIframe == 'url') return $this->nomIframe;
		if($this->tipoIframe == 'directorioRel') return $this->nomIframe;
		if(!file_exists($this->rutaIframes.$this->nomIframe)) return false;
		else return $this->rutaIframes.$this->nomIframe;
	}
	
	protected function setCurrentSection() {
		if ((isset($_GET['tSec'])) && (is_array($_GET['tSec']))) {
			foreach($_GET['tSec'] as $idx => $vlr) {
				list($sec,$op) = explode("::",$idx);
				if ( (isset($this->conf[$sec]['ops'][$op])) && (isset($this->conf[$op])) ) {
					$this->history[$sec] = array($op,$vlr);
					$this->currentSection = $op;
					$this->currentFather = $sec;
					$this->currentValue = $vlr;
					$this->lastValue = $this->getLastValue();
				}
			}
		}
		$this->selectedConf = &$this->conf[$this->currentSection];
	}
	
	static function getAltura(){
		$this->altura;
	}
	
	static function getAnchura(){
		$this->altura;
	}
	
	public function draw(){
		echo '<input type="hidden" name="altIframe" value="'.$this->altura.'" id="altIframe">';
		echo '<input type="hidden" name="anchIframe" value="'.$this->anchura.'" id="anchIframe">';
		echo '<iframe class="iframe" frameborder="0" style="margin:auto;border:0px;height:'.($this->altura).';width:'.($this->anchura).';" width = "'.($this->anchura).'" height="'.($this->altura).';" src="'.$this->getIframe().'" ></iframe>';
	}
}
?>
