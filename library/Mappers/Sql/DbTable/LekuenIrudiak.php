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
 * Table definition for lekuen_irudiak
 *
 * @package Mintzatu_Mappers\Sql\DbTable
 * @subpackage DbTable
 * @author <Lander Ontoria Gardeazabal>
 */

namespace Mappers\Sql\DbTable;
class LekuenIrudiak extends TableAbstract
{
    /**
     * $_name - name of database table
     *
     * @var string
     */
    protected $_name = 'lekuen_irudiak';

    /**
     * $_id - this is the primary key name
     *
     * @var int
     */
    protected $_id = 'id_irudia';

    protected $_sequence = true; // int 

    protected $_referenceMap = array(
        'LekuenIrudiakIbfk1' => array(
          	'columns' => 'id_lekua',
            'refTableClass' => 'Mappers\\Sql\\DbTable\\Lekuak',
            'refColumns' => 'id_lekua'
        )
    );
        
    protected $_metadata = array (
	  'id_irudia' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'lekuen_irudiak',
	    'COLUMN_NAME' => 'id_irudia',
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
	  'id_lekua' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'lekuen_irudiak',
	    'COLUMN_NAME' => 'id_lekua',
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
	  'id_erabiltzailea' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'lekuen_irudiak',
	    'COLUMN_NAME' => 'id_erabiltzailea',
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
	  'irudi_izena' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'lekuen_irudiak',
	    'COLUMN_NAME' => 'irudi_izena',
	    'COLUMN_POSITION' => 4,
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
	  'irudi_tamaina' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'lekuen_irudiak',
	    'COLUMN_NAME' => 'irudi_tamaina',
	    'COLUMN_POSITION' => 5,
	    'DATA_TYPE' => 'varchar',
	    'DEFAULT' => NULL,
	    'NULLABLE' => true,
	    'LENGTH' => '50',
	    'SCALE' => NULL,
	    'PRECISION' => NULL,
	    'UNSIGNED' => NULL,
	    'PRIMARY' => false,
	    'PRIMARY_POSITION' => NULL,
	    'IDENTITY' => false,
	  ),
	  'irudi_mota' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'lekuen_irudiak',
	    'COLUMN_NAME' => 'irudi_mota',
	    'COLUMN_POSITION' => 6,
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
	  'iruzkina' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'lekuen_irudiak',
	    'COLUMN_NAME' => 'iruzkina',
	    'COLUMN_POSITION' => 7,
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
	  'datetime' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'lekuen_irudiak',
	    'COLUMN_NAME' => 'datetime',
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
