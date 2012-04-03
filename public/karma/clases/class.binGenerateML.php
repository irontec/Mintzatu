<?php

class binGenerateML {
	private $aTables = array();
	private $using = false;
	private $aCols = array();
	private $replace = false;
	private $defaultLang = false;
	private $langs = array();
	private $cloneCols = array();
	private $separator = "_";

	function __construct() {
		$c = new con("show tables;");
		$c->setResultType('NUM');
		$aTables = array();
		while($r = $c->getResult()) $this->aTables[] = $r[0];

	}


	public function getSt(&$fp) {
		$buf = "";
		do {
			$_b = fgets($fp);
			return $_b;
		} while(true);

	}

	public function dispatch($c) {
		$c = trim($c);
		switch(true) {
			case $c == "h" || $c == "help":
				echo "Mostrando ayuda....\n";
				$this->help();
			return;
			case $c == "t" || $c == "tabs":
				echo "Mostrando tablas....\n";
				$this->showtables();
			return;
			case $c == "r":
				echo "MODO REEMPLAZAR COLUMNA ACTIVADO\n";
				$this->replace = true;
			break;
			case $c == "nr":
				echo "MODO REEMPLAZAR COLUMNA DESACTIVADO\n";
				$this->replace = false;
			break;
			case $c == "m" || $c == "mode":
				echo "Mostrando modo....\n";
				$this->showMode();
			return;

			case preg_match("/^dLang(.*)$/",$c,$res):
				$param = trim($res[1]);

				if (sizeof($this->langs)>0) {
					if (!in_array($param,$this->langs)) {
						echo $param." no encontrado en listado de sufijos.\n";
						return;
					}

				} else {
					echo "AVISO: Aún no se han especificado sujifos de idioma\n";
				}

				$this->defaultLang = $param;
				echo "El sufijo de idioma principal es [".$this->defaultLang."].\n";

			return;

			case preg_match("/^set(.*)$/",$c,$res):
				$this->langs = array();
				$param = trim($res[1]);
				preg_match_all("/[a-zA-Z]{2}/",$param,$res);

				if (sizeof($res[0]) == 0) {
					echo "No se ha identificado ningún sufijo\n";
					return;
				}
				$this->langs = $res[0];
				if ( ($this->defaultLang === false) || (!in_array($this->defaultLang,$this->langs)) ) {
					$this->defaultLang = $this->langs[0];
				}

				echo "Usando ".implode(", ",$this->langs)." somo sufijos de idioma.\n";
				echo "El sufijo de idioma principal es [".$this->defaultLang."].\n";
			return;

			case preg_match("/^col(.*)$/",$c,$res):
				if ($this->using === false) {
					echo "No se ha seleccionado tabla.\n";
					return;
				}

				$this->cloneCols = array();

				$param = trim($res[1]);
				preg_match_all("/[a-zA-Z_0-9\-]+/",$param,$rCols);
				if (sizeof($rCols) == 0) {
					echo "No se han seleccionado columnas.\n";
					return;
				}

				foreach ($rCols[0] as $col) {

					if ( (is_numeric($col)) && (isset($this->aCols[($col-1)])) ) {
						$this->cloneCols[] = $col-1;
					} else {
						foreach($this->aCols as $idx => $n) {
						var_dump($n);
							if (preg_match("/^".$col."/",$n['Field'])) {
								$this->cloneCols[] = $idx;
								break;
							}
						}
					}
				}

				echo "Los cambios se aplicarán a ".sizeof($this->cloneCols)." columnas.\n";
			return;


			case preg_match("/^u(.*)$/",$c,$res):
				$this->using = false;

				$param = trim($res[1]);

				if ( (is_numeric($param)) && (isset($this->aTables[($param-1)])) ) {
					$this->using = $param-1;
				} else {
					foreach($this->aTables as $idx => $n) {
						if (preg_match("/^".$param."/",$n['Field'])) {
							$this->using = $idx;
							break;
						}
					}
				}
				if ($this->using === false) {
					echo "No se encuentra la tabla \"".$param."\"...\n\n";
					return;
				}
				echo "Usando ".$this->aTables[$this->using].":\n";
				$this->showcols();
			return;

			case $c == "run":
				$this->applyBrief();
			return;

			case $c == "run yeah!":
				$this->applyBrief(true);
			return;
		}


	}


	public function help() {
		echo "Ayuda\n=====\n";
		echo "h\t\tEsta ayuda\n";
		echo "tabs\t\tMuestra todas las tablas\n";
		echo "use tb\t\tUsar tabla por número o nombre\n";
		echo "mode\t\tMuestra el modo de edición de la tabla\n";
		echo "r\t\tMODO REEMPLAZAR COLUMNA ACTIVADO\n";
		echo "nr\t\tMODO REEMPLAZAR COLUMNA DESACTIVADO\n";
		echo "set l1..l2\tSetea listado de sufijos. (si no se ha definido el idioma principal, se usará el primero)\n";
		echo "col c1..cn\tSetea columnas para clonar\n";
		echo "dLang l1\tSetea un idioma como principal\n";
		echo "run\t\tEjecuta las sentencias\n";
		echo "run yeah!\tlas ejecuta de VERDAD :)\n";
		echo "\n";
	}

	public function showtables() {
		for($i=0;$i<sizeof($this->aTables);$i++) {
			echo ($i+1)." ".$this->aTables[$i]."\n";
		}
		echo "\n";
	}

	public function loadcols() {
		$c = new con("show columns from ".$this->aTables[$this->using]);
		$this->aCols = array();
		while($r = $c->getResult()) $this->aCols[] = $r ;
	}

	public function showcols() {
		$this->loadcols();
		for($i=0;$i<sizeof($this->aCols);$i++) {
			echo ($i+1)." ".$this->aCols[$i]['Field']." (".$this->aCols[$i]['Type'].")\n";
		}

		echo "\n";
	}


	public function showMode() {
		if ($this->replace) {
			echo "Las columnas seleccionadas se reemplazarán (alter table modify) por sufijo del idioma principal.\n";
		} else {
			echo "Las columnas seleccionadas NO se reemplazarán (alter table modify) por sufijo del idioma principal.\n";
		}


		if (sizeof($this->langs) == 0) {
			echo "No hay sufijos de idioma definidos.\n";
		} else {
			echo "Usando ".implode(", ",$this->langs)." somo sufijos de idioma.\n";
		}
		if ($this->defaultLang === false) {
			echo "No esta definido el sufijo para idioma principal.\n";
		} else {
			echo "El sufijo de idioma principal es [".$this->defaultLang."].\n";
		}

	}

	private function  _getDefault($null,$def) {

		if ($def == NULL) $def=(string)"NULL";

		if ($null == "NO") {
			if ($def == "NULL") return "";
			else return "default ".$def;
		}	else {
			return "default ".$def;
		}

	}


	public function applyBrief($doit = false) {
		$t = $this->aTables[$this->using];
		echo "En la tabla: [".$t."]\n";
		echo "Se realizarán estas acciones:\n";
		foreach ($this->cloneCols as $idCol) {
			$cName = $this->aCols[$idCol]['Field'];
			$cNameOriginal = $cName;
			$cName = preg_replace("/_[a-z]{2}$/","",$cName);
			$cType = $this->aCols[$idCol]['Type'];
			$cNull = $this->aCols[$idCol]['Null'];
			$cDefault = $this->aCols[$idCol]['Default'];

			$startlang = 0;
			$prev = $cNameOriginal;
			if ($this->replace) {
				$startlang = 1;
				$s =  "alter table $t change $cName ".$cName.$this->separator.$this->langs[0]." ".$cType." ".(($cNull == "NO")? " not NULL":"")." ".$this->_getDefault($cNull,$cDefault).";";
				echo $s;
				if ($doit) {
					$c = new con($s);
					if ($c->error()) echo " [MOK (".$c->getError().")]";
					else echo "[OK]";
				}
				echo "\n";
				$prev = $cName.$this->separator.$this->langs[0];
			}

			for ($i = $startlang; $i<sizeof($this->langs);$i++) {
				$s = "alter table $t add ".$cName.$this->separator.$this->langs[$i]." ".$cType." ".(($cNull == "NO")? " not NULL":"")." ".$this->_getDefault($cNull,$cDefault)." after $prev;";
				echo $s;
				if ($doit) {
					$c = new con($s);
					if ($c->error()) echo " [MOK (".$c->getError().")]";
					else echo "[OK]";
				}
				echo "\n";
				$prev = $cName.$this->separator.$this->langs[$i];
			}
		}

	}


}


?>