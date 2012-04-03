<?php

class iError {
   public $aErrors = array();
   public $cont = 0;
   protected $_nivel;
   function iError($_nivel = 0) {
       $this->_nivel = $_nivel;
       $this->setIni();
   }

   function handler($errno, $errstr, $errfile, $errline, $errcontext) {
		$this->aErrors[] = array($errno,$errstr,$errfile,$errline);
   }

	function draw() {
		$check = true;
	    $ret = '';

	    foreach ($this->aErrors as $aError) {
			switch ($aError[0]) {
				case E_ERROR:
				case E_USER_ERROR:
					$check = false;
					$ret .= '<p class="errorLog e_koerror "';
					break;
				case E_WARNING:
				    if($this->_nivel < 1){
				        return true;
				    }
				case E_USER_WARNING:
					$check = false;
					$ret .= '<p class="errorLog e_ko"';
					break;
				case E_NOTICE:
			        if($this->_nivel < 2){
				        return true;
				    }
				case E_USER_NOTICE:
					$check = false;
					$ret .= '<p class="errorLog e_ok"';
					break;
				default:
				    if($this->_nivel < 3){
				        return true;
				    }
				    $check = false;
                    $ret .= '<p class="errorLog e_ok"';
                    break;
			}

			$ret .= " title=\"".basename($aError[2])." [".$aError[3]."]\"  />".$aError[1]."</p>";
		}

		if ($check) {
		    return true;
		}

		echo $ret;
		return true;
	}

	function isError() { return $this->aErrors; }

	static function error($txt) {
		trigger_error($txt, E_USER_ERROR);
	}
	static function warn($txt) {
		trigger_error($txt, E_USER_WARNING);
	}
	static function ok($txt) {
		trigger_error($txt, E_USER_NOTICE);
	}

	function setIni() {
  	    set_error_handler(array(&$this, 'handler'));
  	}
}
