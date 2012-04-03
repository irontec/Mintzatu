<?php

define("EXEC_SUID", dirname(__FILE__) . "/cgi/exec_suid.cgi");
//include_once(i::base_path()."/configuracion/defs.cfg");

class tablon_FLDfilefs extends tablon_FLDfile
{

	const default_source_action ='mv'; // especifica al origen si debe mv / cp

	public $data_path = false;

	function __construct($conf, $idx, $plt = false)
    {
		parent::__construct($conf, $idx, $plt);

		if (!$this->isFileSystem()) {
			iError::warn("Falta la directiva filesystem en el campo [".$this->file_name."]");
			return false;
		}

		list($ret, $salida) = $this->exec_suid(array('KRM_TMP_FILE'=> dirname(__FILE__).'/../../../'.$this->data_path,'PHP_CGI_FILE'=>'check_writable'));
		if ($ret!=0) {
            iError::warn("Error ejecutando binario. ¿Quizás estés en 64bits?<br />".implode("<br />", $salida) . $ret);
            return;
        }

		list($ret, $salida) = $this->exec_suid(array('KRM_EXEC_SUID'=> EXEC_SUID,'PHP_CGI_FILE'=>'check_suid'));
		if ($ret!=0) {
            iError::warn(implode("<br />", $salida));
        }

	}

	public function afterupdate($value, $id, $tab)
    {
		$this->setCurrentID($id);

		if (!isset($value['size']) || $value['size']<=0) {
		    return false;
		}

		$fname = $value['tmp_name'];
		chmod($fname, 0777);

		$aEnvs = array(
			'KRM_TMP_FILE'=>$fname,
			'KRM_PLT'=>$this->getPlt(),
			'KRM_FLD'=>$this->index,
			'KRM_ID'=>$id,
			'KRM_ACTION'=>self::default_source_action,
			'PHP_CGI_FILE'=>'move');

		list($ret, $salida) = $this->exec_suid($aEnvs);
		if ($ret==0) return true;
        else return false;

	}

	public function afterdelete($id, $tab, $delFld=false)
    {
		return false;
	}

	protected function exec_suid($envs)
    {
		//putenv('KARMA_ALLOW=1');
		foreach ($envs as $e => $v) {
		    putenv($e."=".$v);
		}

		$a = exec(EXEC_SUID, $resultado, $ret);
		return array($ret, $resultado);

	}

	public function drawTableValue($v = false)
    {
		global $kMenu;
		if (($v === false) || (is_array($v))) $v = $this->subFields[$this->file_name]->getValue();

		$srcFile = $this->getRealPath($this->getCurrentID());
		$mimeType = i::mime_content_type($srcFile);

		if (!$icono = i::getMimeTypeIcon($mimeType, $v)) {
			return '<a href="../cache/tablon/filefs/'.$this->getPlt().'/'.$this->getSQLFLD().'/'.$v.'" >"'.$v.'"</a>';
		} else {
			//Icono por defecto (por si no es una imagen o un video)
			$iconUrl = "../cache/mimeCache/{$icono}";

			$arMime = explode("/", $mimeType);
			if (sizeof($arMime) > 0) {
				switch($arMime[0]){
					case "image":
						$dst = i::base_path()."cache/mimeCache/{$this->plt}/{$this->index}/cache/{$this->getCurrentID()}";
						if (!is_file($dst)) {
							if (!is_dir(dirname($dst)))
								mkdir(dirname($dst), 0777, true);

							$im = new i_image($srcFile);
							if ($im->setNewDim(20, 20, false)) {
								$im->prepare();
								$im->imResize($dst);
							}
						}
						$iconUrl = "../cache/mimeCache/{$this->plt}/{$this->index}/cache/{$this->getCurrentID()}";
						break;
					case "video":
						$dst = i::base_path()."cache/mimeCache/{$this->plt}/{$this->index}/cache/{$this->getCurrentID()}";
						if (defined("__FFMPEG") && file_exists(__FFMPEG)) {
							if (!is_file($dst)) {
								if (!is_dir(dirname($dst)))
									mkdir(dirname($dst), 0777, true);
									$salidaCmd = exec(__FFMPEG.' -i ' . $srcFile . ' 2>&1 | grep Duration: | cut -f4 -d" " | cut -f1 -d","');
									if (preg_match("/([0-9]{2}):([0-9]{2}):([0-9]{2}).*/", $salidaCmd, $matches)) {
										$segundos = $matches[1]*3600+$matches[2]*60+$matches[3];
										exec(__FFMPEG." -y -i {$srcFile} -f image2 -ss ".round($segundos*0.25)." -vframes 1 -s 40x30 -an {$dst}");
									}
							}
						}
						if (is_file($dst)) {
							$iconUrl = "../cache/mimeCache/{$this->plt}/{$this->index}/cache/{$this->getCurrentID()}";
						}
						break;
				}
			}
			return "<a href='../cache/tablon/filefs/{$this->getPlt()}/{$this->getSQLFLD()}/{$v}' ><img " .
                ( ($arMime[0] == 'image')? 'class="mag filefs" ':'') . 
                "src='{$iconUrl}' alt='{$mimeType}' /><span>{$v}</span></a>";
		}
	}

	public function getRealPath($id)
    {
		return dirname(__FILE__)."/../../../". $this->calcdir($id, true) .  $id;
	}

	protected function getIdFromName($n)
    {
		if ($this->subFields[$this->file_name]->isUnique()) {
			$aFields = parse_ini_file(RUTA_PLANTILLAS.$this->plt, true);
			$sql = 'select '.$aFields['::main']['id'].' from '.$aFields['::main']['tab'].' where '.$this->conf['name_fld'].'= \''.$n.'\' limit 1';

			$con = new con($sql);
			if ($con->getNumRows()>0) {
				return $con->getResult();
			}
		}
		return false;
	}

	/**
	 * Devuelve el directorio calculado a través del id
	 *     2:
	 *         0/
	 *     12:
	 *         1/
	 *     1456:
	 *         1/4/5
	 *     etc...
	 *
	 * @param $_id
	 * @param $all Si es true, devuelve el path con el directorio "data" y el nombre del plt incluidos
	 */
	protected function calcdir($_id, $all = false)
	{
	    $path = '';

	    if ($_id < 10) {
	        $id = '0';
	    } else {
	        $id = substr((string)$_id, 0, -1);
	    }

	    if ($all) {
	        $path .= $this->data_path . DIRECTORY_SEPARATOR
                . $this->getPlt() . DIRECTORY_SEPARATOR;
	    }

	    return $path .= implode(DIRECTORY_SEPARATOR, str_split($id)) . DIRECTORY_SEPARATOR;
	}
}
