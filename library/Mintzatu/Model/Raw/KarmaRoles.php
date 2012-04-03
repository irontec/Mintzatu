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
class Mintzatu_Model_Raw_KarmaRoles extends Mintzatu_Model_Raw_ModelAbstract
{
    /**
     * Database var type mediumint(8) unsigned
     *
     * @var int
     */
    protected $_IdRol;

    /**
     * Database var type varchar(40)
     *
     * @var string
     */
    protected $_Rol;

    /**
     * Database var type varchar(255)
     *
     * @var string
     */
    protected $_Descripcion;

    /**
     * Database var type enum('0','1')
     *
     * @var string
     */
    protected $_Borrado;

    /**
     * Database var type timestamp
     *
     * @var string
     */
    protected $_FechaInsert;



    /**
     * Dependent relation karma_rel_roles_menus_ibfk_1
     * Type: One-to-Many relationship
     *
     * @var Mintzatu_Model_KarmaRelRolesMenus[]
     */
    protected $_KarmaRelRolesMenus;

    /**
     * Dependent relation karma_rel_usuarios_roles_ibfk_2
     * Type: One-to-Many relationship
     *
     * @var Mintzatu_Model_KarmaRelUsuariosRoles[]
     */
    protected $_KarmaRelUsuariosRoles;

    /**
     * Sets up column and relationship lists
     */
    public function __construct()
    {
        parent::init();
        $this->setColumnsList(array(
            'id_rol'=>'IdRol',
            'rol'=>'Rol',
            'descripcion'=>'Descripcion',
            'borrado'=>'Borrado',
            'fecha_insert'=>'FechaInsert',
        ));
        
        $this->setMultiLangColumnsList(array(
        ));

        $this->setAvailableLangs(array('rol','insert'));

        $this->setParentList(array(
        ));

        $this->setDependentList(array(
            'KarmaRelRolesMenusIbfk1' => array(
                    'property' => 'KarmaRelRolesMenus',
                    'table_name' => 'KarmaRelRolesMenus',
                ),
            'KarmaRelUsuariosRolesIbfk2' => array(
                    'property' => 'KarmaRelUsuariosRoles',
                    'table_name' => 'KarmaRelUsuariosRoles',
                ),
        ));
 
        $this->setOnDeleteCascadeRelationships(array(
        	'karma_rel_roles_menus_ibfk_1',
        	'karma_rel_usuarios_roles_ibfk_2'
        ));

		parent::__construct();
    }




    /**
     * Sets column id_rol
     *
     * @param int $data
     * @return Mintzatu_Model_KarmaRoles
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
     * Sets column rol
     *
     * @param string $data
     * @return Mintzatu_Model_KarmaRoles
     */
    public function setRol($data)
    {
        $this->_Rol = $data;
        return $this;
    }

    /**
     * Gets column rol
     *
     * @return string
     */
    public function getRol()
    {
 
        return $this->_Rol;
    }


    /**
     * Sets column descripcion
     *
     * @param string $data
     * @return Mintzatu_Model_KarmaRoles
     */
    public function setDescripcion($data)
    {
        $this->_Descripcion = $data;
        return $this;
    }

    /**
     * Gets column descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
 
        return $this->_Descripcion;
    }


    /**
     * Sets column borrado
     *
     * @param string $data
     * @return Mintzatu_Model_KarmaRoles
     */
    public function setBorrado($data)
    {
        $this->_Borrado = $data;
        return $this;
    }

    /**
     * Gets column borrado
     *
     * @return string
     */
    public function getBorrado()
    {
 
        return $this->_Borrado;
    }


    /**
     * Sets column fecha_insert
     *
     * @param string $data
     * @return Mintzatu_Model_KarmaRoles
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
     * Sets dependent relations karma_rel_roles_menus_ibfk_1
     *
     * @param array $data An array of Mintzatu_Model_KarmaRelRolesMenus
     * @return Mintzatu_Model_KarmaRoles
     */
    public function setKarmaRelRolesMenus(array $data, $deleteOrphans = false)
    {
    	if ($deleteOrphans === true) {

			if ($this->_KarmaRelRolesMenus === null) {

				$this->getKarmaRelRolesMenus();
			}

			$oldContacts = $this->_KarmaRelRolesMenus;

			if (is_array($oldContacts)) {

				$dataPKs = array();

				foreach ($data as $newItem) {

					if (is_numeric($pk = $newItem->getPrimaryKey())) {

						$dataPKs[] = $pk;
					}
				}
	
				foreach ($oldContacts as $oldItem) {

					if (! in_array($oldItem->getPrimaryKey(), $dataPKs)) {

						$this->_orphans[] = $oldItem;
					}	
				}
			}
    	}

        $this->_KarmaRelRolesMenus = array();

        foreach ($data as $object) {
            $this->addKarmaRelRolesMenus($object);
        }

        return $this;
    }

    /**
     * Sets dependent relations karma_rel_roles_menus_ibfk_1
     *
     * @param Mintzatu_Model_KarmaRelRolesMenus $data
     * @return Mintzatu_Model_KarmaRoles
     */
    public function addKarmaRelRolesMenus(Mintzatu_Model_KarmaRelRolesMenus $data)
    {
        $this->_KarmaRelRolesMenus[] = $data;
        return $this;
    }

    /**
     * Gets dependent karma_rel_roles_menus_ibfk_1
     *
     * @param boolean $load Load the object if it is not already
     * @return array The array of Mintzatu_Model_KarmaRelRolesMenus
     */
    public function getKarmaRelRolesMenus($load = true)
    {
        if ($this->_KarmaRelRolesMenus === null && $load) {
            $this->getMapper()->loadRelated('KarmaRelRolesMenusIbfk1', $this);
        }

        return $this->_KarmaRelRolesMenus;
    }

    /**
     * Sets dependent relations karma_rel_usuarios_roles_ibfk_2
     *
     * @param array $data An array of Mintzatu_Model_KarmaRelUsuariosRoles
     * @return Mintzatu_Model_KarmaRoles
     */
    public function setKarmaRelUsuariosRoles(array $data, $deleteOrphans = false)
    {
    	if ($deleteOrphans === true) {

			if ($this->_KarmaRelUsuariosRoles === null) {

				$this->getKarmaRelUsuariosRoles();
			}

			$oldContacts = $this->_KarmaRelUsuariosRoles;

			if (is_array($oldContacts)) {

				$dataPKs = array();

				foreach ($data as $newItem) {

					if (is_numeric($pk = $newItem->getPrimaryKey())) {

						$dataPKs[] = $pk;
					}
				}
	
				foreach ($oldContacts as $oldItem) {

					if (! in_array($oldItem->getPrimaryKey(), $dataPKs)) {

						$this->_orphans[] = $oldItem;
					}	
				}
			}
    	}

        $this->_KarmaRelUsuariosRoles = array();

        foreach ($data as $object) {
            $this->addKarmaRelUsuariosRoles($object);
        }

        return $this;
    }

    /**
     * Sets dependent relations karma_rel_usuarios_roles_ibfk_2
     *
     * @param Mintzatu_Model_KarmaRelUsuariosRoles $data
     * @return Mintzatu_Model_KarmaRoles
     */
    public function addKarmaRelUsuariosRoles(Mintzatu_Model_KarmaRelUsuariosRoles $data)
    {
        $this->_KarmaRelUsuariosRoles[] = $data;
        return $this;
    }

    /**
     * Gets dependent karma_rel_usuarios_roles_ibfk_2
     *
     * @param boolean $load Load the object if it is not already
     * @return array The array of Mintzatu_Model_KarmaRelUsuariosRoles
     */
    public function getKarmaRelUsuariosRoles($load = true)
    {
        if ($this->_KarmaRelUsuariosRoles === null && $load) {
            $this->getMapper()->loadRelated('KarmaRelUsuariosRolesIbfk2', $this);
        }

        return $this->_KarmaRelUsuariosRoles;
    }

    /**
     * Returns the mapper class for this model
     *
     * @return \Mappers\Sql\KarmaRoles
     */
    public function getMapper()
    {
        if ($this->_mapper === null) {

            \Zend_Loader_Autoloader::getInstance()->suppressNotFoundWarnings(true);

            if (class_exists('\Mappers\Sql\KarmaRoles')) {

                $this->setMapper(new \Mappers\Sql\KarmaRoles);

            } else if (class_exists('\Mappers\Soap\KarmaRoles')) {

                $this->setMapper(new \Mappers\Soap\KarmaRoles);

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
     * @return null | Mintzatu_Model_Validator_KarmaRoles
     */
    public function getValidator()
    {
        if ($this->_validator === null) {

            if (class_exists('Mintzatu_Validator_KarmaRoles')) {
            
                $this->setValidator(new Mintzatu_Validator_KarmaRoles);
            }
        }

        return $this->_validator;
    }

    /**
     * Deletes current row by deleting the row that matches the primary key
     *
	 * @see \Mappers\Sql\KarmaRoles::delete
     * @return int|boolean Number of rows deleted or boolean if doing soft delete
     */
    public function deleteRowByPrimaryKey()
    {
        if ($this->getIdRol() === null) {
            throw new Exception('Primary Key does not contain a value');
        }

        return $this->getMapper()
                    ->getDbTable()
                    ->delete('id_rol = ' .
                             $this->getMapper()
                                  ->getDbTable()
                                  ->getAdapter()
                                  ->quote($this->getIdRol()));
    }
}
