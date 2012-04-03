<?php
/**
 * Fichero de clase para campo tipo DATETIME
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 *
 * ::PLT::
 * nogmdate
 * separator
 * dateformat
 */

class tablon_FLDdatetime extends tablon_FLD
{
    public function __construct($conf, $idx, $plt = false) {
        parent::__construct($conf, $idx, $plt);

        if (!isset($this->conf['dateformat'])) {
            $this->conf['dateformat'] = 'dmy';
        }
    }


    public function getSQLType() {

        return
            "datetime not null default '0000-00-00 00:00:00' \n";
    }


    /*public function preInsertCheckValue($value){

        -TODO si schedule comprobar datos!!!

    }

    */

    public function getMysqlValue($value) {
        $c = new con("select 1;");
        $tmpVal  = explode(" ",$value,2);
        $tmp = explode($_SESSION['K_SEPARATOR'],$_SESSION['K_DATEFORMAT']);
        $tmp2 = explode($_SESSION['K_SEPARATOR'],$tmpVal[0]);
        foreach ($tmp as $i=>$variable){
            $$variable = $tmp2[$i];
        }
        list($H,$s) = explode(":",$tmpVal[1]);
        $timestamp = mktime($H,$s,0,$m,$d,$Y);
        $val = $timestamp;
        if(isset($this->conf['nogmdate']) && !empty($this->conf['nogmdate'])){
            $vl = date('Y-m-d H:i:s',tablon_FLD::cleanMySQLValue($val));
        }else{
            $vl = gmdate('Y-m-d H:i:s',tablon_FLD::cleanMySQLValue($val));
        }
        $this->setValue(tablon_FLD::cleanMySQLValue(date($_SESSION['K_DATETIMEFORMATNOSECS'],$val)));

        $value = date($_SESSION['K_DATETIMEFORMATNOSECS'],$val);

        return '\''.$vl.'\' ';


        /**
         * FIXME: Esto de aquí abajo no se ejecuta nunca?? Se supone que debería ejecutarse??
         */

    /*    if(isset($this->conf['separator']) && ($this->conf['separator'] == '-' || $this->conf['separator'] == '/')){
            $separador = $this->conf['separator'];
        }else{
            $separador = "/";
        }
            switch($this->conf['dateformat']){
                case "ymd":
                    //list($anio,$mes,$dia) = split('/',$value,3);
                    list($anio,$mes,$dia) = split($separador,$value,3);
                break;
                case "mdy":
                    //list($mes,$dia,$anio) = split('/',$value,3);
                    list($mes,$dia,$anio) = split($separador,$value,3);
                break;
                case "dmy":
                default:
                    //list($dia,$mes,$anio) = split('/',$value,3);
                    list($dia,$mes,$anio) = split($separador,$value,3);
                break;
            }
        */


        $val = mktime(0,0,0,$mes,$dia,$anio);




        //$vl = date('Y-m-d',tablon_FLD::cleanMySQLValue($val));
        //$this->setValue(tablon_FLD::cleanMySQLValue(date('Y-m-d',$val)));

        //var_dump($value);
        //$this->setValue(tablon_FLD::cleanMySQLValue($value));
        //return '\'$this->getValue().'\'';
        //echo $this->getValue().','.$this->getdateformat();
        //echo "<br />";

        //echo date($this->getdateformat(),$this->getValue());
        //echo strtotime($this->getValue());
        //echo "<br />";

        //$vl = date(" Y-m-d H:i:s",strtotime($this->getValue()));
        return '\''.$vl.'\' ';
    }
    public function makear($value) {
        $busco = explode($_SESSION['K_SEPARATOR'],$value,3);

        if(sizeof($busco)==3){
            //var_dump($value);
            $val = $value;
            $this->setValue(tablon_FLD::cleanMySQLValue($val));
            return $this->value;

        } else {

            /*
            * No entiendo para nada esto... sólo se que si la fecha viene vacía, seguramente es porque es un campo automático de mysql... (a lo insert date)...
            * En esos casos, el valor devuelto al onNew es un garete.... poniendo esta mini comprobación, parace que se arregla...
            * Si lo rompo, perdon... */
            if ($value == "") $value = time();

            $val = date($_SESSION['K_DATETIMEFORMATNOSECS'],$value);
            $this->setValue(tablon_FLD::cleanMySQLValue($val));
            return $val;

        }



        /*
        * Parche porque no funciona CORRECTAMENTE Al meter desde NEW... bastante garete
        * value se espera como AAAA-MM-DD, pero desde NEW llega como DD-MM-AAAA
        * se comprueba que viene como DD-MM-AAAA y se reescribe value para que tire
        * NO FUNCIONARÁ con MM-DD-AAAA :S
        */

        if (preg_match("/^([0-9]{2})([^0-9]{1})([0-9]{2})[^0-9]{1}([0-9]{4})$/",$value,$r)) {
                $value = $r[4].$r[2].$r[3].$r[2].$r[1];
        }


        /* FIN PARCHE */

        if(isset($this->conf['separator']) && ($this->conf['separator'] == '-' || $this->conf['separator'] == '/')){
            $separador = $this->conf['separator'];
        }else{
            $separador = "/";
        }
            if ($value==NULL) $value = "0000".$separador."00".$separador."00";


            switch($this->conf['dateformat']){
                case "ymd":
                    list($anio,$mes,$dia) = explode('-',$value,3);
                    $t = 'Y'.$separador.'m'.$separador.'d';
                break;
                case "mdy":
                    list($anio,$mes,$dia) = explode('-',$value,3);
                    $t = 'm'.$separador.'d'.$separador.'Y';
                break;
                case "dmy":
                default:

                    list($anio,$mes,$dia) = explode('-',$value,3);
                    $t = 'd'.$separador.'m'.$separador.'Y';
                break;
            }
        //return date($t,$val);

        if ((int)$mes == 0 && (int)$dia == 0 && (int)$anio == 0) {
            $val = '';
        } else {
            $val = date($t,mktime(0,0,0,$mes,$dia,$anio));
        }


        $this->setValue(tablon_FLD::cleanMySQLValue($val));

        return $this->value;
    }
    public function drawTableValue($value) {
        return $this->makear($value);

    }

    /**
     *  Aunque el resto de parámetros además de $value no se usan, hay que ponerlas por ser una clase que extiende a otra (FLD)
     */
    public function drawTableValueEdit($value,$clone=false,$disabled=false) {
        $this->setValue($value);

        $clase = "date-pickESP";
        $clase.=(($clone)? ' clone':'');
        $clase.=((isset($this->conf['req']) && $this->conf['req'] == "1")?" required":"");
        $clase = "class = '".$clase."'";
        $dt = $_SESSION['K_JSDATEFORMAT'];
        $html = '<input type="'.$this->getType()
              . '" id="' . $this->getSQLFLD().'_'.$this->getCurrentID()
              . '" datef="'.$dt
              . '" name="'.$this->getSQLFLD().(($clone)? '_clone':'')
              . '" ' . $clase
              . ' value="'. (($this->getValue() !== false)? $this->drawTableValue($this->getValue()):'')
              . '" />';


        return $html;
    }

    public function drawEditJSON() {
        //$this->setValue($value);
        $dt = $_SESSION['K_JSDATEFORMAT'];
        $aRet['datef'] = $dt;
        $aRet['name'] = $this->getSQLFLD();
        $aRet['theclass'] = "date-pickESP";
        if(isset($this->conf['req']) && $this->conf['req'] == "1"){
            $aRet['req']=true;
        }
        return json_encode($aRet);
    }

    public function getType() {
        //var_dump($this->conf);
        return "text";
    }

    public function getCl() {
        //var_dump($this->conf);
        return "jdateESP";
    }

    public function getdateformat($a=false) {
        if(isset($this->conf['separator']) && ($this->conf['separator'] == '-' || $this->conf['separator'] == '/')){
            $separador = $this->conf['separator'];
        }else{
            $separador = "/";
        }
            switch($this->conf['dateformat']){
                case "ymd":
                    $def = "yy".$separador."mm".$separador."dd";
                break;
                case "mdy":
                    $def = "mm".$separador."dd".$separador."yy";
                break;
                case "dmy":
                default:
                    $def = "dd".$separador."mm".$separador."yy";
                break;
            }


        //if($a) return $def2;
        return $def;
    }

    public function getSQL($tab,$alias = true) {

        //$def = $this->getdateformat();
        //if(isset($this->conf['format']))  $def = $this->conf['format'];
        $ret = "";
        $ret = "unix_timestamp(".$this->getSQLFLDRequest().")";
        if ($alias) $ret .= " as '".$this->getAlias()."'";
        //echo $ret;
        return $ret;
    }
}
