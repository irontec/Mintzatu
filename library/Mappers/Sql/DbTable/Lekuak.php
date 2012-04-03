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
 * Table definition for lekuak
 *
 * @package Mintzatu_Mappers\Sql\DbTable
 * @subpackage DbTable
 * @author <Lander Ontoria Gardeazabal>
 */

namespace Mappers\Sql\DbTable;
class Lekuak extends TableAbstract
{
    /**
     * $_name - name of database table
     *
     * @var string
     */
    protected $_name = 'lekuak';

    /**
     * $_id - this is the primary key name
     *
     * @var int
     */
    protected $_id = 'id_lekua';

    protected $_sequence = true; // int 

    protected $_referenceMap = array(
        'LekuakIbfk1' => array(
          	'columns' => 'id_erabiltzaile',
            'refTableClass' => 'Mappers\\Sql\\DbTable\\Erabiltzaileak',
            'refColumns' => 'id_erabiltzaile'
        ),
        'LekuakIbfk2' => array(
          	'columns' => 'id_kategoria',
            'refTableClass' => 'Mappers\\Sql\\DbTable\\Kategoriak',
            'refColumns' => 'id_kategoria'
        )
    );
    protected $_dependentTables = array(
        'Aktibitatea',
        'Checks',
        'LekuenIrudiak'
    );    
    protected $_metadata = array (
	  'id_lekua' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'lekuak',
	    'COLUMN_NAME' => 'id_lekua',
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
	  'id_kategoria' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'lekuak',
	    'COLUMN_NAME' => 'id_kategoria',
	    'COLUMN_POSITION' => 2,
	    'DATA_TYPE' => 'mediumint',
	    'DEFAULT' => NULL,
	    'NULLABLE' => true,
	    'LENGTH' => NULL,
	    'SCALE' => NULL,
	    'PRECISION' => NULL,
	    'UNSIGNED' => true,
	    'PRIMARY' => false,
	    'PRIMARY_POSITION' => NULL,
	    'IDENTITY' => false,
	  ),
	  'id_erabiltzaile' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'lekuak',
	    'COLUMN_NAME' => 'id_erabiltzaile',
	    'COLUMN_POSITION' => 3,
	    'DATA_TYPE' => 'mediumint',
	    'DEFAULT' => NULL,
	    'NULLABLE' => true,
	    'LENGTH' => NULL,
	    'SCALE' => NULL,
	    'PRECISION' => NULL,
	    'UNSIGNED' => true,
	    'PRIMARY' => false,
	    'PRIMARY_POSITION' => NULL,
	    'IDENTITY' => false,
	  ),
	  'izena' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'lekuak',
	    'COLUMN_NAME' => 'izena',
	    'COLUMN_POSITION' => 4,
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
	  'helbidea' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'lekuak',
	    'COLUMN_NAME' => 'helbidea',
	    'COLUMN_POSITION' => 5,
	    'DATA_TYPE' => 'varchar',
	    'DEFAULT' => NULL,
	    'NULLABLE' => true,
	    'LENGTH' => '350',
	    'SCALE' => NULL,
	    'PRECISION' => NULL,
	    'UNSIGNED' => NULL,
	    'PRIMARY' => false,
	    'PRIMARY_POSITION' => NULL,
	    'IDENTITY' => false,
	  ),
	  'postakodea' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'lekuak',
	    'COLUMN_NAME' => 'postakodea',
	    'COLUMN_POSITION' => 6,
	    'DATA_TYPE' => 'int',
	    'DEFAULT' => NULL,
	    'NULLABLE' => true,
	    'LENGTH' => NULL,
	    'SCALE' => NULL,
	    'PRECISION' => NULL,
	    'UNSIGNED' => NULL,
	    'PRIMARY' => false,
	    'PRIMARY_POSITION' => NULL,
	    'IDENTITY' => false,
	  ),
	  'herria' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'lekuak',
	    'COLUMN_NAME' => 'herria',
	    'COLUMN_POSITION' => 7,
	    'DATA_TYPE' => 'varchar',
	    'DEFAULT' => NULL,
	    'NULLABLE' => true,
	    'LENGTH' => '300',
	    'SCALE' => NULL,
	    'PRECISION' => NULL,
	    'UNSIGNED' => NULL,
	    'PRIMARY' => false,
	    'PRIMARY_POSITION' => NULL,
	    'IDENTITY' => false,
	  ),
	  'probintzia' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'lekuak',
	    'COLUMN_NAME' => 'probintzia',
	    'COLUMN_POSITION' => 8,
	    'DATA_TYPE' => 'varchar',
	    'DEFAULT' => NULL,
	    'NULLABLE' => true,
	    'LENGTH' => '300',
	    'SCALE' => NULL,
	    'PRECISION' => NULL,
	    'UNSIGNED' => NULL,
	    'PRIMARY' => false,
	    'PRIMARY_POSITION' => NULL,
	    'IDENTITY' => false,
	  ),
	  'estatua' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'lekuak',
	    'COLUMN_NAME' => 'estatua',
	    'COLUMN_POSITION' => 9,
	    'DATA_TYPE' => 'varchar',
	    'DEFAULT' => NULL,
	    'NULLABLE' => true,
	    'LENGTH' => '300',
	    'SCALE' => NULL,
	    'PRECISION' => NULL,
	    'UNSIGNED' => NULL,
	    'PRIMARY' => false,
	    'PRIMARY_POSITION' => NULL,
	    'IDENTITY' => false,
	  ),
	  'mapa' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'lekuak',
	    'COLUMN_NAME' => 'mapa',
	    'COLUMN_POSITION' => 10,
	    'DATA_TYPE' => 'varchar',
	    'DEFAULT' => NULL,
	    'NULLABLE' => true,
	    'LENGTH' => '500',
	    'SCALE' => NULL,
	    'PRECISION' => NULL,
	    'UNSIGNED' => NULL,
	    'PRIMARY' => false,
	    'PRIMARY_POSITION' => NULL,
	    'IDENTITY' => false,
	  ),
	  'latitudea' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'lekuak',
	    'COLUMN_NAME' => 'latitudea',
	    'COLUMN_POSITION' => 11,
	    'DATA_TYPE' => 'float',
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
	  'longitudea' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'lekuak',
	    'COLUMN_NAME' => 'longitudea',
	    'COLUMN_POSITION' => 12,
	    'DATA_TYPE' => 'float',
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
	  'deskribapena' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'lekuak',
	    'COLUMN_NAME' => 'deskribapena',
	    'COLUMN_POSITION' => 13,
	    'DATA_TYPE' => 'text',
	    'DEFAULT' => NULL,
	    'NULLABLE' => true,
	    'LENGTH' => NULL,
	    'SCALE' => NULL,
	    'PRECISION' => NULL,
	    'UNSIGNED' => NULL,
	    'PRIMARY' => false,
	    'PRIMARY_POSITION' => NULL,
	    'IDENTITY' => false,
	  ),
	  'url' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'lekuak',
	    'COLUMN_NAME' => 'url',
	    'COLUMN_POSITION' => 14,
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
	);



}
