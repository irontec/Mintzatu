<?php
class tablon_FLDfsfilefs extends tablon_FLDfilefs
{
	const default_source_action = 'cp'; // especifica al origen si debe mv o cp

	public $source_path;

	function __construct($conf, $idx, $plt = false)
	{
		parent::__construct($conf, $idx, $plt);

		if (!$this->isSourceFileSystem()) {
			iError::warn("Falta la directiva filesystem en el campo [".$this->file_name."]");
			return false;
		}
	}

	public function loadJS()
	{
		return array(
			"../modules/tablon/scripts/jquery.FileTree.js"
		);
	}

	public function loadCSS()
	{
		return array(
			"../modules/tablon/css/jqueryFileTree.css"
		);
	}

	public function getType()
	{
		return "fsfilefs";
	}

	public function isSourceFileSystem()
	{
		if ($this->conf['filesystem_source']) {
			$this->source_path = $this->conf['filesystem_source'];
			return true;
		}
		else return false;
	}

	public function getConstantTypeAjaxUpload()
	{
		return "_GET";
	}

	public function preInsertCheckValue(&$value)
	{
		if (!$ret = parent::preInsertCheckValue($value)) return $ret;
		$value['tmp_name'] = $this->source_path . $value['tmp_name'];
		return true;
	}


    /* Aunque el resto de parámetros además de $value no se usan, hay que ponerlas por ser una clase que extiende a otra (FILEFS) */
    public function drawTableValueEdit($value, $clone=false, $disabled=false)
    {
        return
            '<div class="fsfilefs" rel="'.$this->getSQLFLD().'"><span class="selectfsfilefs">Selecciona un fichero</span><div class="fileTreeDemo"></div>'.
            '<input type="hidden" name="'.$this->getSQLFLD().'[name]" class="name" value="" id="'.$this->getSQLFLD().'" />'.
            '<input type="hidden" name="'.$this->getSQLFLD().'[tmp_name]" class="tmp_name" value="" id="'.$this->getSQLFLD().'_tmp_name" />'.
            '<input type="hidden" name="'.$this->getSQLFLD().'[size]" class="size" value="" id="'.$this->getSQLFLD().'_size" />'.
            '<input type="hidden" name="'.$this->getSQLFLD().'[type]" class="type" value="" id="'.$this->getSQLFLD().'_type" /></div>'
            ;
    }
}
