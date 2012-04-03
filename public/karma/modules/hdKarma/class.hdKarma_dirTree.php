<?php

define("SPACES",3);

class hdKarma_dirTree {
	static $list = array(0=>array());
	
	static function _drawOptions($id_padre,$new,$level,$nom) {
	
		if ($new) {
				echo "<option class=\"newdir\" value=\"new_".$id_padre."\">".str_repeat("&nbsp;",($level*SPACES))."...[Nuevo dir en ".$nom.")</option>";
		}	
		
		if (isset(self::$list[$id_padre])) {
			foreach (self::$list[$id_padre] as $id_dir => $ops) {
				echo "<option class=\"".(($level%2 == 0)? "par":"impar")."\"value=\"".$id_dir."\">".str_repeat("&nbsp;",($level*SPACES)).$ops[0]."</option>";
				self::_drawOptions($id_dir,$new,($level+1),$ops[0]);
			}
		}	
		
	}

	static function drawList($path) {
		$id_parent = (int)(str_replace("hd_folder_","",$path));
		$c = new con("select id_dir, id_parent,nombre, deleteable from hdKarma_dirs where id_parent='".$id_parent."'");
		$ret = array();
		
		while ($r = $c->getResult()) {
			$rTmp = array("text"=>$r['nombre'],"classes"=>"folder","id"=>'hd_folder_'.$r['id_dir']);
			$rTmp["hasChildren"] = true;
			$ret[] = $rTmp;
		}

		if (isset($_GET['new'])) $ret[] = array("text"=>"<input type='text' value='' class='hd_newFolder' />","classes"=>"folder","id"=>'hd_folder_new_'.$id_parent);		
		
		echo json_encode($ret);		
 
	}
	
	static function newDir($nombre,$idPadre) {
		$c = new con("select 1");
		$n = mysql_real_escape_string($nombre);
		$idp = (int)$idPadre;
		$c = new con("select id_dir from hdKarma_dirs where id_parent='".$idpadre."' and nombre='".$n."'");
		if ($c->getNumRows()==1) {
			return "Ya existe un directorio con ese nombre.";
		}
		$c = new con("insert into hdKarma_dirs (id_parent,nombre) values('".$idp."','".$n."')");
		if ($c->error()) {
			$c->dump();
			return "Error indeterminado creado el directorio";
		}
		return "ok";
	
	}	 	
	
	static function drawOptions() {
		return '<ul>
				<li id="hd_newFolder" class="karmaBarOption">Nueva Carpeta</li>
				<li id="hd_upload" class="karmaBarOption">Subir Ficheros</li>
			</ul>		
		';	
	}


}