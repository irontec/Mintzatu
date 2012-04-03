<?php

class csvimport extends tablon
{

    const CFG_defaultDelimiter = 'default_delimiter';
    const CFG_defaultEnclosure = 'default_enclosure';
    private $_l;
    private $_tabName;
    private $_idFld;
    private $_requiredFlds;
    private $_defaultEnclosure; // Se puede setear por CFG
    private $_defaultDelimiter;// Se puede setear por CFG

    function __construct($conf)
    { // TOCHECK he quitado & de $conf
        $karmaRegistry = KarmaRegistry::getInstance();
		$this->_l = new k_literal($karmaRegistry->get('lang'));
        $this->rutaPlantillas = "".dirname(__FILE__)."/../../../configuracion/tablon/";
        $this->conf = $conf;
        $this->fixOps();
        $this->setCurrentSection();

        //$this->aJs[] = "../modules/tablon/scripts/date.js";
        //$this->aJs[] = "../modules/tablon/scripts/jquery.autocomplete.js";
        ////$this->aJs[] = "../modules/tablon/scripts/jquery.datePicker.js";
        //$this->aJs[] = "../modules/tablon/scripts/tablon_dateformat.js";
        //$this->aJs[] = "../modules/tablon/scripts/jqueryMultiSelect.js";
        //$this->aCss[] = "../modules/tablon/css/jqueryMultiSelect.css";
        $this->aJs[] = "../modules/csvimport" . "scripts/jquery-1.3.2.min.js";
        //$this->aJs[] = "../modules/csvimport/" . "scripts/jquery.form.js";
        $this->aJs[] = "../modules/csvimport/" . "scripts/jq.ajaxfileupload.js";
        //$this->aJs[] = "../modules/csvimport/"."scripts/jquery.form.wizard-2.0.0-RC4-min.js";
        //$this->aJs[] = "../modules/csvimport/" ."scripts/jquery.history.js";
        //$this->aJs[] = "../modules/csvimport/" ."scripts/jquery.validate.js";

        $this->aCss[] = "../modules/csvimport/" ."css/csvimport.css";
        $this->aJs[] = "../modules/csvimport/" ."scripts/csvimport.js";


        if (isset($this->conf[$this->currentSection]['oJs'])) {
            $this->aJs[] = $this->conf[$this->currentSection]['oJs'];
        }
        $this->_requiredFlds = array();
        $this->_defaultEnclosure = '"';
        $this->_defaultDelimiter = ',';
        $this->_getFldsAndSetDefaults();
    }


    private function _getFldsAndSetDefaults()
    {

        $this->getPlt();
        if (isset($this->selectedConf[self::CFG_defaultEnclosure]) && $this->selectedConf[self::CFG_defaultEnclosure] != '') {
            $this->_defaultEnclosure = $this->selectedConf[self::CFG_defaultEnclosure];
	}
        if (isset($this->selectedConf[self::CFG_defaultDelimiter]) && $this->selectedConf[self::CFG_defaultDelimiter] != '') {
            $this->_defaultDelimiter = $this->selectedConf[self::CFG_defaultDelimiter];
	}
	//phpinfo(); //error_log ( var_dump ( $this->_defaultEnclosure , true), 3, "/tmp/error_log");
        $ini = parse_ini_file($this->getPlt(), true);
        $arr = array();
        foreach ($ini as $key=>$value) {
            if ($key != '::main') {
                if (!isset($value['csvimport_ignore'])) { //Es un campo a ignorar en el importador
                    $arr[] = $key;
                    if (isset($value['req'])) {
                        $name = $key;
                        if (isset($value['alias'])) {
                            $name = $value['alias'];
                        }
                        $this->_requiredFlds[] = $name;
                    }
                }

            } else {
                $this->_tabName = $value['tab']; //TODO Hace mas cosas y el nombre de metodo no lo refleja
                $this->_idFld = $value['id'];
            }
        }
        return implode(',', $arr);

    }

    private function _getAliases()
    {

        $this->getPlt();
        $ini = parse_ini_file($this->getPlt(), true);
        $arr = array();
        foreach ($ini as $key=>$value) {
            if ($key != '::main') {
                if (!isset($value['csvimport_ignore'])) {
                    $add = $key;
                    if (isset($value['alias'])) {
                        $add = $value['alias'];
                    }
                    $arr[] = $add;
                }
            }

        }
        return implode(',', $arr);

    }




    public function draw()
    {
        //$name =   $_FILES['name']['name'];

        //if ($name == null || $name == '') {

        echo $this->drawTitle();
        if (!$plantilla = $this->getPlt()) {
            iError::error("Plantilla no encontrada");
            return false;
        }

        $this->plantillaPrincipal = new tablon_plantilla($plantilla);
        $pl = $this->plantillaPrincipal; //puajj


        $multiops = ( ($this->selected_have_opts_tablon_edit()) || (isset($this->selectedConf['del'])));
        $csv = ((isset($_GET['CSV'])) && ($_GET['CSV']=="1"));
        $generalops = ( (sizeof($this->history)>0) || (isset($this->selectedConf['new'])));
        /*filtrado¿?*/
        $sql = $this->doSQL();

?>
       <div id="busy" class="busy"><img src="modules/csvimport/css/ajax-loader.gif"></div>
       <div id="paso1" class="paso step">
<form enctype="multipart/form-data" id="csvimportpre" method="post" action='modules/csvimport/ajax/ops_csvimport.php?upload'>

         <h2>Paso 1 - Subir fichero CSV</h2>
        <br/>
        <table class="tablon">
        <tbody>
        <tr>
            <td class="cabecera">
        Fichero
        </td>
        <td class="tablon_edit">
         <input id="file_csv" type="file" name="file_csv" value="" />
        </td>
        </tr>
        </tbody>
        </table>
        <input id="csv_upload" class="button_submit" type="submit" value="Siguiente (Configuración)"/>
        </form>
        </div>

       <form  enctype="multipart/form-data" id="csvimport" method="post" action="modules/csvimport/ajax/ops_csvimport.php">
       <div id="paso2" class="paso step">

        <h2>Paso 2 - Configuración</h2>
        <br />
         <table class="tablon" >
         <tbody>
         <tr>
            <td class="cabecera">
         Separador de campo
            </td>
            <td class="tablon_edit">
            <input id="field_delimiter" name="field_delimiter" type="text" value="<?php echo $this->_defaultDelimiter; ?>" />
            </td>
         </tr>
         <tr>
            <td class="cabecera">
         Delimitador de texto
            </td>
            <td class="tablon_edit">
            <input id="enclosure" name="enclosure" type="text" value='<?php echo $this->_defaultEnclosure;?>' />
            </td>
        </tbody>
        </table>
        <p>Ejemplo:</p>
        <pre>"1","Jon","Lopez","Avda Murrieta 50, B 2ºD"</pre>
        <p>En este caso los campos están separador por comas (,) y el texto está delimitado por comilla doble (")</p>
            <br/>
        <table class="tablon">
        <tbody>
        <tr>
        <td class="cabecera">
        Tiene cabecera (1ª fila)
        </td>
        <td class="tablon_edit">
        <input type="checkbox" name="has_header"/>
        </td>
        </tr>
        </tbody>
        </table>
        <input type="hidden" name="flds" value="<?php echo $this->_getFldsAndSetDefaults() ?>"/>
        <input type="hidden" name="req_flds" value="<?php echo implode(',', array_unique($this->_requiredFlds)); ?>"/>
        <input type="hidden" name="aliases" value="<?php echo $this->_getAliases() ?>"/>
        <input type="hidden" name="table_name" value="<?php echo $this->_tabName; ?>"/>
        <input type="hidden" name="id_fld" value="<?php echo $this->_idFld; ?>"/>
        <br/><br/><input class="button_submit" id="button_submit_conf" type="submit" value="Siguiente (asignar campos)"/>
       </div>

<div id="preview" class="step submit_step">
</div>
<div id="rollback" class="step submit_step">
</div>
<div id="do_commit" class="step submit_step">
</div>
     </form>


<?php


    }



}


//EOF
