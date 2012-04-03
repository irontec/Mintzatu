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
class Mintzatu_Model_Raw_KarmaRelUsuariosRoles extends Mintzatu_Model_Raw_ModelAbstract
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
    protected $_IdUsuario;

    /**
     * Database var type mediumint(8) unsigned
     *
     * @var int
     */
    protected $_IdRol;

    /**
     * Database var type timestamp
     *
     * @var string
     */
    protected $_FechaInsert;


    /**
     * Parent relation karma_rel_usuarios_roles_ibfk_1
     *
     * @var Mintzatu_Model_KarmaUsuarios
     */
    protected $_KarmaUsuarios;

    /**
     * Parent relation karma_rel_usuarios_roles_ibfk_2
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
            'id_usuario'=>'IdUsuario',
            'id_rol'=>'IdRol',
            'fecha_insert'=>'FechaInsert',
        ));
        
        $this->setMultiLangColumnsList(array(
        ));

        $this->setAvailableLangs(array('rel','usuario','rol','insert'));

        $this->setParentList(array(
            'KarmaRelUsuariosRolesIbfk1'=> array(
                    'property' => 'KarmaUsuarios',
                    'table_name' => 'KarmaUsuarios',
                ),
            'KarmaRelUsuariosRolesIbfk2'=> array(
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
     * @return Mintzatu_Model_KarmaRelUsuariosRoles
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
     * Sets column id_usuario
     *
     * @param int $data
     * @return Mintzatu_Model_KarmaRelUsuariosRoles
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
     * Sets column id_rol
     *
     * @param int $data
     * @return Mintzatu_Model_KarmaRelUsuariosRoles
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
     * Sets column fecha_insert
     *
     * @param string $data
     * @return Mintzatu_Model_KarmaRelUsuariosRoles
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
     * Sets parent relation IdUsuario
     *
     * @param Mintzatu_Model_KarmaUsuarios $data
     * @return Mintzatu_Model_KarmaRelUsuariosRoles
     */
    public function setKarmaUsuarios(Mintzatu_Model_KarmaUsuarios $data)
    {
        $this->_KarmaUsuarios = $data;

        $primary_key = $data->getPrimaryKey();
        if (is_array($primary_key)) {
            $primary_key = $primary_key['id_usuario'];
        }

        $this->setIdUsuario($primary_key);

        return $this;
    }

    /**
     * Gets parent IdUsuario
     *
     * @param boolean $load Load the object if it is not already
     * @return Mintzatu_Model_KarmaUsuarios
     */
    public function getKarmaUsuarios($load = true)
    {
        if ($this->_KarmaUsuarios === null && $load) {
            $this->getMapper()->loadRelated('KarmaRelUsuariosRolesIbfk1', $this);
        }

        return $this->_KarmaUsuarios;
    }

    /**
     * Sets parent relation IdRol
     *
     * @param Mintzatu_Model_KarmaRoles $data
     * @return Mintzatu_Model_KarmaRelUsuariosRoles
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
            $this->getMapper()->loadRelated('KarmaRelUsuariosRolesIbfk2', $this);
        }

        return $this->_KarmaRoles;
    }

    /**
     * Returns the mapper class for this model
     *
     * @return \Mappers\Sql\KarmaRelUsuariosRoles
     */
    public function getMapper()
    {
        if ($this->_mapper === null) {

            \Zend_Loader_Autoloader::getInstance()->suppressNotFoundWarnings(true);

            if (class_exists('\Mappers\Sql\KarmaRelUsuariosRoles')) {

                $this->setMapper(new \Mappers\Sql\KarmaRelUsuariosRoles);

            } else if (class_exists('\Mappers\Soap\KarmaRelUsuariosRoles')) {

                $this->setMapper(new \Mappers\Soap\KarmaRelUsuariosRoles);

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
     * @return null | Mintzatu_Model_Validator_KarmaRelUsuariosRoles
     */
    public function getValidator()
    {
        if ($this->_validator === null) {

            if (class_exists('Mintzatu_Validator_KarmaRelUsuariosRoles')) {
            
                $this->setValidator(new Mintzatu_Validator_KarmaRelUsuariosRoles);
            }
        }

        return $this->_validator;
    }

    /**
     * Deletes current row by deleting the row that matches the primary key
     *
	 * @see \Mappers\Sql\KarmaRelUsuariosRoles::delete
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
