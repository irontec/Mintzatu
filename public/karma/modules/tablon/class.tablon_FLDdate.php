<?php
/**
 * Fichero de clase para campo tipo Date
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 *
 * ::::PLT::::
 *
 * req: Campo requerido
 * unique: Valor unico
 * separator [-,/]: Separador utilizado para las fechas. Por defecto: /
 * dateformat [ymd,mdy,dmy]: Formato para la fecha. Por defecto: dmy
 * showon [both,focus,button]: Cuando mostrar el datepicker.
 * mindate (intervalo): Fecha mínima seleccionable por defecto (en formato +/-Intervalo, es decir, por ejemplo: +7d (para una semana), +1M +1d (para más un mes y un día...))
 * maxdate (intervalo): Fecha máxima seleccionable por defecto (en formato +/-Intervalo, es decir, por ejemplo: +7d (para una semana), +1M +1d (para más un mes y un día...))
 * date_couple: index de otro campo Date. Toma el valor de este otro, cuando tiene valor, como delimitador de fecha máxima o mínima, dependiendo del siguiente campo...
 * couple_pos [min,max]: si se pone min, se indica que este campo es el de fecha 'inferior' y tomará el el valor del campo del "date_couple" como delimitador de fecha máxima. Si se pone max, sera a la inversa, este será la fecha 'superior' y tomará el valor del campo de "date_couple" como delimitador de fecha mínima.
 * notfillDefaultDate: Para que el campo fecha no se rellene automáticamente con la fecha actual
 *
 *
 */

class tablon_FLDdate extends tablon_FLD
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
	public function getMysqlValue($value) {

		$c = new con("select 1;");


		if(isset($this->conf['separator']) && ($this->conf['separator'] == '-' || $this->conf['separator'] == '/')){
			$separador = $this->conf['separator'];
		}else{
			$separador = "/";
		}

		switch($this->conf['dateformat']){
			case "ymd":
				list($anio,$mes,$dia) = explode($separador,$value,3);
			break;
			case "mdy":
				list($mes,$dia,$anio) = explode($separador,$value,3);
			break;
            case "dmy":
            default:
                list($dia,$mes,$anio) = explode($separador,$value,3);
            break;
		}



		$val = mktime(0,0,0,$mes,$dia,$anio);

		$vl = date("Y{$separador}m{$separador}d",tablon_FLD::cleanMySQLValue($val));
		$this->setValue(tablon_FLD::cleanMySQLValue(date("Y{$separador}m{$separador}d",$val)));

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

	    if(isset($this->conf['separator']) && ($this->conf['separator'] == '-' || $this->conf['separator'] == '/')){
            $separador = $this->conf['separator'];
        }else{
            $separador = "/";
        }

        /*
		* Parche porque no funciona CORRECTAMENTE Al meter desde NEW... bastante garete
		* value se espera como AAAA-MM-DD, pero desde NEW llega como DD-MM-AAAA
		* se comprueba que viene como DD-MM-AAAA y se reescribe value para que tire
		* NO FUNCIONARÁ con MM-DD-AAAA :S
		*
		* En definitiva, sea lo que sea lo transformamos a AAAA-MM-DD
		*/
		if (preg_match("/^([0-9]{2})[^0-9]{1}([0-9]{2})[^0-9]{1}([0-9]{4})$/",$value,$r)) {
				$value = $r[3].$separador.$r[2].$separador.$r[1];
		}
		/* FIN PARCHE */
		$value = preg_replace('/[^0-9]/',$separador,$value);

		if ($value==NULL) $value = "0000{$separador}00{$separador}00";

		switch($this->conf['dateformat']){
			case "ymd":
				list($anio,$mes,$dia) = explode($separador,$value,3);
				$t = 'Y'.$separador.'m'.$separador.'d';
			break;
			case "mdy":
				list($anio,$mes,$dia) = explode($separador,$value,3);
				$t = 'm'.$separador.'d'.$separador.'Y';
			break;
			case "dmy":
			default:
				list($anio,$mes,$dia) = explode($separador,$value,3);
				$t = 'd'.$separador.'m'.$separador.'Y';
			break;
		}

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

	public function drawTableValueEdit($value,$clone=false,$disabled=false) {
		$showon=$couple="";
		if(isset($this->conf['separator']) && ($this->conf['separator'] == '-' || $this->conf['separator'] == '/')){
			$separador = $this->conf['separator'];
		}else{
			$separador = "/";
		}
		$showon = " showon = ";
		if(isset($this->conf['showon']) && ($this->conf['showon']=='both' || $this->conf['showon']=='focus' || $this->conf['showon'] == 'button')){
			$showon .= "'".$this->conf['showon']."' ";
		}else{
			$showon .= "'both' ";
		}

		$couple = " coupledate = ";
		if(isset($this->conf['date_couple']) && !empty($this->conf['date_couple']) && isset($this->conf['couple_pos']) && !empty($this->conf['couple_pos'])){
			$couple .= "'".$this->conf['date_couple']."' couplepos='".$this->conf['couple_pos']."'";
		}else{
			$couple .= "'' ";
		}
		$mind = $maxd = "";
		if(isset($this->conf['mindate']) && !empty($this->conf['mindate'])){
			$mind = " mindate = '".$this->conf['mindate']."' ";
		}
		if(isset($this->conf['maxdate']) && !empty($this->conf['maxdate'])){
			$maxd = " maxdate = '".$this->conf['maxdate']."' ";
		}
		$this->setValue($value);
		$clase = "date-pick";
		$clase.=(($clone)? ' clone':'');
		$clase.=((isset($this->conf['req']) && $this->conf['req'] == "1")?" required":"");
		$clase = "class = '".$clase."'";

		$html ='<input type="'.$this->getType()
            . '" id="' . $this->getSQLFLD().'_'.$this->getCurrentID()
    		. '" datef="'.$this->getdateformat()
	       	. '" name="'.$this->getSQLFLD().(($clone)? '_clone':'').'" '.$clase.$showon.$couple.$mind.$maxd.' value="';

		if($this->getValue() !== false){

			$html.=$this->drawTableValue($this->getValue());
		}else{
			if(isset($this->conf['notfillDefaultDate']) && !empty($this->conf['notfillDefaultDate'])){
				$html .= '';
			}else{
				$dt = $this->getdateformat();
				$aos=array();

				for($i=0;$i<strlen($dt);$i++){
					$x = $dt{$i};
					$aos[]= ($x=="y")? 'Y':$x;
				}
				//echo $this->drawTableValue(date('Y-m-d'));
				//var_dump(date(implode($separador,$aos))); exit();
				$html.=$this->drawTableValue(date('Y-m-d'));
			}
		}

		$html.='" />';



	 	return $html;
	}
	public function drawEditJSON() {
		$this->setValue($value);
		$aRet['datef'] = $this->getdateformat();
		$aRet['name'] = $this->getSQLFLD();
		$aRet['theclass'] = "date-pick";
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
		return "jdate";
	}

	/*
	 * Devuelve el formato del dateformat
	 */
	public function getdateformat() {
		if(isset($this->conf['separator']) && ($this->conf['separator'] == '-' || $this->conf['separator'] == '/')){
			$separador = $this->conf['separator'];
		}else{
			$separador = "/";
		}

		if(!isset($this->conf['dateformat'])){
		    $this->conf['dateformat'] = 'dmy';
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
		return $def;
	}

	public function getSQL($tab,$alias = true) {

		//$def = $this->getdateformat();
		//if(isset($this->conf['format']))  $def = $this->conf['format'];
		$ret = "";
		$ret = " date_format(".$this->getSQLFLDRequest().",'%Y-%m-%d') ";
		if ($alias) $ret .= " as '".$this->getAlias()."'";
		//echo $ret;
		return $ret;
	}



}

?>