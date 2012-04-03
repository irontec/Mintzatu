<?php 
	




class vhd extends contenidos{
	
	protected $dir_plt;
	
	protected $file_plt;

	protected $dir_fields;
	
	protected $file_fields;	
	
	protected $rutaPlantillas;
	
	protected $current_dir_content = false;
	
	protected $current_dir_content_files = false;
	
	protected $request_dir;
	
	public $currentSection;
	
	public function __construct(&$conf){
		
	    
	    
	    
		$this->l = new k_literal(KarmaRegistry::getInstance()->get('lang'));
		
		$this->conf = $conf;
		
		$this->setCurrentSection();
		
		$this->rutaPlantillas = "".dirname(__FILE__)."/../../../configuracion/karma_vhd/";
		
		$this->aJs[] = "../modules/vhd/scripts/vhd.js";
		$this->aJs[] = "../modules/vhd/scripts/jqueryFileTree.js";
		$this->aJs[] = "../modules/vhd/scripts/jquery.contextMenu.js";
		$this->aJs[] = "../modules/vhd/scripts/swfupload.js";
		$this->aJs[] = "../modules/vhd/scripts/fileprogress.js";
		$this->aJs[] = "../modules/vhd/scripts/handlers.js";
		$this->aJs[] = "../modules/vhd/scripts/swfupload.queue.js";
		$this->aJs[] = "../modules/vhd/scripts/jquery.base64.js";

		$this->aCss[] = "../modules/vhd/css/vhd.css";
		$this->aCss[] = "../modules/vhd/css/jqueryFileTree.css";
		$this->aCss[] = "../modules/vhd/css/jquery.contextMenu.css";
		$this->aCss[] = "../modules/vhd/css/default.css";
		
		
		
		$this->dir_plt = $this->selectedConf['dir_plt'];
		
		$this->file_plt = $this->selectedConf['file_plt'];
		
		$this->parse_dir_plt();
		
		$this->parse_file_plt();
		
		$this->uploads_dir = dirname(__FILE__).'/../../../'.$this->selectedConf['upload_dir'].'/';
		
	}
	
	protected function setCurrentSection()
    {
        if ((isset($_GET['tSec'])) && (is_array($_GET['tSec']))) {
            foreach($_GET['tSec'] as $idx => $vlr) {
                list($sec,$op) = explode("::",$idx);
                
                if ( (isset($this->conf[$sec]['ops'][$op])) && (isset($this->conf[$op])) ) {

                    $this->history[$sec] = array('op' => $op, 'vlr' => $vlr);
                    $this->currentSection = $op;
                    $this->currentFather = $sec;
                    $this->currentValue = $vlr;
                }
            }
        }
        
        
        if (!$this->currentSection||$this->currentSection==NULL) $this->currentSection="main";
        
        
        
        $this->selectedConf = $this->conf[$this->currentSection];
        
        
    }
	
	
	protected function parse_dir_plt(){

		if (!$this->dir_fields = @parse_ini_file($this->rutaPlantillas.$this->dir_plt,true)){
			
			iError::error("Plantilla de directorios no encontrada");
		}
	}

	protected function parse_file_plt(){

		if (!$this->file_fields = @parse_ini_file($this->rutaPlantillas.$this->file_plt,true)){
			
			iError::error("Plantilla de ficheros no encontrada");
		}
	}
	
	protected function get_id_parent(){
		
		$aDir = explode("/",$this->request_dir); 
			
		$dir = $aDir[sizeof($aDir)-2];
		
		$c = new con("select ".$this->dir_fields['::main']['id']." as __ID from ".$this->dir_fields['::main']['tab']." where ".$this->dir_fields['::main']['url']." = '".$dir."'");
		
		if ($c->getNumRows()<=0) return false;
		
		$r = $c->getResult();
		
		return $r['__ID'];
	}
	
	protected function load_dir(){
		
		if ($this->request_dir=="root/"||$this->request_dir=="root_dir/"){

			$cond=" is null ";
		}else{

			$id_parent = $this->get_id_parent();
			
			$cond = " = '".$id_parent."' ";
		}
		
		$borr = false;
		
		if ($this->dir_fields['::main']['deleted']){
			
			$borr = $this->dir_fields['::main']['deleted']." ='0' and ";
		}
		
		$c = new con("select * from ".$this->dir_fields['::main']['tab']." where ".(($borr)? $borr:"" )." ".$this->dir_fields['::main']['parent_id']." ".$cond);
		
		if ($c->getNumRows()>0) {
			
			while ($r = $c->getResult()){
				
				$this->current_dir_content[] = $r;
			}
		}
		
		$borr = false;
		
		if ($this->file_fields['::main']['deleted']){
			
			$borr = $this->file_fields['::main']['deleted']." ='0' and ";
		}		
		
		$c = new con("select * from ".$this->file_fields['::main']['tab']." where ".(($borr)? $borr:"" )." ".$this->file_fields['::main']['parent_id']." ".$cond);
		
		if ($c->getNumRows()>0){

			while ($r = $c->getResult()){
				
				$this->current_dir_content_files[] = $r;
			}
		}
	}

	protected function draw_dir_field($dir){
				
		echo "<li class=\"directory collapsed\" id=\"li__".$dir[$this->dir_fields['::main']['id']]."\"><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $dir[$this->dir_fields['::main']['url']]) . "/\">" . htmlentities(utf8_decode($dir[$this->dir_fields['::main']['defaultFLD']])) . "</a></li>";
	}

	protected function draw_file_field($file){

		echo "<li class=\"file ext_".preg_replace('/^.*\./', '', $file[$this->file_fields['::main']['url']])."\"><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file[$this->file_fields['::main']['url']]) . "\" id=\"file::" .$file[$this->file_fields['::main']['id']] . "\" >" . htmlentities($file[$this->file_fields['::main']['name']]) . "</a></li>";
	}	
	
	public function draw_dir(){
		
		$this->load_dir();
		
		$root = ($this->request_dir=="root/")? true:false; 

		if ($root){
			
			echo "<ul class=\"jqueryFileTree\" style=\"display: block;\">";
			
			echo "<li class=\"directory collapsed \"><a href=\"#\" rel=\"" . htmlentities('root_dir') . "/\" id=\"root_dir\">" . htmlentities('root') . "</a>";
		}
		
		if ($this->current_dir_content||$this->current_dir_content_files){
			
			echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
				
			foreach ($this->current_dir_content as $dir) $this->draw_dir_field($dir);
			
			foreach ($this->current_dir_content_files as $file) $this->draw_file_field($file);
			
			echo "</ul>";
		}
		
		if ($root){
			
			echo "</li></ul>";
		}
	
	}
	
	protected function get_context_menu(){
		
		echo '
		<ul id="myMenu" class="contextMenu">
		    <li class="edit ">
		        <a href="#new_folder">New Folder</a>
		    </li>
		    <li class="edit ">
		        <a href="#new_file">New File</a>
		    </li>
		    <li class="edit separator">
		        <a href="#edit">Edit</a>
		    </li>
		    <li class="delete">
		        <a href="#delete">Delete</a>
		    </li>
		</ul>
		<ul id="myMenu2" class="contextMenu">
		    <li class="edit ">
		        <a href="#download">Download</a>
		    </li>
		    <li class="edit separator">
		        <a href="#edit">Edit</a>
		    </li>
		    <li class="delete">
		        <a href="#delete_file">Delete</a>
		    </li>
		</ul>		
		';
	}
	
	
	public function draw(){
		
		if (isset($_GET['ajax'])){
			
			$this->ajax();
		}
		
		echo '<div id="vhd_div" >';
		
		echo '<div id="vhd_tree" ></div>';
		
		echo '</div>';
		
		echo '<script> 
					var sid = "'.session_id().'"; 
					var txurro = "'.base64_encode($_SERVER['PHP_AUTH_USER'].':'.$_SERVER['PHP_AUTH_PW']).'"; 
			</script>';
		
		echo $this->get_context_menu();
	}

	protected function getSQL(&$pl = false,$nofields = false) {
		
		/*
		 * 
		 * Cogido desde tablón y un poco ñapeado para que tire.
		 */
		
		if ($pl === false) $pl = &$this->plantillaPrincipal;
		$sql = "select SQL_CALC_FOUND_ROWS ";
		$pajar = array('GHOST','DATE','TAGS','MULTISELECT','MULTISELECTNG','MULTISELECTIVOZ','MULTISELECTNOREAL','MULTISELECTMULTI','DATETIME');
		$sql .= $pl->getTab().".".$pl->getID()." as __ID";
		if (method_exists($this,"getCustomSelectFields")) {
			$aFields = $this->getCustomSelectFields();
			foreach($aFields as $f) $sql .= ", ".$f;
		}
		for ($i=0;$i<$pl->getNumFields();$i++) {
			if (!$pl->fields[$i]->getInSQL() ){
				$sql .= ", ".$pl->fields[$i]->getSQL($pl->getTab());
				continue;
			}

			/*
			 * Si el campo tiene el atributo "fromAnotherTab" a true, obtener su sql sin la tabla del plt actual por delante ya que viene de otra tabla.
			 */
			if($pl->fields[$i]->ifFieldOfAnotherTab()){
				$sql .= ", ".$pl->fields[$i]->getSQL($pl->getTab());
				continue;
			}

			$sql .= ", ".((!in_array($pl->fields[$i]->getRealType(),$pajar))? $pl->getTab().".":"").$pl->fields[$i]->getSQL($pl->getTab());

			//Las imagenes y los ficheros tienen subfields, también los añadimos a la select...
			if ($pl->fields[$i]->hasSubFields()) {
				for ($j=0;$j<$pl->fields[$i]->sizeofsubFields;$j++) {
					$sql .= ",".$pl->fields[$i]->subFields[$j]->getSQL($pl->getTab());
				}
			}
		}
		$sql .= ' from '.$pl->getTab();
		$leftTabs = $pl->getALeftTabs();

		if(isset($leftTabs) && is_array($leftTabs) && !empty($leftTabs)){
			$leftTab = $leftTabs['lefttab'];
			$leftCond = $leftTabs['leftcond'];
			$leftWhere = $leftTabs['leftwhere'];

			if(sizeof($leftTab)>0 && sizeof($leftCond)>0 && sizeof($leftTab)==sizeof($leftCond)){
				for($i=0;$i<sizeof($leftTab);$i++){
					if($i!=0){
						$sql.=" ";
					}
					$sql .= " left join ".$leftTab[$i]." on(".$leftCond[$i].") ";
				}
			}
		}


		if(sizeof($leftWhere)>0) {
			$preWord = ($condiciones == "")? "where":"and";
			foreach($leftWhere as $l) {
				if ($l == "" || !$l) continue;
				$condiciones .= ' '.$preWord.' '.$l;
				$preWord = "and";
			}
		}
		$sql .= $condiciones;

		// WTF?!?!?!??!
		/*	if (isset($this->selectedConf['group'])	) {
					$sql .= ' group by '.$this->selectedConf['group'].' ';
			}*/

		if (($pl->getGroupBy()!==false) || (isset($this->selectedConf['group'])) ) {
			if ( (isset($this->selectedConf['group'])) && (trim($this->selectedConf['group'])!="" ) )$gr = $this->selectedConf['group'];
			if (trim($pl->getGroupBy()!="") ) $gr = $pl->getGroupBy();
			$sql .= " group by  ".$gr;
		}
		if ($having!="") {
			$sql .= " $having ";
		}
		if ((isset($_GET['order'])) && (isset($pl->fields[(int)$_GET['order']])) ) {
			$sql .= ' order by '.$pl->fields[(int)$_GET['order']]->getIndex();
			if ((isset($_GET['orderType'])) && ($_GET['orderType']=="desc")) {
				$sql .= ' desc ';
			}
		}elseif($pl->getOrderBy()){
			if (isset($this->selectedConf['order'])	) {
				$sql .= ' order by '.$this->selectedConf['order'].' ';
			}else{
				$sql .= ' order by '.$pl->getOrderBy().' ';
			}
		}

		if(isset($this->selectedConf['limit']) && !isset($_GET['CSV']) ){
			$sql .= ' limit '.$this->getPag().",".$this->selectedConf['limit'];
		}
		if (isset($_GET['DEBUG'])) iError::warn("<textarea>".$sql."</textarea>");

		return $sql;
	}	
	
	protected function new_folder_prompt($edit=false,$file_id=false){

		$ret = array();
		
		$ret['error'] = false;
		
		if ($file_id) {
		    
		    $plantilla = $this->rutaPlantillas.$this->file_plt;
		    
		} else {
		
		  $plantilla = $this->rutaPlantillas.$this->dir_plt;
		
		}
		/*
		 * 
		 * tablon ajax
		 */
		
		
		
		{
			
			$pl = new tablon_plantilla($plantilla);
			
			if ($edit){
				
			    if ($file_id) {
			        

			        
			        $c = new con($this->getSQL($pl)." where ".$this->file_fields['::main']['id']." = '".$file_id."'");
                    
			        
			        
                    if ($c->getNumRows()<=0) return false;
                    
                    $results = $c->getResult();
                    
			        
			    }else{
				
    				$this->request_dir = mysql_real_escape_string($_GET['rel']);
    				$aDir = explode("/",$this->request_dir); 
    			
    				$dir = $aDir[sizeof($aDir)-2];
    				
    				$c = new con($this->getSQL($pl)." where ".$this->dir_fields['::main']['url']." = '".$dir."'");
    				
    				if ($c->getNumRows()<=0) return false;
    				
    				$results = $c->getResult();
				
			    }
				
			}
			
			$fields = array();

			$onnewValues=false;

			if ($pl->onnew) $onnewValues = $pl->onnew;

			if (isset($_GET['tablononnew'])){
				$tmponnewValues = explode(',',$_GET['tablononnew']);
				if (sizeof($tmponnewValues)>0) $onnewValues = $tmponnewValues;
			}

			for ($i=0;$i<$pl->getNumFields();$i++) {
				if ($onnewValues) {
					if(!in_array($pl->fields[$i]->getSQLFLD(),$onnewValues)) continue;
				}
				
				if ($pl->fields[$i]->getIndex() ==  $this->dir_fields['::main']['parent_id']) continue;
				
				if ($pl->fields[$i]->getType()===false) continue;
				$field = array();

				if(isset($fldFechaCalendar) && $pl->fields[$i]->getIndex() == $fldFechaCalendar){
					/*Campo especial para módulo calendario que marca la fecha seleccionada y que no se dibuja...*/
					$field['alias'] = $pl->fields[$i]->getAlias();
					$field['noEdit'] = false;
					$field['name'] = $pl->fields[$i]->getSQLFLD();

					$field['fplt'] = $pl->fields[$i]->getfPlt();

					$field['ftype'] = "text";
					$field['req'] = false;
					$field['data'] = '<input type="hidden" name="'.$pl->fields[$i]->getSQLFLD().'" value="'.implode("/",explode("-",$fechaCalendar)).'" />';
					$field['title'] = "Nuev".$pl->getGenero()." ".$pl->getEntidad()." para el ".implode("/",explode("-",$fechaCalendar));
					$fields[] = $field;
					continue;
				}
				
				$field['alias'] = $pl->fields[$i]->getAlias().(($pl->fields[$i]->iscloneInfo())? ' <small>'.$pl->fields[$i]->iscloneInfo().'</small>':'');
				if(in_array($pl->fields[$i]->getSQLFLD(),$noEdit)){
					$field['noEdit'] = true;
				}else{
					$field['noEdit'] = false;
				}
				$field['name'] = $pl->fields[$i]->getSQLFLD();

				$field['fplt'] = $pl->fields[$i]->getfPlt();

				$field['ftype'] = $pl->fields[$i]->getType();
				$field['req'] = $pl->fields[$i]->isRequired();
				if(method_exists($pl->fields[$i],"getDefault")){
					$defaultVal = $pl->fields[$i]->getDefault();
				}else{
					$defaultVal = "";
				}
				
				if ($edit){
					
					$defaultVal= $results[$pl->fields[$i]->getAlias()];
				}
				
				$field['data'] = $pl->fields[$i]->drawTableValueEdit($defaultVal);
				$field['textoAyuda'] = $pl->fields[$i]->getDescriptionTextForField();
				$fields[] = $field;
			}
			die(json_encode(array("error"=>0,"fields"=>$fields,"ofields"=>$oFields,'opl'=>$tmpopl)));
		}
	}
	
    protected function update_folder(){
        
        $this->save_folder(true);
    }
    
    protected function update_file(){
        
        $this->save_folder(true,true);
    }    
	
	
    protected function remove_folder(){
        
        $this->request_dir = mysql_real_escape_string($_GET['rel']);
                
        $aDir = explode("/",$this->request_dir); 
    
        $dir = $aDir[sizeof($aDir)-2];

        $conds = " where ".$this->dir_fields['::main']['url']." = '".$dir."'";
        
        if ($this->dir_fields['::main']['deleted']){
        
            new con("update ".$this->dir_fields['::main']['tab']." set ".$this->dir_fields['::main']['deleted']."='1' ".$conds);
        }else{
            
            new con("delete from ".$this->dir_fields['::main']['tab']." ".$conds);  
        }
        
        die(json_encode(array('error'=>0)));
    }
    
    protected function remove_file(){

        if (isset($_GET['file']) && $file_get_data = explode("::",$_GET['file'],2)){
            
            $file_id = $file_get_data[1];
            
            if ($this->file_fields['::main']['deleted']){
                
                $sql = "update ".$this->file_fields['::main']['tab']." set 
                
                    ".$this->file_fields['::main']['deleted']."='1' where  ".$this->file_fields['::main']['id']." = ".(int)$file_id." ";
                
                $c = new con($sql);
                
            }else{
                
                //TODO
            }
            
            
        }

        die(json_encode(array('error'=>0)));

    }    
	
	
	protected function save_folder($update = false,$file=false){
		
		/*
		 * 
		 * tablon
		 */
		
		/*** Cambios para reutilizar el módulo de nuevo desde otros modulos que hereda de tablon pero que necesitan algún cambio ******/
		$pltEdit = false;
		if ($file){
		    $plantilla = $this->rutaPlantillas.$this->file_plt;
		}else{
		    $plantilla = $this->rutaPlantillas.$this->dir_plt;
		}
		
		
		{
			
			$pl = new tablon_plantilla($plantilla);

			$fields = array();
			$conds = array();
				/*LANDER TABLONONNEW*/
			$onnewValues=false;

			if ($pl->onnew) $onnewValues = $pl->onnew;

			if (isset($_GET['tablononnew'])){
				$tmponnewValues = explode(',',$_GET['tablononnew']);
				if (sizeof($tmponnewValues)>0) $onnewValues = $tmponnewValues;
			}


			for($i=0;$i<$pl->getNumFields();$i++) {
				
				if(in_array($pl->fields[$i]->getSQLFLD(),$noEdit)){
					continue;
				}
				$fl = $pl->fields[$i];
				if ($onnewValues) {
					if(!in_array($pl->fields[$i]->getSQLFLD(),$onnewValues)) continue;
				}
				$constType = $fl->getConstantTypeAjaxUpload();
                $constType = ($constType == "_POST")? "_GET":$constType;

				$fldName = "FLD__".$fl->getSQLFLD();
				$aValues = $_GET;

				if(isset($fldFechaCalendar) && $fldFechaCalendar == $fl->getSQLFLD()){
					/*Campo especial para módulo calendario que marca la fecha seleccionada y que no se devuelve como los demás...*/
					$fields[$fl->getSQLFLD()] = $fl->getMysqlValue($aValues['idCalendar']);
					continue;
				}

				if($pl->fields[$i]->isRequired() && (!isset($aValues[$fldName]) || empty($aValues[$fldName]))){
					if(isset($aValues["NoReqDepend__".$fl->getSQLFLD()]) && $aValues["NoReqDepend__".$fl->getSQLFLD()] == "seguir"){
						// Si es un campo dependiente de otro, y en este caso aunque el plt marque como requerido, no es necesaria.
						continue;
					}else
						die(json_encode(array("error"=>"Req","errorStr"=>"Valor requerido para ".$constType.$fl->getSQLFLD())));
				}
				
				if ($pl->fields[$i]->getIndex() ==  $this->dir_fields['::main']['parent_id']) {
					
					$this->request_dir = mysql_real_escape_string($_GET['rel']);
					
					if ($this->request_dir=="root/"||$this->request_dir=="root_dir/"){
						
						
					}else{
						$aValues[$fldName] = $this->get_id_parent();
					}
				}

				if((!isset($aValues[$fldName]) || $aValues[$fldName] === '')){
					continue;
				}

				if (method_exists($fl,"preInsertCheckValue")) {

					if (($ret = $fl->preInsertCheckValue($aValues[$fldName]))!==true) {

						if ((!is_array($ret)) || ((isset($ret[0])) && ($ret[0]==0))) {
							continue;
						} else {
							die(json_encode(array("error"=>$ret[0],"errorStr"=>$ret[1])));
						}
					}
				}
				$fields[$fl->getSQLFLD()] = ($constType!="_FILES")? $fl->getMysqlValue(($aValues[$fldName])):$fl->getMysqlValue($aValues[$fldName]);
				if($fields[$fl->getSQLFLD()] == "noInsertBecauseFileEsp"){
					$fields[$fl->getSQLFLD()] = $aValues[$fldName];
				}
				for($j=0;$j<$fl->sizeofsubFields;$j++) {
					$ret = $pl->saveSingleField($i,$aValues[$fldName],false,$j);
					if (is_array($ret)) {
						$fields[$ret[0]] = $ret[1];
					}

				}
			}
			foreach($_GET as $idx=>$value) {
				if (preg_match("/^COND__(.*)/",$idx,$fldNameH)){
					$conds[$fldNameH[1]] = stripslashes($value); /*var_dump($fldName[1],$value); LANDER*/
										//var_dump(stripslashes($value));
				}
				if (preg_match("/^CONDT__(.*)/",$idx,$fldNameH)){
					$conds['triggerCond_'.$fldNameH[1]] = stripslashes($value);
				}
			}

			if ($update){
				
			    if ($file){

			        $file_get_data = explode("::",$_GET['file'],2);
            
                    $file_id = $file_get_data[1];
            
			        $conds = " where ".$this->file_fields['::main']['id']." = '".$file_id."'";
                    
                    $ret = $pl->updateRow($fields,$conds);
                    
			    }else{
			    
    				$this->request_dir = mysql_real_escape_string($_GET['rel']);
    				
    				$aDir = explode("/",$this->request_dir); 
    			
    				$dir = $aDir[sizeof($aDir)-2];
    				
    				$conds = " where ".$this->dir_fields['::main']['url']." = '".$dir."'";
    				
    				unset($fields[$this->dir_fields['::main']['parent_id']]);
    				
    				$ret = $pl->updateRow($fields,$conds);
				
				
			    }
			}else{
				
				$ret = $pl->newRow($fields,$conds);
			}

			if (!is_array($ret)) {
				if($pltEdit){
					$retFields = array();
					$plantillaS = tablon_AJAXjeditable::setPlantillaPath($campos[3]);
					$plShow = new tablon_plantilla($plantillaS);
					for ($i=0;$i<$plShow->getNumFields();$i++) {
						$sql = $plShow->loadSingleField($i,$ret);
						$retFields[basename($plShow->getFile()).'::'.$plShow->fields[$i]->getSQLFLD().'::'.$ret] = rawurlencode($plShow->fields[$i]->drawTableValue($plShow->fields[$i]->getValue()));
						for ($j=0;$j<$plShow->fields[$i]->sizeofsubFields;$j++) {
							$retFields[basename($plShow->getFile()).'::'.$plShow->fields[$i]->subFields[$j]->getSQLFLD().'::'.$ret] = urldecode($plShow->fields[$i]->subFields[$j]->drawTableValue($plShow->fields[$i]->subFields[$j]->getValue()));
						}
					}
					$arr=array();
					foreach ($retFields as $id=>$vl){
						if(!i::detectUTF8($vl))
							$vl = utf8_encode($vl);
						$arr[$id] = rawurldecode($vl);
					}
					if(is_array($aRefresh) && !empty($aRefresh) && in_array('insert',$aRefresh)){
						die(json_encode(array("error"=>0,"idTR"=>basename($plShow->getFile()).'::'.$ret,"id"=>$ret,"values"=>$arr,"refreshAfter"=>true,"defaultFLD"=>basename($pl->getFile()).'::'.$pl->getDefaultFLD()."::".$ret)));
					}
	 				die(json_encode(array("error"=>0,"idTR"=>basename($plShow->getFile()).'::'.$ret,"id"=>$ret,"values"=>$arr,"defaultFLD"=>basename($pl->getFile()).'::'.$pl->getDefaultFLD()."::".$ret)));
				}else{
					$retFields = array();
					$refrescar = $refrescarE = false;
					$aRefresh = $pl->getRefreshwhen();
					$aNotify = $pl->getNotifyWhenInsert();
					$notifyStr = ($aNotify == false)? false:(($pl->getNotifyWhenInsertStr())? $pl->getNotifyWhenInsertStr():'');

					for ($i=0;$i<$pl->getNumFields();$i++) {
						if ($pl->fields[$i]->getConstantTypeAjaxUpload()=="_FILES"){
							for ($j=0;$j<$pl->fields[$i]->sizeofsubFields;$j++) {
								if($pl->fields[$i]->subFields[$j]->getRealType() == "IMG_NAME") {
									$retFields[basename($pl->getFile()).'::'.$pl->fields[$i]->getSQLFLD().'::'.$ret] = rawurlencode($pl->fields[$i]->drawTableValue($pl->fields[$i]->subFields[$j]->getValue()));
								}
								if($pl->fields[$i]->subFields[$j]->getRealType() == "FILE_NAME") {
									$retFields[basename($pl->getFile()).'::'.$pl->fields[$i]->getSQLFLD().'::'.$ret] = rawurlencode($pl->fields[$i]->drawTableValue($pl->fields[$i]->subFields[$j]->getValue()));
								}
							}
						}else{
							$constType = &$pl->fields[$i]->getConstantTypeAjaxUpload();
							$aValues = &$$constType;
							
							$vlr_para_drawTabValue = (!isset($aValues['FLD__'.$pl->fields[$i]->getSQLFLD()]))? NULL:$aValues['FLD__'.$pl->fields[$i]->getSQLFLD()];
							$r = rawurldecode($pl->fields[$i]->drawTableValue($vlr_para_drawTabValue ,$ret ,$pl ));
							$retFields[basename($pl->getFile()).'::'.$pl->fields[$i]->getSQLFLD().'::'.$ret] = $r;

							if ($aNotify!== false) {
								if (in_array($pl->fields[$i]->getSQLFLD(),$aNotify)) {
									if ($pl->getNotifyWhenInsertStr()) $notifyStr = str_replace("%".$pl->fields[$i]->getSQLFLD()."%",$r,$notifyStr);
									else $notifyStr .= $pl->fields[$i]->getTitle() .": ".$r."<br />";
								}
							}							
							

					


						}

						for ($j=0;$j<$pl->fields[$i]->sizeofsubFields;$j++) {
							$retFields[basename($pl->getFile()).'::'.$pl->fields[$i]->subFields[$j]->getSQLFLD().'::'.$ret] = urldecode($pl->fields[$i]->subFields[$j]->drawTableValue($pl->fields[$i]->subFields[$j]->getValue()));
						}
						if (!isset($id)) $id = NULL; //Evitar Warning... no se que hace...			
						if($pl->ifFieldRefres($i,$id,'insert') === true){
							if($pl->ifRefreshwhen('insert') === true){
								$refrescar = true;
							}
						}
						if($pl->ifFieldRefres($i,$id,'error') === true){
							if($pl->ifRefreshwhen('error') === true){
								$refrescarE = true;
							}
						}
					}

					$arr=array();
					foreach ($retFields as $id=>$vl){
						if(!i::detectUTF8($vl))
							$vl = utf8_encode($vl);
						$arr[$id] = ($vl);
					}

					if(is_array($aRefresh) && !empty($aRefresh) && in_array('insert',$aRefresh)){
						$refrescar = true;
					}

					die(
							json_encode(
									array(
										"error"=>0,
										"idTR"=>basename($pl->getFile()).'::'.$ret,
										"id"=>$ret,
										"values"=>$arr,
										"notifywheninsert"=>$notifyStr,
										"refreshAfter"=>$refrescar,
										"defaultFLD"=>basename($pl->getFile()).'::'.$pl->getDefaultFLD()."::".$ret
									)
							)
					);
				}
			} else {
				$aRefresh = $pl->getRefreshwhen();
				if(is_array($aRefresh) && !empty($aRefresh) && in_array('error',$aRefresh))
					die(json_encode(array("error"=>$ret[0],"errorStr"=>$ret[1],"refreshAfter"=>true)));
				else
					die(json_encode(array("error"=>$ret[0],"errorStr"=>$ret[1])));

			}		
		}
		
	}
	
	
	
	
	
	protected function download(){
		
		if (isset($_GET['file']) && $file_get_data = explode("::",$_GET['file'],2)){
			
			$file_id = $file_get_data[1];

			$c = new con("select * from ".$this->file_fields['::main']['tab'].' where '.$this->file_fields['::main']['id']." = ".(int)$file_id." ");
			
			$r = $c->getResult();
			
/*			var_dump(
				$r[$this->file_fields['::main']['parent_id']], 
				$r[$this->file_fields['::main']['url']],
				$r[$this->file_fields['::main']['name']],
				$r[$this->file_fields['::main']['size']],
				$r[$this->file_fields['::main']['mimetype']],
				$r[$this->file_fields['::main']['upload_dir_file']],
				$this->uploads_dir.$r[$this->file_fields['::main']['upload_dir_file']].'/'.$r[$this->file_fields['::main']['url']]
			);
	*/		
			ob_end_clean();
			

			
			header("Content-type: ".$r[$this->file_fields['::main']['mimetype']]);
			header("Content-Disposition: attachment; filename=\"".$r[$this->file_fields['::main']['name']]."\";");
			header("Content-Length: ".filesize($this->uploads_dir.$r[$this->file_fields['::main']['upload_dir_file']].'/'.$r[$this->file_fields['::main']['url']]));
			
			readfile($this->uploads_dir.$r[$this->file_fields['::main']['upload_dir_file']].'/'.$r[$this->file_fields['::main']['url']]);
			
			exit();
		}
	}
	
	public function ajax(){
		
		ob_end_clean();

		switch($_GET['action']){
			
			case 'vhd_file_tree':
				
				$this->request_dir = urldecode($_POST['dir']);
				
				$this->draw_dir();
			break;
			
			case 'vhd_file_tree_subaction':
				
				switch ($_GET['subaction']){
				
					case "new_folder":
						
						if (isset($_GET['sop']) && $_GET['sop']=="new_folder"){
							
							$this->save_folder();
						}
						
						$this->new_folder_prompt();
					break;
					
					case "new_file":

						$this->save_files();
					break;

					case "download":

						$this->download();
					break;
										
					case "edit":

					    
					    
					    
                        if (isset($_GET['file']) && $file_get_data = explode("::",$_GET['file'],2)) {

                            $file_id = $file_get_data[1];
                            
                            if (isset($_GET['sop']) && $_GET['sop']=="edit") {
                                
                                $this->update_file();
                            }
                            
                            $this->new_folder_prompt(true,$file_id);
                            
                        } else {
                            
                            if (isset($_GET['sop']) && $_GET['sop']=="edit") {
                                
                                $this->update_folder();
                            }           

                            $this->new_folder_prompt(true);
                        }					    
					    
					    
						
					break;
					
                    case "delete":
                        
                        if (isset($_GET['sop']) && $_GET['sop']=="delete"){
                        
                            $this->remove_folder();
                        }else{
                            
                            die(json_encode(array("error"=>0)));
                        }
                    break;
                    
                    case "delete_file":
                        
                        if (isset($_GET['sop']) && $_GET['sop']=="delete_file"){
                        
                            $this->remove_file();
                        }else{
                            
                            die(json_encode(array("error"=>0)));
                        }
                    break;                    
				}
			break;
		}
		
		exit();
	}
	
	protected function HandleError($message) {
		
		echo $message;
	}
	
	protected function save_files(){
		/*
		 * 
		 * SWF UPLOAD 
		 * 
		 */
	// Check post_max_size (http://us3.php.net/manual/en/features.file-upload.php#73762)
		$POST_MAX_SIZE = ini_get('post_max_size');
		$unit = strtoupper(substr($POST_MAX_SIZE, -1));
		$multiplier = ($unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1)));
	
		if ((int)$_SERVER['CONTENT_LENGTH'] > $multiplier*(int)$POST_MAX_SIZE && $POST_MAX_SIZE) {
			header("HTTP/1.1 500 Internal Server Error"); // This will trigger an uploadError event in SWFUpload
			echo "POST exceeded maximum allowed size.";
			exit(0);
		}
	
	// Settings
		//$save_path = getcwd() . "/uploads/";				// The path were we will save the file (getcwd() may not be reliable and should be tested in your environment)
		$upload_name = "Filedata";
		$max_file_size_in_bytes = 2147483647;				// 2GB in bytes
		$extension_whitelist = array("jpg", "gif", "png");	// Allowed file extensions
		$valid_chars_regex = '.A-Z0-9_ !@#$%^&()+={}\[\]\',~`-';				// Characters allowed in the file name (in a Regular Expression format)
		
	// Other variables	
		$MAX_FILENAME_LENGTH = 260;
		$file_name = "";
		$file_extension = "";
		$uploadErrors = array(
	        0=>"There is no error, the file uploaded with success",
	        1=>"The uploaded file exceeds the upload_max_filesize directive in php.ini",
	        2=>"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
	        3=>"The uploaded file was only partially uploaded",
	        4=>"No file was uploaded",
	        6=>"Missing a temporary folder"
		);
	
	
	// Validate the upload
		if (!isset($_FILES[$upload_name])) {
			$this->HandleError("No upload found in \$_FILES for " . $upload_name);
			exit(0);
		} else if (isset($_FILES[$upload_name]["error"]) && $_FILES[$upload_name]["error"] != 0) {
			$this->HandleError($uploadErrors[$_FILES[$upload_name]["error"]]);
			exit(0);
		} else if (!isset($_FILES[$upload_name]["tmp_name"]) || !@is_uploaded_file($_FILES[$upload_name]["tmp_name"])) {
			$this->HandleError("Upload failed is_uploaded_file test.");
			exit(0);
		} else if (!isset($_FILES[$upload_name]['name'])) {
			$this->HandleError("File has no name.");
			exit(0);
		}
		
		
	// Validate the file size (Warning: the largest files supported by this code is 2GB)
		$file_size = @filesize($_FILES[$upload_name]["tmp_name"]);
		if (!$file_size || $file_size > $max_file_size_in_bytes) {
			$this->HandleError("File exceeds the maximum allowed size");
			exit(0);
		}
		
		if ($file_size <= 0) {
			$this->HandleError("File size outside allowed lower bound");
			exit(0);
		}
	
	
	// Validate file name (for our purposes we'll just remove invalid characters)
		$file_name = preg_replace('/[^'.$valid_chars_regex.']|\.+$/i', "", basename($_FILES[$upload_name]['name']));
		if (strlen($file_name) == 0 || strlen($file_name) > $MAX_FILENAME_LENGTH) {
			$this->HandleError("Invalid file name");
			exit(0);
		}
	
	
		$this->uploads_dir = dirname(__FILE__).'/../../../'.$this->selectedConf['upload_dir'].'/';
		
		//var_dump($this->file_fields);
		
		$atmp = explode(".",$file_name);
		
		$sizeatmp = sizeof($atmp);
		
		$extension = $atmp[$sizeatmp-1];
		
		unset($atmp[$sizeatmp-1]);
		
		$tmpfilename = implode(".",$atmp); 
		
		$clean_file_name = i::clean($tmpfilename);
		
		$mime_type = i::mime_content_type($_FILES[$upload_name]["tmp_name"]);
		
		$file_size = i::tamFich($file_size);
		
		$file_date_year = date('Y');
		$file_date_month = date('m');
		$file_date_day = date('d');
		
		$file_date = array($file_date_year,$file_date_year.'/'.$file_date_month,$file_date_year.'/'.$file_date_month.'/'.$file_date_day);
		
		if (!is_dir($this->uploads_dir)){
			$this->HandleError("El directorio UPLOADS no existe.");
			exit(0);
		}
		
		foreach ($file_date as $date){
		
			$dir = $this->uploads_dir.$date;
			
			if ( !is_dir($dir) ){
				
				if (!@mkdir($dir,0777)){
					
					$this->HandleError("El directorio UPLOADS no tiene los permisos necesarios.");
					exit(0);
				}
			}
		}
		
		$upload_dir_file = $file_date_year.'/'.$file_date_month.'/'.$file_date_day;
		
		$name = $clean_file_name.'.'.$extension;
		
		$nombreTemp  = $name;
		
		$cont = 0;
		do {
	 		$sql = 'select '.$this->file_fields['::main']['url'].' as url from '.$this->file_fields['::main']['tab'].' 
	 				where '.$this->file_fields['::main']['url'].'= \''.$name.'\'';

	 		$con = new con($sql);
	 		if ($con->getNumRows()==0) break;
	 		$name = preg_replace("/^([^\.]*)/","\\1_".$cont++,$nombreTemp);
	 	} while (1);
		
		$this->request_dir = mysql_real_escape_string($_GET['rel']); 
		
		$parent_id = (int) $this->get_id_parent();
		
		$tmp = false;
		
	 	if (isset($this->file_fields['::main']['oninsert'])) {
	 	    
	 	    $tmp = explode("=",$this->file_fields['::main']['oninsert'],2);
	 	    
	 	
	 	    
	 	}
	 	
		$sql = "insert into ".$this->file_fields['::main']['tab']." 
			(".$this->file_fields['::main']['parent_id'].", 
			".$this->file_fields['::main']['url'].",
			".$this->file_fields['::main']['name'].",
			".$this->file_fields['::main']['size'].",
			".$this->file_fields['::main']['mimetype'].",
			".$this->file_fields['::main']['upload_dir_file']."
			".(($tmp)? ",".$tmp[0]:"" )."
			)
			values
			(".(($parent_id==0)? "NULL": "'".$parent_id."'")."
			,'".$name."','".$file_name."','".$file_size."','".$mime_type."','".$upload_dir_file."'".(($tmp)? ",".$tmp[1]:"" ).")
			";
		
		
		$con = new con($sql);
		
		if (!@move_uploaded_file($_FILES[$upload_name]["tmp_name"], $this->uploads_dir.$upload_dir_file.'/'.$name)) {
			$this->HandleError("File could not be saved.");
			exit(0);
		}
		echo "Complete";
		exit(0);
	}
	
	
	
}


?>
