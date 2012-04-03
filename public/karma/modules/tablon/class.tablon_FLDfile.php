<?php
class tablon_FLDfile_SIZE extends tablon_FLDsafetext
{
        public function getMysqlValue($value)
        {
            if (isset($value['size'])) {
                $this->setValue($value['size']);
                return "'{$value['size']}'";
            }
            return "''";
        }

        public function drawTableValue($v)
        {
            return i::tamFich($v);
        }
}

class tablon_FLDfile_TYPE extends tablon_FLDsafetext
{
        public function getMysqlValue($value)
        {
            if (isset($value['type'])) {
                $this->setValue($value['type']);
                return "'{$value['type']}'";
            }
            return "''";
        }

        public function drawTableValue($v)
        {
            return $v; //i::mime_content_type()
        }
}

class tablon_FLDfile_NAME extends tablon_FLDsafetext
{
        function __construct($conf, $idx, $plt = false)
        {
            parent::__construct($conf, $idx, $plt);
            $this->unique = true;
        }

        public function getMysqlValue($value)
        {
            if (isset($value['name'])) {
                $this->setValue($value['name']);
                return "'{$value['name']}'";
            }
            return "''";
        }
}

class tablon_FLDfile extends tablon_FLD
{
    public $file_name = false;
    public $file_size = false;
    public $file_type = false;
    public $data_path = false;

    public $ruta = false;
    public $tmpruta = false;

    public function getType() {
        return "ajaxfileupload";
    }

    public function getSQLType() {
        //return "mediumblob".$this->getSQLRequired();;
    }

    public function isfilesystem()
    {
        if (isset($this->conf['filesystem'])) {
            $this->data_path = $this->conf['filesystem'];
            return true;
        } else {
            return false;
        }
    }

    public function registerCustomSubField($type, $idSubField)
    {
        switch ($type) {
            case "FILE_NAME":
                $this->file_name = $idSubField;
                break;
            case "FILE_TYPE":
                $this->file_type = $idSubField;
                break;
            case "FILE_SIZE":
                $this->file_size = $idSubField;
                break;
        }

    }

    public function getMysqlValue($value)
    {
        $c = new con("select 1;");
        return 'noInsertBecauseFile';
        //return 'compress(\''.$this->cleanMysqlValue(file_get_contents($value['tmp_name'])).'\')';
    }

    public function getSQLFLDRequest()
    {
        return $this->subFields[$this->file_name]->getSQLFLD();
    }

    public function preInsertCheckValue(&$value)
    {
        // Necesito el nombre de la tabla de la plantilla
        if (!file_exists(RUTA_PLANTILLAS.$this->plt)) {
            return false;
        }
        $aFields = parse_ini_file(RUTA_PLANTILLAS.$this->plt, true);



        if ((!is_array($value)) || ($value['name']=="")) {
            if ($this->isRequired())
                return array("1","No se ha subido.");
            else
                return true;
        }

        /*if ((!is_array($value)) || ($value['name']=="") ) {//  || (!file_exists($value['tmp_name']))) {
            return array("1","No se ha subido.");
        }*/

        $nombreTemp  = $value['name'];
        $cont = 0;

        $field = $this->subFields[$this->file_name]->getSQLFLD();

        do {
             $sql = 'select '.$field.' as url from '.$aFields['::main']['tab'].' where '.$field.'= \''.$value['name'].'\'';
            $con = new con($sql);
            if ($con->error()) return array("1","Error indeterminado antes de guardar");
             if ($con->getNumRows()==0) break;
            $value['name'] = preg_replace("/^([^\.]*)/", "\\1_".$cont++, $nombreTemp);
        } while (1);
        return true;

    }

    public function afterupdate($value, $id, $tab)
    {
        if ($value['size']<=0) return false;
        $cmpname = $tab."_data_".$id;
        $fname = $value['tmp_name'];
        $zcon = new con("delete from vertedero where id_fich='".$cmpname."'");
        $fp = fopen($fname, 'rb');

        while (!feof($fp)) {
            $data = con::escape(fread($fp, con::MAXSQL));
            $sql = "insert into vertedero(id_fich,data) values ('".$cmpname."',compress('".$data."'));";
            $con = new con($sql);
            unset($con);
        }

    }

    public function afterdelete($id, $tab, $delFld=false)
    {
        if ($delFld === false) {
            $cmpname = $tab."_data_".$id;
            $zcon = new con("delete from vertedero where id_fich='".$cmpname."'");
        } else {
            $zcon = new con("update vertedero set borrado = '1 where id_fich='".$cmpname."'");
        }
    }

    public function getConstantTypeAjaxUpload()
    {
        return "_FILES";
    }

    /* Aunque el resto de parámetros además de $value no se usan, hay que ponerlas por ser una clase que extiende a otra (FLD) */
    public function drawTableValueEdit($value, $clone=false, $disabled=false)
    {
        return
            '<div class="updatedValue">'. (($value!="")? $this->drawTableValue($value):''). '</div>'.
            '<input type="file" name="'.$this->getSQLFLD().'" id="'.$this->getSQLFLD() . i::random(4) .'" '.(($this->isRequired())? ' class="required"':'').'/>';
    }

    private function existe($n)
    {
        $aFields = parse_ini_file(RUTA_PLANTILLAS.$this->plt, true);
        $sql = 'select '.$aFields['::main']['id'].' from '.$aFields['::main']['tab'].' where '.$this->conf['name_fld'].'= \''.$n.'\'';

        $con = new con($sql);
        return ($con->getNumRows()!==0);
    }

    public function drawTableValue($v = false)
    {
        if ($v === false) {
            $v = $this->subFields[$this->file_name]->getValue();
        }

        return '<a href="../cache/tablon/file/'.$this->getPlt().'/'.$this->getSQLFLD().'/'.$v.'" >"'.$v.'"</a>';
    }

    public function processReturnJSONValue($v, $pl, $id, $edit=false)
    {
        $principal = '';
        if (isset($v['name'])) {
            $principal = $v['name'];
        }

        $ret = array(
            0 => 0,
            "principal" => rawurlencode($this->drawTableValue($principal)),
            "subfields" => array()
        );

        for ($i=0; $i < $this->sizeofsubFields; $i++) {
            $ret['subfields'][basename($pl->getFile()).'::'.$this->subFields[$i]->getSQLFLD().'::'.$id] = rawurlencode($this->subFields[$i]->drawTableValue($this->subFields[$i]->getValue()));
        }
        return $ret;
    }
}
