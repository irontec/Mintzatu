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
 * Table definition for karma_usuarios
 *
 * @package Mintzatu_Mappers\Sql\DbTable
 * @subpackage DbTable
 * @author <Lander Ontoria Gardeazabal>
 */

namespace Mappers\Sql\DbTable;
class KarmaUsuarios extends TableAbstract
{
    /**
     * $_name - name of database table
     *
     * @var string
     */
    protected $_name = 'karma_usuarios';

    /**
     * $_id - this is the primary key name
     *
     * @var int
     */
    protected $_id = 'id_usuario';

    protected $_sequence = true; // int 

    
    protected $_dependentTables = array(
        'KarmaRelUsuariosRoles'
    );    
    protected $_metadata = array (
	  'id_usuario' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'karma_usuarios',
	    'COLUMN_NAME' => 'id_usuario',
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
	  'login' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'karma_usuarios',
	    'COLUMN_NAME' => 'login',
	    'COLUMN_POSITION' => 2,
	    'DATA_TYPE' => 'varchar',
	    'DEFAULT' => NULL,
	    'NULLABLE' => false,
	    'LENGTH' => '255',
	    'SCALE' => NULL,
	    'PRECISION' => NULL,
	    'UNSIGNED' => NULL,
	    'PRIMARY' => false,
	    'PRIMARY_POSITION' => NULL,
	    'IDENTITY' => false,
	  ),
	  'pass' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'karma_usuarios',
	    'COLUMN_NAME' => 'pass',
	    'COLUMN_POSITION' => 3,
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
	  'nombre' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'karma_usuarios',
	    'COLUMN_NAME' => 'nombre',
	    'COLUMN_POSITION' => 4,
	    'DATA_TYPE' => 'varchar',
	    'DEFAULT' => NULL,
	    'NULLABLE' => false,
	    'LENGTH' => '255',
	    'SCALE' => NULL,
	    'PRECISION' => NULL,
	    'UNSIGNED' => NULL,
	    'PRIMARY' => false,
	    'PRIMARY_POSITION' => NULL,
	    'IDENTITY' => false,
	  ),
	  'email' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'karma_usuarios',
	    'COLUMN_NAME' => 'email',
	    'COLUMN_POSITION' => 5,
	    'DATA_TYPE' => 'varchar',
	    'DEFAULT' => NULL,
	    'NULLABLE' => true,
	    'LENGTH' => '100',
	    'SCALE' => NULL,
	    'PRECISION' => NULL,
	    'UNSIGNED' => NULL,
	    'PRIMARY' => false,
	    'PRIMARY_POSITION' => NULL,
	    'IDENTITY' => false,
	  ),
	  'apellidos' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'karma_usuarios',
	    'COLUMN_NAME' => 'apellidos',
	    'COLUMN_POSITION' => 6,
	    'DATA_TYPE' => 'varchar',
	    'DEFAULT' => NULL,
	    'NULLABLE' => true,
	    'LENGTH' => '150',
	    'SCALE' => NULL,
	    'PRECISION' => NULL,
	    'UNSIGNED' => NULL,
	    'PRIMARY' => false,
	    'PRIMARY_POSITION' => NULL,
	    'IDENTITY' => false,
	  ),
	  'fecha_nacimiento' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'karma_usuarios',
	    'COLUMN_NAME' => 'fecha_nacimiento',
	    'COLUMN_POSITION' => 7,
	    'DATA_TYPE' => 'datetime',
	    'DEFAULT' => '0000-00-00 00:00:00',
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
	    'TABLE_NAME' => 'karma_usuarios',
	    'COLUMN_NAME' => 'fecha_insert',
	    'COLUMN_POSITION' => 8,
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
