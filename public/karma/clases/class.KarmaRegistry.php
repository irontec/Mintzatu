<?php
/**
 * Registro global de Karma
 * Almacena "variables" y objetos reutilizables en el sistema
 * @author alayn
 *
 */
class KarmaRegistry implements Iterator
{
    protected static $_instance = null;

    /**
     * Array dónde se almacenan los objetos/variables
     * @var array
     */
    protected $_values = array();

    /**
     * Posición actual del Iterator dentro del array
     * @var int
     */
    protected $_position = 0;

    protected function __construct()
    {
    }

    /**
     * @return KarmaRegistry
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }

    /**
     * @return KarmaRegistry
     */
    public function set($name, $value)
    {
        $this->_values[$name] = $value;
        return $this;
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function get($name)
    {
        if (isset($this->_values[$name])) {
            return $this->_values[$name];
        }
    }

    public function isDefined($name)
    {
        return isset($this->_values[$name]);
    }

    /**
     * Devuelve el valor actual
     */
    public function current()
    {
        return current($this->_values);
    }

    /**
     * Mueve el puntero a la siguiente posición del array
     */
    public function next()
    {
        next($this->_values);
        $this->_position++;
    }

    /**
     * Devuelve la clave del elemento actual
     */
    public function key()
    {
        return key($this->_values);
    }

    /**
     * Indica si la posición actual es válida o no
     */
    public function valid()
    {
        return  $this->_position < sizeof($this->_values);
    }

    /**
     * Resetea el puntero a la posición inicial del array
     */
    public function rewind()
    {
        reset($this->_values);
        $this->_position = 0;
    }

}