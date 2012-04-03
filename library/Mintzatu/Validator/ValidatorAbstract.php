<?php
/**
 * Application Model Validator
 *
 * @package Application_Model_Validator
 * @subpackage Model
 * @author <YOUR NAME HERE>
 * @copyright ZF model generator
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Abstract class that is extended by all validation models
 *
 * @package Application_Model
 * @subpackage Model
 * @author <Arkaitz Etxeberria>
 */

abstract class Mintzatu_Validator_ValidatorAbstract
{
    protected $_errorMessages;

    protected $_data = array();

    public function isValid($data)
    {
        if (! is_array($data)) {

            if (! $data instanceof Iterator) {

                return false;

            } else {

                $tmp = array();
                foreach ($data as $attrib) {

                    $method = 'get'.ucfirst($attrib);
                    $tmp[$attrib] = $data->$method();
                }

                $data = $tmp;
            }
        }

        $this->_data = $data;

        $this->_errorMessages = array();

        foreach ($this->_data as $key => $value) {

            $method = 'set'.ucfirst($key);
            if (method_exists($this, $method)) {

                $this->$method($value);
            }
        }

        $this->_data = array();
        return count($this->_errorMessages) > 0 ? false : true;
    }

    public function getErrorMessages()
    {
        return $this->_errorMessages;
    }

    public function setErrorMessages($key, array $error)
    {
        $this->_errorMessages[$key] = $error;
        return $this;
    }
}