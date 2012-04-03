<?php
/**
 * Fichero de clase para campo tipo XHTML
 *
 * @author Arkaitz Etxeberria <arkaitz@irontec.com>
 * @version 1.0
 * @package karma
 */
class tablon_FLDxhtmlparser {
	
	protected $_error;
	protected $_file;
	protected $_conf;
	protected $_buttons = array(
				'bold' => array(
	                'name'=>'Bold',
	                'title'=>'Strong',
	                'css'=>'wym_tools_strong'
	            ),
	            'italic' => array(
	                'name'=> 'Italic',
	                'title'=> 'Emphasis',
	                'css'=> 'wym_tools_emphasis'
	            ),
	            'superscript' => array(
	                'name'=> 'Superscript',
	                'title'=> 'Superscript',
	                'css'=> 'wym_tools_superscript'
	            ),
	            'subscript' => array(
	                'name'=> 'Subscript',
	                'title'=> 'Subscript',
	                'css'=> 'wym_tools_subscript'
	            ),
	            'ol' => array(
	                'name'=> 'InsertOrderedList',
	                'title'=> 'Ordered_List',
	                'css'=> 'wym_tools_ordered_list'
	            ),
	            'ul' => array(
	                'name'=> 'InsertUnorderedList',
	                'title'=> 'Unordered_List',
	                'css'=> 'wym_tools_unordered_list'
	            ),
	            'indent' => array(
	                'name'=> 'Indent',
	                'title'=> 'Indent',
	                'css'=> 'wym_tools_indent'
	            ),
	            'outdent' => array(
	                'name'=> 'Outdent',
	                'title'=> 'Outdent',
	                'css'=> 'wym_tools_outdent'
	            ),
	            'undo' => array(
	                'name'=> 'Undo',
	                'title'=> 'Undo',
	                'css'=> 'wym_tools_undo'
	            ),
	            'redo' => array(
	                'name'=> 'Redo',
	                'title'=> 'Redo',
	                'css'=> 'wym_tools_redo'
	            ),
	            'link' => array(
	                'name'=> 'CreateLink',
	                'title'=> 'Link',
	                'css'=> 'wym_tools_link'
	            ),
	            'unlink' => array(
	                'name'=> 'Unlink',
	                'title'=> 'Unlink',
	                'css'=> 'wym_tools_unlink'
	            ),
	            'table' => array(
	                'name'=> 'InsertTable',
	                'title'=> 'Table',
	                'css'=> 'wym_tools_table'
	            ),
	            'paste' => array(
	                'name'=> 'Paste',
	                'title'=> 'Paste_From_Word',
	                'css'=> 'wym_tools_paste'
	            ),
	            'html' => array(
	                'name'=> 'ToggleHtml',
	                'title'=> 'HTML',
	                'css'=> 'wym_tools_html'
	            ));
	

	function __construct() {


	}

	public function loadFile($file) {
		if (!file_exists($file)) {
			$this->_error = "No existe el fichero de configuracion";
			return false;
		}

		$this->_file = $file;
		$this->_conf = parse_ini_file($this->_file,true);
		return $this;
	}

	public function parseFile() {
		foreach($this->_conf as $p => $val) {
			if(!strpos($p,'::') && $val['active'] == 'true'){
				$perfil = $p;
			} elseif(!strpos($p,'::') && strpos($p,"/") == '0') {
				unset($perfil);
			}
			if(strpos($p,'general') && isset($perfil)) {

				foreach($val as $item => $va) {
					if($item != 'toolsItems') {
						$json[$perfil]['wymeditor'][$item] = $va;
					} else {
						$tools = explode('|',$va);
						foreach($tools as $tool){
								$json[$perfil]['wymeditor'][$item][$tool] = $this->_buttons[$tool];
						}
					}
				}
				if(!isset($json[$perfil]['wymeditor']['toolsItems']) || empty($json[$perfil]['wymeditor']['toolsItems'])) 
				$this->_error[] = "No hay ningún botón configurado, revisa el archivo de configuración";
			}
			if(strpos($p,'classes::') && isset($perfil)) {
				$clase = explode('::',$p);
				$clase = $clase[2];
				$spec['name'] = $clase;
				foreach($val as $c => $v) {
					if($c != 'css'){
						$spec[$c] = $v;
						if($c == 'name') $clase = $v;
					} else {
						$css = $v;
					}
				}
				$json[$perfil]['wymeditor']['classesItems'][] = $spec;
				$json[$perfil]['wymeditor']['editorStyles'][] = array(
					'name' => '.'.$clase,
					'css' => $css
				);
				unset($clase);
				unset($spec);
			}
			
			if(strpos($p,'cssfile') && isset($perfil)) {
				if (!isset($val['source'])) {
					$this->_error[] = "Has olvidado especificar la propiedad 'cssfile' ";
					continue;
				}
				$cssfile = i::base_path() . $val['source'];
				
				if (!file_exists($cssfile)) {
					$this->_error[] = "El fichero css especificado no existe.";
					continue;
				} else {
					$_rawCSS = file_get_contents($cssfile);
					preg_match_all("/\\n(.*)\{.*\\/\*WYM\:([^\*]*)\*\\/([^\}]*)\}/Um",$_rawCSS,$results,PREG_SET_ORDER);
					
					
					foreach($results as $result) {
						if ( preg_match("/([a-z0-9]*)\.([^.]*)$/",trim($result[1]),$miniresult)) {
							$_elem = $miniresult[1];
							$_class = $miniresult[2];
						} else {
								continue;
						}
							
						
						$_titulo = $result[2];
						$_css = trim(str_replace("\n","",$result[3]));
						$json[$perfil]['wymeditor']['classesItems'][] = array(
							"name"=>$_class,
							"title"=> strtoupper($_elem).': ' . $_titulo,
							"expr"=>$_elem
						);
						$json[$perfil]['wymeditor']['editorStyles'][] = array(
							'name' => '.'.$_class,
							'css' => $_css
						);				
						
					
					}
					
				}
			}
			
			
			if(strpos($p,'plugins::') && isset($perfil)) {
				$plugin = $val['type'];
				$inst = explode('::',$p);
				$inst = $inst[2];
				
				if($val['active'] == 'true') {
					
					if(!isset($val['type']) || empty($val['type']))
						$this->_error[] = "Has olvidado poner el tipo de plugin en el botón que has llamado '".$inst."'";
					
					$json[$perfil]['plugins'][$inst] = $plugin;
					if(count($val) > 1) {
						foreach($val as $data => $value){
							 $specs[$data] = $value;
						}
						$json[$perfil][$inst][] = $specs;
					}
				}
				unset($plugin);
				unset($specs);
			}
		}
		if(!$json || empty($json)) $this->_error[] = "No hay perfiles activos, revisa el archivo de configuración";
		if($this->_error) $json['error'] = $this->_error;
//		var_dump($json);
		die(json_encode($json));
	}
}
