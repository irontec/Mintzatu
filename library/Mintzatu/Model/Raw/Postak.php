<?php
/**
 * Application Models
 *
 * @package Mintzatu_Model_Raw
 * @subpackage Model
 * @author <Lander Ontoria Gardeazabal>
 * @copyright Irontec - Internet y Sistemas sobre GNU/Linux
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */
/**
 * 
 *
 * @package Mintzatu_Model
 * @subpackage Model
 * @author <Lander Ontoria Gardeazabal>
 */
class Mintzatu_Model_Raw_Postak extends Mintzatu_Model_Raw_ModelAbstract
{
    /**
     * Database var type mediumint(8) unsigned
     *
     * @var int
     */
    protected $_IdPosta;

    /**
     * Database var type varchar(300)
     *
     * @var string
     */
    protected $_Identifikatzailea;

    /**
     * Database var type text
     *
     * @var text
     */
    protected $_MezuaHtml;

    /**
     * Database var type text
     *
     * @var text
     */
    protected $_MezuaText;



    /**
     * Sets up column and relationship lists
     */
    public function __construct()
    {
        parent::init();
        $this->setColumnsList(array(
            'id_posta'=>'IdPosta',
            'identifikatzailea'=>'Identifikatzailea',
            'mezua_html'=>'MezuaHtml',
            'mezua_text'=>'MezuaText',
        ));
        
        $this->setMultiLangColumnsList(array(
        ));

        $this->setAvailableLangs(array('posta','html','text'));

        $this->setParentList(array(
        ));

        $this->setDependentList(array(
        ));

		parent::__construct();
    }




    /**
     * Sets column id_posta
     *
     * @param int $data
     * @return Mintzatu_Model_Postak
     */
    public function setIdPosta($data)
    {
        $this->_IdPosta = $data;
        return $this;
    }

    /**
     * Gets column id_posta
     *
     * @return int
     */
    public function getIdPosta()
    {
 
        return $this->_IdPosta;
    }


    /**
     * Sets column identifikatzailea
     *
     * @param string $data
     * @return Mintzatu_Model_Postak
     */
    public function setIdentifikatzailea($data)
    {
        $this->_Identifikatzailea = $data;
        return $this;
    }

    /**
     * Gets column identifikatzailea
     *
     * @return string
     */
    public function getIdentifikatzailea()
    {
 
        return $this->_Identifikatzailea;
    }


    /**
     * Sets column mezua_html
     *
     * @param text $data
     * @return Mintzatu_Model_Postak
     */
    public function setMezuaHtml($data)
    {
        $this->_MezuaHtml = $data;
        return $this;
    }

    /**
     * Gets column mezua_html
     *
     * @return text
     */
    public function getMezuaHtml()
    {
 
        return $this->_MezuaHtml;
    }


    /**
     * Sets column mezua_text
     *
     * @param text $data
     * @return Mintzatu_Model_Postak
     */
    public function setMezuaText($data)
    {
        $this->_MezuaText = $data;
        return $this;
    }

    /**
     * Gets column mezua_text
     *
     * @return text
     */
    public function getMezuaText()
    {
 
        return $this->_MezuaText;
    }

    /**
     * Returns the mapper class for this model
     *
     * @return \Mappers\Sql\Postak
     */
    public function getMapper()
    {
        if ($this->_mapper === null) {

            \Zend_Loader_Autoloader::getInstance()->suppressNotFoundWarnings(true);

            if (class_exists('\Mappers\Sql\Postak')) {

                $this->setMapper(new \Mappers\Sql\Postak);

            } else if (class_exists('\Mappers\Soap\Postak')) {

                $this->setMapper(new \Mappers\Soap\Postak);

            } else {

                Throw new \Exception("Not a valid mapper class found");
            }

            \Zend_Loader_Autoloader::getInstance()->suppressNotFoundWarnings(false);
        }

        return $this->_mapper;
    }

    /**
     * Returns the validator class for this model
     *
     * @return null | Mintzatu_Model_Validator_Postak
     */
    public function getValidator()
    {
        if ($this->_validator === null) {

            if (class_exists('Mintzatu_Validator_Postak')) {
            
                $this->setValidator(new Mintzatu_Validator_Postak);
            }
        }

        return $this->_validator;
    }

    /**
     * Deletes current row by deleting the row that matches the primary key
     *
	 * @see \Mappers\Sql\Postak::delete
     * @return int|boolean Number of rows deleted or boolean if doing soft delete
     */
    public function deleteRowByPrimaryKey()
    {
        if ($this->getIdPosta() === null) {
            throw new Exception('Primary Key does not contain a value');
        }

        return $this->getMapper()
                    ->getDbTable()
                    ->delete('id_posta = ' .
                             $this->getMapper()
                                  ->getDbTable()
                                  ->getAdapter()
                                  ->quote($this->getIdPosta()));
    }
}
