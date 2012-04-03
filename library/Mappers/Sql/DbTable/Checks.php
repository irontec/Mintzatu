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
 * Table definition for checks
 *
 * @package Mintzatu_Mappers\Sql\DbTable
 * @subpackage DbTable
 * @author <Lander Ontoria Gardeazabal>
 */

namespace Mappers\Sql\DbTable;
class Checks extends TableAbstract
{
    /**
     * $_name - name of database table
     *
     * @var string
     */
    protected $_name = 'checks';

    /**
     * $_id - this is the primary key name
     *
     * @var int
     */
    protected $_id = 'id_check';

    protected $_sequence = true; // int 

    protected $_referenceMap = array(
        'ChecksIbfk2' => array(
          	'columns' => 'id_lekua',
            'refTableClass' => 'Mappers\\Sql\\DbTable\\Lekuak',
            'refColumns' => 'id_lekua'
        ),
        'ChecksIbfk3' => array(
          	'columns' => 'id_erabiltzaile',
            'refTableClass' => 'Mappers\\Sql\\DbTable\\Erabiltzaileak',
            'refColumns' => 'id_erabiltzaile'
        )
    );
        
    protected $_metadata = array (
	  'id_check' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'checks',
	    'COLUMN_NAME' => 'id_check',
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
	  'id_erabiltzaile' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'checks',
	    'COLUMN_NAME' => 'id_erabiltzaile',
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
	  'id_lekua' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'checks',
	    'COLUMN_NAME' => 'id_lekua',
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
	  'iruzkina' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'checks',
	    'COLUMN_NAME' => 'iruzkina',
	    'COLUMN_POSITION' => 4,
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
	  'noiz' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'checks',
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
	  'nondik' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'checks',
	    'COLUMN_NAME' => 'nondik',
	    'COLUMN_POSITION' => 6,
	    'DATA_TYPE' => 'enum(\'web\',\'app\')',
	    'DEFAULT' => 'app',
	    'NULLABLE' => true,
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
