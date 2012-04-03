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
 * Table definition for rel_erabiltzaileak
 *
 * @package Mintzatu_Mappers\Sql\DbTable
 * @subpackage DbTable
 * @author <Lander Ontoria Gardeazabal>
 */

namespace Mappers\Sql\DbTable;
class RelErabiltzaileak extends TableAbstract
{
    /**
     * $_name - name of database table
     *
     * @var string
     */
    protected $_name = 'rel_erabiltzaileak';

    /**
     * $_id - this is the primary key name
     *
     * @var int
     */
    protected $_id = 'id_rel';

    protected $_sequence = true; // int 

    protected $_referenceMap = array(
        'RelErabiltzaileakIbfk1' => array(
          	'columns' => 'id_erabiltzaile1',
            'refTableClass' => 'Mappers\\Sql\\DbTable\\Erabiltzaileak',
            'refColumns' => 'id_erabiltzaile'
        ),
        'RelErabiltzaileakIbfk2' => array(
          	'columns' => 'id_erabiltzaile2',
            'refTableClass' => 'Mappers\\Sql\\DbTable\\Erabiltzaileak',
            'refColumns' => 'id_erabiltzaile'
        )
    );
        
    protected $_metadata = array (
	  'id_rel' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'rel_erabiltzaileak',
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
	  'id_erabiltzaile1' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'rel_erabiltzaileak',
	    'COLUMN_NAME' => 'id_erabiltzaile1',
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
	  'id_erabiltzaile2' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'rel_erabiltzaileak',
	    'COLUMN_NAME' => 'id_erabiltzaile2',
	    'COLUMN_POSITION' => 3,
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
	  'noiz' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'rel_erabiltzaileak',
	    'COLUMN_NAME' => 'noiz',
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
	  'lagunak' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'rel_erabiltzaileak',
	    'COLUMN_NAME' => 'lagunak',
	    'COLUMN_POSITION' => 5,
	    'DATA_TYPE' => 'enum(\'0\',\'1\',\'2\')',
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
	);



}
