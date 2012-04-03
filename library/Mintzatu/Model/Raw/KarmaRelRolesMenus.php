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
class Mintzatu_Model_Raw_KarmaRelRolesMenus extends Mintzatu_Model_Raw_ModelAbstract
{
    /**
     * Database var type mediumint(8) unsigned
     *
     * @var int
     */
    protected $_IdRel;

    /**
     * Database var type mediumint(8) unsigned
     *
     * @var int
     */
    protected $_IdRol;

    /**
     * Database var type varchar(50)
     *
     * @var string
     */
    protected $_KeyMenu;

    /**
     * Database var type timestamp
     *
     * @var string
     */
    protected $_FechaInsert;


    /**
     * Parent relation karma_rel_roles_menus_ibfk_1
     *
     * @var Mintzatu_Model_KarmaRoles
     */
    protected $_KarmaRoles;


    /**
     * Sets up column and relationship lists
     */
    public function __construct()
    {
        parent::init();
        $this->setColumnsList(array(
            'id_rel'=>'IdRel',
            'id_rol'=>'IdRol',
            'key_menu'=>'KeyMenu',
            'fecha_insert'=>'FechaInsert',
        ));
        
        $this->setMultiLangColumnsList(array(
        ));

        $this->setAvailableLangs(array('rel','rol','menu','insert'));

        $this->setParentList(array(
            'KarmaRelRolesMenusIbfk1'=> array(
                    'property' => 'KarmaRoles',
                    'table_name' => 'KarmaRoles',
                ),
        ));

        $this->setDependentList(array(
        ));

		parent::__construct();
    }




    /**
     * Sets column id_rel
     *
     * @param int $data
     * @return Mintzatu_Model_KarmaRelRolesMenus
     */
    public function setIdRel($data)
    {
        $this->_IdRel = $data;
        return $this;
    }

    /**
     * Gets column id_rel
     *
     * @return int
     */
    public function getIdRel()
    {
 
        return $this->_IdRel;
    }


    /**
     * Sets column id_rol
     *
     * @param int $data
     * @return Mintzatu_Model_KarmaRelRolesMenus
     */
    public function setIdRol($data)
    {
        $this->_IdRol = $data;
        return $this;
    }

    /**
     * Gets column id_rol
     *
     * @return int
     */
    public function getIdRol()
    {
 
        return $this->_IdRol;
    }


    /**
     * Sets column key_menu
     *
     * @param string $data
     * @return Mintzatu_Model_KarmaRelRolesMenus
     */
    public function setKeyMenu($data)
    {
        $this->_KeyMenu = $data;
        return $this;
    }

    /**
     * Gets column key_menu
     *
     * @return string
     */
    public function getKeyMenu()
    {
 
        return $this->_KeyMenu;
    }


    /**
     * Sets column fecha_insert
     *
     * @param string $data
     * @return Mintzatu_Model_KarmaRelRolesMenus
     */
    public function setFechaInsert($data)
    {
        $this->_FechaInsert = $data;
        return $this;
    }

    /**
     * Gets column fecha_insert
     *
     * @return string
     */
    public function getFechaInsert()
    {
 
        return $this->_FechaInsert;
    }

    /**
     * Sets parent relation IdRol
     *
     * @param Mintzatu_Model_KarmaRoles $data
     * @return Mintzatu_Model_KarmaRelRolesMenus
     */
    public function setKarmaRoles(Mintzatu_Model_KarmaRoles $data)
    {
        $this->_KarmaRoles = $data;

        $primary_key = $data->getPrimaryKey();
        if (is_array($primary_key)) {
            $primary_key = $primary_key['id_rol'];
        }

        $this->setIdRol($primary_key);

        return $this;
    }

    /**
     * Gets parent IdRol
     *
     * @param boolean $load Load the object if it is not already
     * @return Mintzatu_Model_KarmaRoles
     */
    public function getKarmaRoles($load = true)
    {
        if ($this->_KarmaRoles === null && $load) {
            $this->getMapper()->loadRelated('KarmaRelRolesMenusIbfk1', $this);
        }

        return $this->_KarmaRoles;
    }

    /**
     * Returns the mapper class for this model
     *
     * @return \Mappers\Sql\KarmaRelRolesMenus
     */
    public function getMapper()
    {
        if ($this->_mapper === null) {

            \Zend_Loader_Autoloader::getInstance()->suppressNotFoundWarnings(true);

            if (class_exists('\Mappers\Sql\KarmaRelRolesMenus')) {

                $this->setMapper(new \Mappers\Sql\KarmaRelRolesMenus);

            } else if (class_exists('\Mappers\Soap\KarmaRelRolesMenus')) {

                $this->setMapper(new \Mappers\Soap\KarmaRelRolesMenus);

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
     * @return null | Mintzatu_Model_Validator_KarmaRelRolesMenus
     */
    public function getValidator()
    {
        if ($this->_validator === null) {

            if (class_exists('Mintzatu_Validator_KarmaRelRolesMenus')) {
            
                $this->setValidator(new Mintzatu_Validator_KarmaRelRolesMenus);
            }
        }

        return $this->_validator;
    }

    /**
     * Deletes current row by deleting the row that matches the primary key
     *
	 * @see \Mappers\Sql\KarmaRelRolesMenus::delete
     * @return int|boolean Number of rows deleted or boolean if doing soft delete
     */
    public function deleteRowByPrimaryKey()
    {
        if ($this->getIdRel() === null) {
            throw new Exception('Primary Key does not contain a value');
        }

        return $this->getMapper()
                    ->getDbTable()
                    ->delete('id_rel = ' .
                             $this->getMapper()
                                  ->getDbTable()
                                  ->getAdapter()
                                  ->quote($this->getIdRel()));
    }
}
