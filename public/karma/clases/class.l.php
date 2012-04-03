<?php
/**
 * Clase para la gestión de literales de las zonas públicas.
 * Requisito indispensable, que exista la tabla literales en la BBDD:
     CREATE TABLE `literales` (
      `id_literal` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
      `identificativo` varchar(255) DEFAULT NULL,
      `literal_es` varchar(500) DEFAULT NULL COMMENT 'lits. en castellano',
      `literal_eu` varchar(500) DEFAULT NULL COMMENT 'lits. en euskera',
      `literal_en` varchar(500) DEFAULT NULL COMMENT 'lits. en inglés',
      PRIMARY KEY (`id_literal`),
      UNIQUE KEY `identificativo` (`identificativo`)
     ) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8
 *
 *
 */
class l
{
	protected static $_lits = array();
	protected static $_tab = "literales";
	protected static $_campo = "literal";
	protected static $_iden = "identificativo";
	protected static $_sep = "_";

	/**
	 * Guarda el literal en el array de literales
	 * @param string $lit Identificador del literal, si $data no existe entonces se usará también como literal.
	 * @param string $data Traducción / Literal a mostrar
	 */
	protected static function _store($lit, $data = null)
	{
		if (is_null($data)) {
            $data = $lit;
		}
		self::$_lits[modules_ng::$lang . '::' . $lit] = $data;
	}

	/**
	 * Añadir una nueva cadena a la tabla de la BBDD
	 * @param string $lit
	 * @return string con la cadena insertada ($lit)
	 */
	protected static function _new($lit)
    {
		$con = new con("insert into " . self::$_tab . "(" . self::$_iden . ") values('" . con::escape($lit) . "')");
		self::_store($lit);
		return $lit;
	}

	/**
	 * Comprueba si el literal existe en el array de literales y si existe lo devuelve
	 * @param $lit
	 * @return string con la traducción o FALSE en caso de que el literal no exista
	 */
	protected static function _exists($lit)
    {
		if (isset(self::$_lits[modules_ng::$lang . '::' . $lit])) {
            return self::$_lits[modules_ng::$lang . '::' . $lit];
		} else {
            return false;
		}
	}

	/**
	 * Traduce la cadena y la escribe o la devuelve
	 * @param $lit Cadena a traducir
	 * @param $doEcho si es false, devuelve la cadena. Default: TRUE
	 * @return string|none
	 */
	public static function _($lit, $doEcho = true)
    {
		$val = self::__($lit);
		if ($doEcho) {
            echo $val;
		} else {
            return $val;
		}

	}

	/**
	 * Devuelve la cadena traducida
	 * @param string $lit Cadena a traducir
	 * @return string
	 */
	public static function __($lit)
    {
		if ( ($ret = self::_exists($lit)) !== false) {
            return $ret;
		}

		$langs = array();
		$langs[] = modules_ng::$lang;

		if (modules_ng::$lang != modules_ng::$defLang) {
			$langs[] = modules_ng::$defLang;
		}

		$sql = "select ";
		$sqlAux = array();

		foreach ($langs as $_l) {
			$sql .= "coalesce(";
			$sqlAux[] = "nullif(". self::$_campo . self::$_sep . $_l .",'')";
		}

		$sql .= implode(",", $sqlAux) . str_repeat(')', (sizeof($langs)-1)) . ',' . self::$_iden . ') as t';
		$sql .= ' from ' . self::$_tab .' where ' . self::$_iden . '=\'' . con::escape($lit) . '\' limit 1';


		$con = new con($sql);
		if ($con->getNumRows() == 0) {
			return self::_new($lit);
		}
		$r = $con->getResult();

		self::_store($lit, $r['t']);
		return $r['t'];
	}

    /**
     * Devuelve la cadena traducida
     * @param string $lit
     * @return string
     */
	public function __invoke($lit)
    {
	    return self::_($lit, false);
	}

	/**
	 * Función que hace lo mismo que _(), pero retorna el literal sustituyendo lo indicado en $searchStr por $replaceStr
	 * @param string $lit Cadena a traducir
	 * @param string $searchStr parte de la cadena a sustituir
	 * @param string $replaceStr cadena por la que se sustituirá
	 * @param bool $doEcho si es false, devuelve la cadena. Default: TRUE
	 * @return string|none
	 */
	public static function _rl($lit, $searchStr, $replaceStr, $doEcho = true)
    {
		$val = self::__($lit);
		$val = str_replace($searchStr, $replaceStr, $val);
		if ($doEcho) {
            echo $val;
		} else {
            return $val;
		}
	}

    /**
     * Traduce la cadena al idioma especificado y la devuelve o la escribe
     * @param string $lit Cadena a traducir
     * @param string $_lang Idioma en el que lo queremos traducir
     * @param bool $doEcho si es false, devuelve la cadena. Default: TRUE
     * @return string|none
     */
	public static function _inlang($lit, $lang, $doEcho = true)
    {
        $val = self::__inlang($lit, $lang);
        if ($doEcho) {
            echo $val;
        } else {
            return $val;
        }
    }

    /**
     * Devuelve la cadena traducida al idioma especificado
     * @param string $lit Cadena a traducir
     * @param string $_lang Idioma en el que lo queremos traducir
     * @return string
     */
    public static function __inlang($lit, $_lang)
    {
        $deflang = modules_ng::$lang;
        $lang = $_lang;

        $sql = "select ";
        $sqlAux = array();

        $sql .= "coalesce(";
        $sql .= "coalesce(";
        $sqlAux[] = "nullif(" . self::$_campo . self::$_sep . $lang .",'')";
        $sqlAux[] = "nullif(" . self::$_campo . self::$_sep . $deflang.",'')";
        $sql .= implode(",", $sqlAux) . str_repeat(')', 1) . ',' . self::$_iden . ') as t';
        $sql .= ' from ' . self::$_tab .' where ' . self::$_iden . '=\'' . con::escape($lit) . '\' limit 1';

        $con = new con($sql);

        if ($con->getNumRows() == 0) {
            return self::_new($lit);
        }
        $r = $con->getResult();

        self::_store($lit, $r['t']);
        return $r['t'];
    }
}
