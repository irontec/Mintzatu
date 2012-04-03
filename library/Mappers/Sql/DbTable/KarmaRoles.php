<?php

/**
 * Application Model DbTables
 *
 * @package Mintzatu_Mappers\Sql\DbTable
 * @subpackage DbTable
 * @author <Lander Ontoria Gardeazabal>
 * @copyright Irontec - Internet y Sistemas sobre GNU/Linux
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Table definition for karma_roles
 *
 * @package Mintzatu_Mappers\Sql\DbTable
 * @subpackage DbTable
 * @author <Lander Ontoria Gardeazabal>
 */

namespace Mappers\Sql\DbTable;
class KarmaRoles extends TableAbstract
{
    /**
     * $_name - name of database table
     *
     * @var string
     */
    protected $_name = 'karma_roles';

    /**
     * $_id - this is the primary key name
     *
     * @var int
     */
    protected $_id = 'id_rol';

    protected $_sequence = true; // int 

    
    protected $_dependentTables = array(
        'KarmaRelRolesMenus',
        'KarmaRelUsuariosRoles'
    );    
    protected $_metadata = array (
	  'id_rol' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'karma_roles',
	    'COLUMN_NAME' => 'id_rol',
	    'COLUMN_POSITION' => 1,
	    'DATA_TYPE' => 'mediumint',
	    'DEFAULT' => NULL,
	    'NULLABLE' => false,
	    'LENGTH' => NULL,
	    'SCALE' => NULL,
	    'PRECISION' => NULL,
	    'UNSIGNED' => true,
	    'PRIMARY' => true,
	    'PRIMARY_POSITION' => 1,
	    'IDENTITY' => true,
	  ),
	  'rol' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'karma_roles',
	    'COLUMN_NAME' => 'rol',
	    'COLUMN_POSITION' => 2,
	    'DATA_TYPE' => 'varchar',
	    'DEFAULT' => NULL,
	    'NULLABLE' => false,
	    'LENGTH' => '40',
	    'SCALE' => NULL,
	    'PRECISION' => NULL,
	    'UNSIGNED' => NULL,
	    'PRIMARY' => false,
	    'PRIMARY_POSITION' => NULL,
	    'IDENTITY' => false,
	  ),
	  'descripcion' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'karma_roles',
	    'COLUMN_NAME' => 'descripcion',
	    'COLUMN_POSITION' => 3,
	    'DATA_TYPE' => 'varchar',
	    'DEFAULT' => NULL,
	    'NULLABLE' => true,
	    'LENGTH' => '255',
	    'SCALE' => NULL,
	    'PRECISION' => NULL,
	    'UNSIGNED' => NULL,
	    'PRIMARY' => false,
	    'PRIMARY_POSITION' => NULL,
	    'IDENTITY' => false,
	  ),
	  'borrado' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'karma_roles',
	    'COLUMN_NAME' => 'borrado',
	    'COLUMN_POSITION' => 4,
	    'DATA_TYPE' => 'enum(\'0\',\'1\')',
	    'DEFAULT' => '0',
	    'NULLABLE' => false,
	    'LENGTH' => NULL,
	    'SCALE' => NULL,
	    'PRECISION' => NULL,
	    'UNSIGNED' => NULL,
	    'PRIMARY' => false,
	    'PRIMARY_POSITION' => NULL,
	    'IDENTITY' => false,
	  ),
	  'fecha_insert' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'karma_roles',
	    'COLUMN_NAME' => 'fecha_insert',
	    'COLUMN_POSITION' => 5,
	    'DATA_TYPE' => 'timestamp',
	    'DEFAULT' => 'CURRENT_TIMESTAMP',
	    'NULLABLE' => false,
	    'LENGTH' => NULL,
	    'SCALE' => NULL,
	    'PRECISION' => NULL,
	    'UNSIGNED' => NULL,
	    'PRIMARY' => false,
	    'PRIMARY_POSITION' => NULL,
	    'IDENTITY' => false,
	  ),
	);



}
