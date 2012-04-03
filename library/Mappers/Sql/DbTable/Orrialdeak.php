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
 * Table definition for orrialdeak
 *
 * @package Mintzatu_Mappers\Sql\DbTable
 * @subpackage DbTable
 * @author <Lander Ontoria Gardeazabal>
 */

namespace Mappers\Sql\DbTable;
class Orrialdeak extends TableAbstract
{
    /**
     * $_name - name of database table
     *
     * @var string
     */
    protected $_name = 'orrialdeak';

    /**
     * $_id - this is the primary key name
     *
     * @var int
     */
    protected $_id = 'id_orrialde';

    protected $_sequence = true; // int 

    
        
    protected $_metadata = array (
	  'id_orrialde' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'orrialdeak',
	    'COLUMN_NAME' => 'id_orrialde',
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
	  'identifikatzailea' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'orrialdeak',
	    'COLUMN_NAME' => 'identifikatzailea',
	    'COLUMN_POSITION' => 2,
	    'DATA_TYPE' => 'varchar',
	    'DEFAULT' => NULL,
	    'NULLABLE' => false,
	    'LENGTH' => '300',
	    'SCALE' => NULL,
	    'PRECISION' => NULL,
	    'UNSIGNED' => NULL,
	    'PRIMARY' => false,
	    'PRIMARY_POSITION' => NULL,
	    'IDENTITY' => false,
	  ),
	  'titulua' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'orrialdeak',
	    'COLUMN_NAME' => 'titulua',
	    'COLUMN_POSITION' => 3,
	    'DATA_TYPE' => 'varchar',
	    'DEFAULT' => NULL,
	    'NULLABLE' => false,
	    'LENGTH' => '350',
	    'SCALE' => NULL,
	    'PRECISION' => NULL,
	    'UNSIGNED' => NULL,
	    'PRIMARY' => false,
	    'PRIMARY_POSITION' => NULL,
	    'IDENTITY' => false,
	  ),
	  'edukia' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'orrialdeak',
	    'COLUMN_NAME' => 'edukia',
	    'COLUMN_POSITION' => 4,
	    'DATA_TYPE' => 'text',
	    'DEFAULT' => NULL,
	    'NULLABLE' => false,
	    'LENGTH' => NULL,
	    'SCALE' => NULL,
	    'PRECISION' => NULL,
	    'UNSIGNED' => NULL,
	    'PRIMARY' => false,
	    'PRIMARY_POSITION' => NULL,
	    'IDENTITY' => false,
	  ),
	  'noiz' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'orrialdeak',
	    'COLUMN_NAME' => 'noiz',
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
