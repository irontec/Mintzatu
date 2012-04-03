<?php

class k_literal
{
    protected $_aliterales = array(
        ''=>array('es'=>'','en'=>'','eu'=>'')
    );

    protected $_lang;
    protected $_genero = false;

    public function __construct($lang = 'es')
    {
        $this->setLang($lang);
        if (file_exists(dirname(__FILE__).'/../lang/langFile.php')) {
            try {
                include(dirname(__FILE__).'/../lang/langFile.php');

            } catch (Exception $e) {
            }
            if (isset($multiLangLiterals) && is_array($multiLangLiterals)) {
                $this->addLiterals($multiLangLiterals);
            }
        }
        if (file_exists(dirname(__FILE__).'/../modules/lang/langFile.php')) {
            try {
                include(dirname(__FILE__).'/../modules/lang/langFile.php');

            } catch (Exception $e) {
            }
            if (isset($multiLangLiterals) && is_array($multiLangLiterals)) {
                $this->addLiterals($multiLangLiterals);
            }
        }
    }

    public function addLiteralsFromPath($path)
    {
        if (file_exists($path.'/lang/langFile.php')) {
            try {
                include($path.'/lang/langFile.php');
            } catch (Exception $e) {
            }
            if (isset($multiLangLiterals) && is_array($multiLangLiterals)) {
                $this->addLiterals($multiLangLiterals);
            }
        }
    }

    public function addLiterals($array)
    {
        if (is_array($array) && sizeof($array)>0) {
            foreach ($array as $key=>$values) {
                if (!isset($this->_aliterales[$key])) {
                    $this->_aliterales[$key] = $values;
                } else {
                    foreach ($this->_aliterales[$key] as $lang=>$value) {
                        if (isset($values[$lang]) && trim($values[$lang])!="" && $value=="") {
                            $this->_aliterales[$key][$lang] = $values[$lang];

                        }
                    }
                }
            }
        }
    }


    /**
     * Cambia el idioma del objeto a $lang
     * @param $lang
     */
    public function setLang($lang)
    {
        $this->_lang = $lang;
    }

    /**
     * Comprueba si una traducción del literal existe
     * @param $l
     */
    public function exist($l)
    {
        if (isset($this->_aliterales[$l]) && is_array($this->_aliterales[$l])) {
            return true;
        }
        return false;
    }

    /**
     * Comprueba si una traducción del literal existe
     * @param $l
     */
    public function search($l)
    {
        $options = array(mb_strtolower($l), ucfirst(mb_strtolower($l)), mb_strtoupper($l));
        foreach ($options as $option) {
            if (
                isset($this->_aliterales[$option])
                && is_array($this->_aliterales[$option])
                && isset($this->_aliterales[$option][$this->_lang])
                && trim($this->_aliterales[$option][$this->_lang])!=""
            ) {
                return ucfirst($this->_aliterales[$option][$this->_lang]);
            }
        }
        return false;
    }
    /**
     * Devuelve la traducción del literal con el genero concatenado en caso de que exista
     * Si no existe devuelve el propio literal con el genero añadido al final
     * El genero solo se añade en el caso del castellano (es)
     *
     * @param $l Nombre o valor por defecto del literal
     * @param $genero String que se añadirá al final (Normalmente utilizado para el genero)
     */
    function literal($l, $genero = false)
    {
        if ($this->exist($l)) {
	    if (!isset($this->_aliterales[$l]) || !isset($this->_aliterales[$l][$this->_lang])) {
		return $this->_freturn($l, $genero);
	    }
            if ($retval = $this->_aliterales[$l][$this->_lang]) {
                return $this->_freturn($retval, $genero);
            }
        }

        return $this->_freturn($l, $genero);
    }

    /**
     * Devuelve el literal traducido concatenando la entidad y el genero.
     * En caso de que el idioma sea "eu" la entidad irá al principio
     * @param $l
     * @param $genero
     * @param $entidad
     */
    function literalstr($l, $genero = false, $entidad = '')
    {
        switch ($this->_lang){
            case "eu":
                $str = $entidad." ".$this->literal($l, $genero);
                break;
            default:
                $str = $this->literal($l, $genero) . " " . $entidad;
                break;
        }
        return $str;
    }

    /**
     * Devuelve el literal traducido substituyendo las instancias de %var% de la traducción por la variable $v
     * @param $l
     * @param $v
     */
    public function literalvar($l, $v)
    {
        $str = str_replace('%var%', $v, $this->literal($l));
        return $str;
    }

    protected function _freturn($l, $genero = false)
    {
        if ($this->_lang == "es" && $genero != false && $genero != "") {
            return $l . $genero;
        } else {
            return $l;
        }
    }

    /****************************
     * ALIASES DE LAS FUNCIONES *
     ****************************/
    public function l($l, $genero = false)
    {
        return $this->literal($l, $genero);
    }

    public function lstr($l, $genero = false, $entidad = '')
    {
        return $this->literalstr($l, $genero, $entidad);
    }

    public function lvar($l, $v)
    {
        return $this->literalvar($l, $v);
    }
}
