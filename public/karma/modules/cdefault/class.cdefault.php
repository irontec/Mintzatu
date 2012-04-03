<?php

 class cdefault extends contenidos
 {
 	function __construct($conf) {
		$this->conf = $conf;
 	}

 	public function draw() {
 	    $str =  $this->getMLval($this->conf['main']['intro'], $this->conf['main'], 'intro');
		echo "<div class=\"intro\" >".$str;
		echo "</div>";
	}

}
