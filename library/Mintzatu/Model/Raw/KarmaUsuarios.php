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
class Mintzatu_Model_Raw_KarmaUsuarios extends Mintzatu_Model_Raw_ModelAbstract
{
    /**
     * Database var type mediumint(8) unsigned
     *
     * @var int
     */
    protected $_IdUsuario;

    /**
     * Database var type varchar(255)
     *
     * @var string
     */
    protected $_Login;

    /**
     * Database var type varchar(40)
     *
     * @var string
     */
    protected $_Pass;

    /**
     * Database var type varchar(255)
     *
     * @var string
     */
    protected $_Nombre;

    /**
     * Database var type varchar(100)
     *
     * @var string
     */
    protected $_Email;

    /**
     * Database var type varchar(150)
     *
     * @var string
     */
    protected $_Apellidos;

    /**
     * Database var type datetime
     *
     * @var string
     */
    protected $_FechaNacimiento;

    /**
     * Database var type timestamp
     *
     * @var string
     */
    protected $_FechaInsert;



    /**
     * Dependent relation karma_rel_usuarios_roles_ibfk_1
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
            'id_usuario'=>'IdUsuario',
            'login'=>'Login',
            'pass'=>'Pass',
            'nombre'=>'Nombre',
            'email'=>'Email',
            'apellidos'=>'Apellidos',
            'fecha_nacimiento'=>'FechaNacimiento',
            'fecha_insert'=>'FechaInsert',
        ));
        
        $this->setMultiLangColumnsList(array(
        ));

        $this->setAvailableLangs(array('usuario','nacimiento','insert'));

        $this->setParentList(array(
        ));

        $this->setDependentList(array(
            'KarmaRelUsuariosRolesIbfk1' => array(
                    'property' => 'KarmaRelUsuariosRoles',
                    'table_name' => 'KarmaRelUsuariosRoles',
                ),
        ));
 
        $this->setOnDeleteCascadeRelationships(array(
        	'karma_rel_usuarios_roles_ibfk_1'
        ));

		parent::__construct();
    }




    /**
     * Sets column id_usuario
     *
     * @param int $data
     * @return Mintzatu_Model_KarmaUsuarios
     */
    public function setIdUsuario($data)
    {
        $this->_IdUsuario = $data;
        return $this;
    }

    /**
     * Gets column id_usuario
     *
     * @return int
     */
    public function getIdUsuario()
    {
 
        return $this->_IdUsuario;
    }


    /**
     * Sets column login
     *
     * @param string $data
     * @return Mintzatu_Model_KarmaUsuarios
     */
    public function setLogin($data)
    {
        $this->_Login = $data;
        return $this;
    }

    /**
     * Gets column login
     *
     * @return string
     */
    public function getLogin()
    {
 
        return $this->_Login;
    }


    /**
     * Sets column pass
     *
     * @param string $data
     * @return Mintzatu_Model_KarmaUsuarios
     */
    public function setPass($data)
    {
        $this->_Pass = $data;
        return $this;
    }

    /**
     * Gets column pass
     *
     * @return string
     */
    public function getPass()
    {
 
        return $this->_Pass;
    }


    /**
     * Sets column nombre
     *
     * @param string $data
     * @return Mintzatu_Model_KarmaUsuarios
     */
    public function setNombre($data)
    {
        $this->_Nombre = $data;
        return $this;
    }

    /**
     * Gets column nombre
     *
     * @return string
     */
    public function getNombre()
    {
 
        return $this->_Nombre;
    }


    /**
     * Sets column email
     *
     * @param string $data
     * @return Mintzatu_Model_KarmaUsuarios
     */
    public function setEmail($data)
    {
        $this->_Email = $data;
        return $this;
    }

    /**
     * Gets column email
     *
     * @return string
     */
    public function getEmail()
    {
 
        return $this->_Email;
    }


    /**
     * Sets column apellidos
     *
     * @param string $data
     * @return Mintzatu_Model_KarmaUsuarios
     */
    public function setApellidos($data)
    {
        $this->_Apellidos = $data;
        return $this;
    }

    /**
     * Gets column apellidos
     *
     * @return string
     */
    public function getApellidos()
    {
 
        return $this->_Apellidos;
    }


    /**
     * Sets column fecha_nacimiento. Stored in ISO 8601 format.
     *
     * @param string|Zend_Date $date
     * @return Mintzatu_Model_KarmaUsuarios
     */
    public function setFechaNacimiento($data)
    {
		if (! is_null($data) and ! $data instanceof Zend_Date) {

			$data = new Zend_Date($data, Zend_Date::ISO_8601, 'es_ES');
		}

        $this->_FechaNacimiento = $data;
        return $this;
    }

    /**
     * Gets column fecha_nacimiento
     *
     * @param boolean $returnZendDate
     * @return Zend_Date|null|string Zend_Date representation of this datetime if enabled, or ISO 8601 string if not
     */
    public function getFechaNacimiento($returnZendDate = false)
    {

		if (is_null($this->_FechaNacimiento)) {

			return null;
		}

        if ($returnZendDate) {

            return $this->_FechaNacimiento;
        }

        return $this->_FechaNacimiento->setTimezone(date_default_timezone_get())->toString('yyyy-MM-dd HH:mm:ss');
    }


    /**
     * Sets column fecha_insert
     *
     * @param string $data
     * @return Mintzatu_Model_KarmaUsuarios
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
     * Sets dependent relations karma_rel_usuarios_roles_ibfk_1
     *
     * @param array $data An array of Mintzatu_Model_KarmaRelUsuariosRoles
     * @return Mintzatu_Model_KarmaUsuarios
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
     * Sets dependent relations karma_rel_usuarios_roles_ibfk_1
     *
     * @param Mintzatu_Model_KarmaRelUsuariosRoles $data
     * @return Mintzatu_Model_KarmaUsuarios
     */
    public function addKarmaRelUsuariosRoles(Mintzatu_Model_KarmaRelUsuariosRoles $data)
    {
        $this->_KarmaRelUsuariosRoles[] = $data;
        return $this;
    }

    /**
     * Gets dependent karma_rel_usuarios_roles_ibfk_1
     *
     * @param boolean $load Load the object if it is not already
     * @return array The array of Mintzatu_Model_KarmaRelUsuariosRoles
     */
    public function getKarmaRelUsuariosRoles($load = true)
    {
        if ($this->_KarmaRelUsuariosRoles === null && $load) {
            $this->getMapper()->loadRelated('KarmaRelUsuariosRolesIbfk1', $this);
        }

        return $this->_KarmaRelUsuariosRoles;
    }

    /**
     * Returns the mapper class for this model
     *
     * @return \Mappers\Sql\KarmaUsuarios
     */
    public function getMapper()
    {
        if ($this->_mapper === null) {

            \Zend_Loader_Autoloader::getInstance()->suppressNotFoundWarnings(true);

            if (class_exists('\Mappers\Sql\KarmaUsuarios')) {

                $this->setMapper(new \Mappers\Sql\KarmaUsuarios);

            } else if (class_exists('\Mappers\Soap\KarmaUsuarios')) {

                $this->setMapper(new \Mappers\Soap\KarmaUsuarios);

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
     * @return null | Mintzatu_Model_Validator_KarmaUsuarios
     */
    public function getValidator()
    {
        if ($this->_validator === null) {

            if (class_exists('Mintzatu_Validator_KarmaUsuarios')) {
            
                $this->setValidator(new Mintzatu_Validator_KarmaUsuarios);
            }
        }

        return $this->_validator;
    }

    /**
     * Deletes current row by deleting the row that matches the primary key
     *
	 * @see \Mappers\Sql\KarmaUsuarios::delete
     * @return int|boolean Number of rows deleted or boolean if doing soft delete
     */
    public function deleteRowByPrimaryKey()
    {
        if ($this->getIdUsuario() === null) {
            throw new Exception('Primary Key does not contain a value');
        }

        return $this->getMapper()
                    ->getDbTable()
                    ->delete('id_usuario = ' .
                             $this->getMapper()
                                  ->getDbTable()
                                  ->getAdapter()
                                  ->quote($this->getIdUsuario()));
    }
}
