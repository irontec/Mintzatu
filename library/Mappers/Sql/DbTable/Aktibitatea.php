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
 * Table definition for aktibitatea
 *
 * @package Mintzatu_Mappers\Sql\DbTable
 * @subpackage DbTable
 * @author <Lander Ontoria Gardeazabal>
 */

namespace Mappers\Sql\DbTable;
class Aktibitatea extends TableAbstract
{
    /**
     * $_name - name of database table
     *
     * @var string
     */
    protected $_name = 'aktibitatea';

    /**
     * $_id - this is the primary key name
     *
     * @var int
     */
    protected $_id = 'id_aktibitatea';

    protected $_sequence = true; // int 

    protected $_referenceMap = array(
        'AktibitateaIbfk1' => array(
          	'columns' => 'id_lerroa',
            'refTableClass' => 'Mappers\\Sql\\DbTable\\Lekuak',
            'refColumns' => 'id_lekua'
        )
    );
        
    protected $_metadata = array (
	  'id_aktibitatea' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'aktibitatea',
	    'COLUMN_NAME' => 'id_aktibitatea',
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
	  'id_lerroa' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'aktibitatea',
	    'COLUMN_NAME' => 'id_lerroa',
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
	  'taula' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'aktibitatea',
	    'COLUMN_NAME' => 'taula',
	    'COLUMN_POSITION' => 3,
	    'DATA_TYPE' => 'varchar',
	    'DEFAULT' => NULL,
	    'NULLABLE' => false,
	    'LENGTH' => '250',
	    'SCALE' => NULL,
	    'PRECISION' => NULL,
	    'UNSIGNED' => NULL,
	    'PRIMARY' => false,
	    'PRIMARY_POSITION' => NULL,
	    'IDENTITY' => false,
	  ),
	  'akzioa' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'aktibitatea',
	    'COLUMN_NAME' => 'akzioa',
	    'COLUMN_POSITION' => 4,
	    'DATA_TYPE' => 'enum(\'erabiltzaileberria\',\'lekuberria\',\'checkina\')',
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
	    'TABLE_NAME' => 'aktibitatea',
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
