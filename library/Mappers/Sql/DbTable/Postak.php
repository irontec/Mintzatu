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
 * Table definition for postak
 *
 * @package Mintzatu_Mappers\Sql\DbTable
 * @subpackage DbTable
 * @author <Lander Ontoria Gardeazabal>
 */

namespace Mappers\Sql\DbTable;
class Postak extends TableAbstract
{
    /**
     * $_name - name of database table
     *
     * @var string
     */
    protected $_name = 'postak';

    /**
     * $_id - this is the primary key name
     *
     * @var int
     */
    protected $_id = 'id_posta';

    protected $_sequence = true; // int 

    
        
    protected $_metadata = array (
	  'id_posta' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'postak',
	    'COLUMN_NAME' => 'id_posta',
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
	    'TABLE_NAME' => 'postak',
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
	  'mezua_html' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'postak',
	    'COLUMN_NAME' => 'mezua_html',
	    'COLUMN_POSITION' => 3,
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
	  'mezua_text' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'postak',
	    'COLUMN_NAME' => 'mezua_text',
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
	);



}
