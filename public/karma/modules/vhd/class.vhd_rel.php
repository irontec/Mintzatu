<?php 
	




class vhd_rel extends vhd{
    
    protected $rel_fields;
	
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
        
        $this->rel_plt = $this->selectedConf['rel_plt'];
        
        $this->parse_dir_plt();
        
        $this->parse_file_plt();
        
        $this->parse_rel_plt();
        
        $this->uploads_dir = dirname(__FILE__).'/../../../'.$this->selectedConf['upload_dir'].'/';
        
    }

    protected function parse_rel_plt(){

        if (!$this->rel_fields = @parse_ini_file($this->rutaPlantillas.$this->rel_plt,true)){
            
            iError::error("Plantilla de ficheros no encontrada");
        }
    }    
    
    protected function get_rel_values($vars,$id){
        
        $sql =  "select ".$this->rel_fields['::main']['id']." as __ID ";
        $sql.= ", ".$this->rel_fields['::main']['field']." as __FIELD ";
        $sql.= "from ".$this->rel_fields['::main']['tab']." where ".$vars['id']." = ".$id." and  ".$this->rel_fields['::main']['idcond']." = ".$this->currentValue." ";
        
        $c = new con($sql);
        
        if ($c->getNumRows()<=0) return false;
        
        $r = $c->getResult();
        
        return $r;
        
    }
    
    protected function set_perm(){
        
        
        if (isset($_GET['dir_action']) && ($tmp  = explode("_",$_GET['dir_action'],4))){
            
            
            
            $ret = $this->get_rel_values($this->dir_fields['::main'],$tmp[2]);
            
            if ($tmp[1]=="r") $perm = "1";
            
            if ($tmp[1]=="w") $perm = "2";
            
            if($ret){
                
                $relid = $ret['__ID'];
                
                if ($tmp[3]=="1"){
                
                        new con(" update ".$this->rel_fields['::main']['tab']." set ".$this->rel_fields['::main']['field']." = '".$perm."'  where ".$this->rel_fields['::main']['id']." = ".$relid."");
                
                }else{
                    
                    if ($perm=="2"){
                        new con(" update ".$this->rel_fields['::main']['tab']." set ".$this->rel_fields['::main']['field']." = '1'  where ".$this->rel_fields['::main']['id']." = ".$relid."");
                        
                    } else{
                        new con(" delete from ".$this->rel_fields['::main']['tab']."  where ".$this->rel_fields['::main']['id']." = ".$relid."");
                        
                    }
                    
                        
                }

                
            }else{
                
                
                if ($tmp[3]=="1"){
                    
                    $sql = "insert into ".$this->rel_fields['::main']['tab']." (".$this->rel_fields['::main']['idcond'].",".$this->dir_fields['::main']['id'].",".$this->rel_fields['::main']['field'].")";
                    
                    $sql.= " values ('".$this->currentValue."','".(int)$tmp[2]."','".$perm."')";
                    
                    new con($sql);
                }    
                
                
            }
            
        }
        
        echo json_encode(array('ret'=>1));
        exit(0);
    }
    
    protected function draw_perm_checkboxes($ret,$id){
        
        $r = false;
        $w = false;
        
        if (isset($ret['__FIELD'])){
            if ($ret['__FIELD']=="1"){
                $r = true;
                $w = false;
            }
            
            if ($ret['__FIELD']=="2"){
                $r = true;
                $w = true;
            }
            
        }
        
        $html = '<label >r<span>&nbsp;</span><input type="checkbox" name="read" '.(($r)? 'checked="checked"':'' ).' id="perm_r_'.$id.'" /></label>';
        
        $html.= '<label >w<span>&nbsp;</span><input type="checkbox" name="write" '.(($w)? 'checked="checked"':'' ).' id="perm_w_'.$id.'"  /></label>';
        
        return $html;
    }
    
    protected function draw_dir_field($dir){
                
        
        $dir_id  = $dir[$this->dir_fields['::main']['id']];
        
        $ret = $this->get_rel_values($this->dir_fields['::main'],$dir_id);

        //var_dump($this->rel_fields[$this->rel_fields['::main']['field']]);
        
        
        
        $options = '
            <span class="treeopts">'.$this->draw_perm_checkboxes($ret,$dir_id).'</span>
        ';
        
        
        echo "<li class=\"directory collapsed\" id=\"li__".$dir[$this->dir_fields['::main']['id']]."\">".$options."<a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $dir[$this->dir_fields['::main']['url']]) . "/\">" . htmlentities(utf8_decode($dir[$this->dir_fields['::main']['defaultFLD']])) . "</a></li>";
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

                    
                    case "set_perm":

                        $this->set_perm();
                    break;
                }
            break;
        }
        
        exit();
    }

	
}

?>