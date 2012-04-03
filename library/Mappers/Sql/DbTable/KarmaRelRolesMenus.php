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
 * Table definition for karma_rel_roles_menus
 *
 * @package Mintzatu_Mappers\Sql\DbTable
 * @subpackage DbTable
 * @author <Lander Ontoria Gardeazabal>
 */

namespace Mappers\Sql\DbTable;
class KarmaRelRolesMenus extends TableAbstract
{
    /**
     * $_name - name of database table
     *
     * @var string
     */
    protected $_name = 'karma_rel_roles_menus';

    /**
     * $_id - this is the primary key name
     *
     * @var int
     */
    protected $_id = 'id_rel';

    protected $_sequence = true; // int 

    protected $_referenceMap = array(
        'KarmaRelRolesMenusIbfk1' => array(
          	'columns' => 'id_rol',
            'refTableClass' => 'Mappers\\Sql\\DbTable\\KarmaRoles',
            'refColumns' => 'id_rol'
        )
    );
        
    protected $_metadata = array (
	  'id_rel' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'karma_rel_roles_menus',
	    'COLUMN_NAME' => 'id_rel',
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
	  'id_rol' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'karma_rel_roles_menus',
	    'COLUMN_NAME' => 'id_rol',
	    'COLUMN_POSITION' => 2,
	    'DATA_TYPE' => 'mediumint',
	    'DEFAULT' => NULL,
	    'NULLABLE' => false,
	    'LENGTH' => NULL,
	    'SCALE' => NULL,
	    'PRECISION' => NULL,
	    'UNSIGNED' => true,
	    'PRIMARY' => false,
	    'PRIMARY_POSITION' => NULL,
	    'IDENTITY' => false,
	  ),
	  'key_menu' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'karma_rel_roles_menus',
	    'COLUMN_NAME' => 'key_menu',
	    'COLUMN_POSITION' => 3,
	    'DATA_TYPE' => 'varchar',
	    'DEFAULT' => NULL,
	    'NULLABLE' => false,
	    'LENGTH' => '50',
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
	    'TABLE_NAME' => 'karma_rel_roles_menus',
	    'COLUMN_NAME' => 'fecha_insert',
	    'COLUMN_POSITION' => 4,
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
