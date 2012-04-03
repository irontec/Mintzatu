<?php


class hdKarma {


	protected $new = false;
	protected $confs = array();
	protected $kMenu;

	function __construct($kMenu) {
		$this->kMenu = $kMenu;
		$this->kMenu->aPrincJs = array_merge($this->kMenu->aPrincJs,$this->getHdKarmaJs());
		$this->kMenu->aPrincCss = array_merge($this->kMenu->aPrincCss,$this->getHdKarmaCSS());

		$menu = $this->kMenu->getMenuSection('hdKarma');
		foreach ($menu as $prop => $value) {
			$this->confs[$prop] = $value;
		}

		$this->checkHdDIR();
		$this->checkHdDB();
	}

	protected function checkHdDIR() {
		if (!isset($this->confs['baseDir'])) iError::warn("El directorio Base para HDKarma no está configurado.");
		if (!file_exists(dirname(__FILE__)."/../../../".$this->confs['baseDir'])) iError::warn("El directorio base del Disco Duro no existe (".$this->confs['baseDir'].").");
		if (!is_writable(dirname(__FILE__)."/../../../".$this->confs['baseDir'])) iError::warn("El directorio Base del Disco Duro no tiene permiso de escritura.");
	}

	protected function checkHdDB() {
		$c = new con("describe hdKarma_dirs");
		if ($c->error()){
			$sql = "
CREATE TABLE `hdKarma_dirs` (
`id_dir` mediumint(8) unsigned NOT NULL auto_increment,
`id_parent` mediumint(8) unsigned NOT NULL default '0',
`nombre` varchar(255) default NULL,
`deleteable` enum('0','1') NOT NULL default '0',
PRIMARY KEY  (`id_dir`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8";
			iError::warn($c->geterror());
			iError::warn("<textarea>".$sql."</textarea>");
		}

		$c = new con("describe hdKarma_files");
		if ($c->error()) {
			$sql = "
CREATE TABLE `hdKarma_files` (
`id_file` mediumint(8) unsigned NOT NULL auto_increment,
`nombre` varchar(255) default NULL,
`url` varchar(255) default NULL,
`crc32` varchar(10) default NULL,
PRIMARY KEY  (`id_file`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

			iError::warn($c->geterror());
			iError::warn("<textarea>".$sql."</textarea>");
		}


	}

	public function getNew() {
		return $this->new['allow'];
	}

	public function getHdKarmaJs() {
		return array(
			'jquery/jquery.livequery.js',
			'jquery/jquery.disable.text.select.pack.js',
			'../modules/hdKarma/scripts/jquery.uploadify.js',
			'../modules/hdKarma/scripts/jquery.treeview.js',
			'../modules/hdKarma/scripts/jquery.treeview.async.js',
			'../modules/hdKarma/scripts/hdKarma_base.js');
	}
	public function getHdKarmaCSS() {
		return array(
			'../modules/hdKarma/css/hdkarma.css',
			'../modules/hdKarma/css/jquery.treeview.css',
			'../modules/tablon/css/tablon.css');
	}

	public function drawFireButton($tag) {
		return '<'.$tag.' class="karmaBarOption" id="hdFire">Disco Duro</'.$tag.'>';
	}

	/*
	* metodo para aplicar a $o...
	*/
	public function enableContentObjectMethod() {
		return false;
	}

}
