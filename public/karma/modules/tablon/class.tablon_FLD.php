<?php
/**
 * Fichero de clase abstracta para campo de tablon
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 *
 * ::PLT::
 * unique
 * entitify
 * alias
 * literal
 * type
 * clone
 * cloneInfo
 * clean
 * real
 * sql
 * randombutton
 * caser: Poner todo en mayúsculas
 * nullable
 * defaultKey
 * req
 * valueOnEmpty
 * fplt
 * trigger
 * triggerOn
 * triggerRefresh
 * triggerParams
 *
 */
abstract class tablon_FLD
{
    /**
     *
     * @var KarmaRegistry Registro del sistema, donde se almacenan las variables globales
     */
    protected $_kRegistry;
	protected $conf;
	protected $index;
	protected $plt;
	protected $unique = false;
	protected $value;
	protected $entitify = false;
	public $subFields = array();
	public $sizeofsubFields = 0;
	public $aHideConds = array();
	public $searchValue = false;
	protected $trigger = false;
	protected $triggerOn = array();
	public $triggerParamsEsp = false;

	/**
	 *
	 * @var array Array que contiene las acciones tras las cuales hay que lanzar el refresco: insert, update, ...
	 * Si está vacío no habrá ningún refresco
	 */
	protected $triggerRefresh = array();

	/**
	 *
	 * @var k_literal Variable utilizada para proveer de multilenguaje a los mensajes relativos a los campos
	 */
	protected $l;

	/**
	 *
	 *  @var int Se setea en cada drawTableContents para que FLD conozca que ID es en ese momento
	 */
	protected $currentID = false;

	/**
	 *
	 * @param array $conf Configuración de la columna actual (plt)
	 * @param string $idx Nombre de la columna actual (plt)
	 * @param string $plt Nombre del fichero plt
	 */
	function __construct($conf,$idx,$plt = false)
    {
        $this->_kRegistry = KarmaRegistry::getInstance();
        if (!$this->_kRegistry->isDefined('lang')) {
			new krm_menu();
        }
        $this->l = new k_literal($this->_kRegistry->get('lang'));

		$this->conf = $conf;
		if (isset($this->conf['unique'])) $this->unique = true;
		if (isset($this->conf['entitify'])) $this->entitify = true;
		$this->index = $idx;
		$this->plt = $plt;
		if (isset($this->conf['trigger'])) {
			if (isset($this->conf['triggerOn']) && !empty($this->conf['triggerOn'])) {
				$this->triggerOn = explode("|", $this->conf['triggerOn']);
				$this->trigger = $this->conf['trigger'];
			}
			if (isset($this->conf['triggerRefresh'])
			   && !empty($this->conf['triggerRefresh'])) {
				$this->triggerRefresh = explode("|", $this->conf['triggerRefresh']);
			}
			if (isset($this->conf['triggerParamsEspecial'])
			   && !empty($this->conf['triggerParamsEspecial'])) {
				$this->triggerParamsEsp = $this->conf["triggerParamsEspecial"];
			}
		}
	}

	public function getTagConf()
    {
		return $this->conf;
	}

	public function getIsReal()
	{
	    return true;
	}

	/**
	 * Alias de getAlias()
	 */
	public function getTitle()
    {
		return $this->getAlias();
	}

	/**
	 *
	 * @param $tab Nombre de la tabla
	 * @param $alias
	 */
	public function getSQL($tab,$alias = true)
    {
		$ret = "";
		$ret = $this->getSQLFLDRequest();
		if ($alias) $ret .= " as '".$this->getAlias()."'";
		return $ret;
	}

	public function getMLAlias()
	{
	    $alias = KarmaRegistry::getInstance()->get('translator')->translate($this->conf['alias'], $this->conf);
	    $isTranslated = KarmaRegistry::getInstance()->get('translator')->isTranslated($this->conf['alias'], $this->conf);
	    if ($isTranslated) {
	        return $alias;
	    }
        if ($res = $this->l->search($this->conf['alias'])) {
            return $res;
        }
        if (isset($this->conf['alias'])) {
            return $this->conf['alias'];
        }
        if (isset($this->conf['alias'.$langSeparator."es"])) {
            return $this->conf['alias'.$langSeparator."es"];
        }
	    return false;
	}

    /**
     * Devuelve el Alias del campo.
     * Si no existe devuelve el index con guiones bajos a los lados.
     */
	public function getAlias()
    {
        $alias = $this->getMLAlias();

	    if (!$alias) {
	        return '_' . $this->index . '_';
	    }
		return $alias;
	}
	public function getLiteral()
    {
		return $this->conf['literal'];
	}
	public function getRealType()
    {
		return $this->conf['type'];
	}

	public function isclone()
    {
		return isset($this->conf['clone']);
	}
	public function iscloneInfo()
    {
		return ( (isset($this->conf['cloneInfo']))? $this->conf['cloneInfo'] :"" );
	}
	public function toClean()
    {
		return isset($this->conf['clean'])? $this->conf['clean']:false;
	}
	public function toCleanUnique()
    {
		//cleanunique
		if (isset($this->conf['cleanunique'])) {
			$aUniques = explode("|", $this->conf['cleanunique']);
			if (count($aUniques)>0) return $aUniques;
			else return false;
		} else return false;
	}
	public function getReal()
    {
		return isset($this->conf['real'])? $this->conf['real']:false;
	}

	/**
	 * Devuelve la sentencia necesaria para añadir el tipo de campo a MySQL
	 * @return String
	 */
	public function getSQLType()
    {
		return "";
	}


	public function getSQLFLDRequest()
    {
		return $this->getSQLFLD(true);

	}

	public function getSQLFLD($real=false)
    {
		if (isset($this->conf['sql'])) {
            return $this->conf['sql'];
		} else {
            return (($real && $this->getReal() != "")? $this->getReal() : $this->getIndex());
		}
	}

	public function getCl()
    {
		return "";
	}

	public function getOCl()
    {
		return "";
	}

	public function getIndex()
    {
		return $this->index;
	}

	public function getInSQL()
    {
        return true;
    }

    public function drawTableValue($value)
    {
		if ($this->entitify) {
			$value = htmlentities($value, ENT_QUOTES, 'utf-8');
		}
		$accionEjec = 'select';
		if (($rFunc = $this->getTrigger($value, $accionEjec))!==false) {
			if (in_array($accionEjec, $this->getTriggerOn())) {
				$funcionTrigger = $rFunc.';';
				eval("\$value = $funcionTrigger");
				return $value;
			}
		}
		if (empty($value) && $value !== "0" && $value !== 0) return "";
		return $value;
	}

	/**
	 * Devuelve el string necesario para dibujar el input/select/etc. de edición del campo
	 * @param $value String con el valor del campo
	 * @param $clone bool que indica si el campo debería clonarse o no (añade la clase "clone" al campo)
	 * @param $disabled bool indicando si el campo debe estar deshabilitado o no
	 * @return String
	 */
	public function drawTableValueEdit($value,$clone=false,$disabled=false)
    {
		$this->setValue($value);
		if ($disabled == true) {
			$strDis = ' disabled = "disabled" ';
		} else {
			$strDis = "";
		}
		$toappend = "";
		if (($boton = $this->addrandombutton()) !== false) {
			$toappend = "&nbsp;&nbsp;".$boton;
		}
		return '<input type="'.$this->getType()
		       . '" name="'.$this->getSQLFLD().(($clone)? '_clone':'')
		       . '" '.(($clone)? 'class="clone"':'')
		       . ' value="' . $this->drawTableValue($this->getValue())
               . '" id="' . $this->getSQLFLD().'_'.$this->getCurrentID()
		       . '" />'
		       . $toappend;
	}

	/**
	 * Si se especifica randombutton en el plt, con el formato : opcion1_opcion2_opcion3|largura...
	 * se añade un botón de generador de strings aleatorios junto al input, que:
	 * 	toma como base los caracteres especificados como "opciones" (si hay más de 1 separado por _,
	 *  se concatenarán las opciones):
	 * 		- 1: "0123456789"
	 * 		- 2: "abcdfghjkmnpqrstvwxyz"
	 * 		- 3: "ABCDEFGHIJKLMNOPQRSTUVWXYZ"
	 *	largura: largura deseada para el string generado.
	 * */
	public function addrandombutton()
    {
		if (isset($this->conf['randombutton']) && !empty($this->conf['randombutton'])) {
			list($randpatt,$randlength) = explode("|", $this->conf['randombutton']);
			if (!empty($randpatt) && !empty($randlength))
				return "<input type='button' id='dorandomstring_" . $this->getSQLFLD()
				       . "' name='dorandomstring_" . $this->getSQLFLD()
				       . "' class= 'dorandomstring' value='?' rpatterns = '" . $randpatt
				       . "' rlengrh='" . $randlength
				       . "' id='" . $this->getSQLFLD()."_".$this->getCurrentID()
				       . "' style='width:10px;'>";
		}
		return false;
	}

	public function hasSubFields()
    {
		return sizeof($this->subFields>0);
	}

	/**
	 * Devuelve el valor limpio para insertar en la BBDD
	 *
	 * @param String $value
	 */
	static function cleanMysqlValue($value)
    {
		return con::escape($value);
	}

	/**
	 * Setea el valor del campo
	 * @param $value
	 */
	public function setValue($value)
    {
		$this->value = $value;
		return $this;
	}

	public function getMysqlValue($value)
    {

		$c = new con("select 1;");
		$v=tablon_FLD::cleanMySQLValue($value);

		if (isset($this->conf['caser'])) {

			switch($this->conf['caser']) {
				case "allfirstcapital":
					$v = mb_strtolower(mb_ucwords($v));
				    break;
				case "alltolowercase":
					$v = mb_strtolower($v);
				    break;
			}



		}
		$this->setValue($v);
		return '\''.$this->getValue().'\'';
		//return '\''.tablon_FLD::cleanMySQLValue($v).'\'';
	}
	public static function getMysqldelValue($value)
    {

		$c = new con("select 1;");
		$v=tablon_FLD::cleanMySQLValue($value);
		return '\''.tablon_FLD::cleanMySQLValue($v).'\'';
	}
	public function getValue()
    {
		//echo "<br />\n*getValue=".$this->value;
		if (isset($this->conf['nullable'])
		   && $this->conf['nullable']!==false
		   && isset($this->conf['defaultKey'])
		   && $this->conf['defaultKey'] == "__NULL") {
            if (empty($this->value)) {
            	return "NULL";
            }
		}
		return $this->value;
	}

	/**
	 * Devuelve true si el campo es requerido, false en caso contraio
	 * @return bool
	 */
	public function isRequired()
    {
		return ((isset($this->conf['req'])) && ((bool)$this->conf['req']));
	}

	/**
	 * Devuelve " not null" si es un campo requerido, "" en caso contrario
	 * @return String
	 */
	public function getSQLRequired()
    {
		if ($this->isRequired()) {
		    return " not null";
		}
		return "";
	}

	/**
	 * Devuelve " unique" si es un campo unico, "" en caso contrario
	 * @return String
	 */
	public function getSQLUnique()
    {
		if ($this->isUnique()) {
		    return " unique";
		}
		return "";
	}

	public function getDefault()
    {
		if (!isset($this->conf['defaultKey'])) {
            return false;
		} else {
			if (isset($this->conf['nullable'])
			   && $this->conf['nullable']!==false
			   && $this->conf['defaultKey'] == "__NULL") {
				if (empty($this->value))
					return "NULL";
			} else {
			    return $this->conf['defaultKey'];
			}
		}
	}

    public function setDefault($value)
    {
        $this->conf['defaultKey'] = $value;
    }

	public function getSQLDefaultValue()
    {
		if (($dflt = $this->getDefault())!==false) {
			return " default '".$dflt."'";
		}
		return "";
	}

	public function getConstantTypeAjaxUpload()
    {
		return "_GET";
	}

	protected function getPlt()
    {
		return $this->plt;
	}

	/**
	 * Devuelve true si el campo es unico, false en caso contrario
	 * @return bool
	 */
	public function isUnique()
    {
		return $this->unique;
	}
	public function hasHiddenConds()
    {
		return sizeof($this->aHideConds)>0;
	}

	public function getHiddenFieldsCond()
    {
		if (!isset($this->value)) return array();
	}

	public function getTrigger($id="",$accionEjec = "")
    {
		if (isset($this->conf['triggerParams']) && !empty($this->conf['triggerParams'])) {
			$elParam = $this->conf["triggerParams"];
			eval("\$elParam = \"$elParam\";");
			$elParam = trim($elParam, ",");
			$this->trigger = preg_replace("/\(.*\)/", "(" . $elParam . ")", $this->trigger);
		}
		return $this->trigger;
	}

	public function runTriggerNew($id, $accionEjec, $datos=false)
    {
		if ($this->trigger !== false && isset($this->triggerParamsEsp) && !empty($this->triggerParamsEsp)) {
			$theTrigger = $this->trigger;
			$theTrigger = preg_replace("/\(.*\)/", "", $this->trigger);
			$theParams = $this->triggerParamsEsp;
			$retorno = call_user_func($theTrigger, $id, $accionEjec, $datos);
			return $retorno;
		}
		return false;
	}

	public function getTriggerOn()
    {
		return $this->triggerOn;
	}
	/*
	 * Mira si triggerRefresh es un array con contenido, en cuyo caso devuelve el array, sino devuelve false
	 */
	public function getTriggerRefresh()
    {
		if (is_array($this->triggerRefresh) && !empty($this->triggerRefresh)) {
			return $this->triggerRefresh;
		} else {
			return false;
		}
	}

	public function valueOnEmpty($value = null)
    {
		if (isset($this->conf['valueOnEmpty'])) {
			return "".$this->conf['valueOnEmpty']."";
		} else {
			if (isset($value))
				return $value;
			else
				return "''";
		}
	}

	public function getSearchValue()
    {
		return $this->searchValue;
	}

	public function setSearchValue($v)
    {
		$c = new con("select 1;");
		$this->searchValue = tablon_FLD::cleanMySQLValue($v);
	}

	public function getSQLFLDSearch()
    {
		if (isset($this->conf['sqlSearch'])) return $this->conf['sqlSearch'];
		return $this->getSQLFLDRequest();
	}

	public function getSearchOp()
    {
		return ' like \'%'.$this->getSearchValue().'%\'';
	}

	public function setCurrentID($id)
    {
		$this->currentID = $id;
	}

	public function getCurrentID()
    {
		return $this->currentID;
	}

	/**
	 * @return String involving where the condition should be added into the query:
	 *		"aCondSearch" if the condition should be added to the "where" sentence
	 *  	"aHavingSearch" if the condition should be added to the "having" sentence
	 */
 	public function getSearchVarType()
    {
 		return "aCondSearch";
 	}

	abstract protected function getType();

	//Obtener plantilla externa
    public function getfPlt()
    {
    	if (isset($this->conf['fplt']) && $this->conf['fplt']) {
            return $this->conf['fplt'];
    	} else {
    		return false;
    	}
    }

    public function getDependencia()
    {
    	return false;
    }

    /**
     * Función que comprueba si el campo tiene el atributo "fromAnotheTab" a 1,
     * lo cual indica que el campo no está en la tabla del plt actual, sino en otra
     * @return boolean
     */
    public function ifFieldOfAnotherTab()
    {
    	if (isset($this->conf['fromAnotherTab']) && $this->conf['fromAnotherTab']=="1") return true;
    	else return false;
    }

     /**
     * Función que devuelve el texto descriptivo del campo, si existe.
     * Servirá para los formularios de "nuevo" o "edición", para añadir textos
     * con algún requistio sobre el campo que el usuario deba saber
     * @return text
     */
    public function getDescriptionTextForField()
    {
    	if (isset($this->conf['descriptionHelperText']) && !empty($this->conf['descriptionHelperText'])) {
    		return $this->conf['descriptionHelperText'];
    	} else {
    		return false;
    	}
    }

    public function getTotal()
    {
    	switch(true) {
    		case (isset($this->conf['total_sql'])):
    		case (isset($this->conf['totalsql'])):
    			$sql = (isset($this->conf['total_sql']))? $this->conf['total_sql']:$this->conf['totalsql'];
    			$con = new con($sql);
    			if ( (!$con->error()) && ($con->getNumRows()>0) ) {
    				$r = $con->getResult();
    				return array_shift($r);
    			}

    		case (isset($this->conf['total'])):
    			if (isset($this->conf['total'])) return $this->conf['total'];

    		default:
    			return $this->getTitle();
    	}

    }

	public function getSearchHelp()
    {
    	if (!isset($this->conf['helpSearch'])) return false;
    	return $this->conf['helpSearch'];
	}

    public function isMutant() {
        return (isset($this->conf['mutant'])) && (bool)$this->conf['mutant'];
    }

}
