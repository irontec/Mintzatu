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
 * Table definition for karma_img_size
 *
 * @package Mintzatu_Mappers\Sql\DbTable
 * @subpackage DbTable
 * @author <Lander Ontoria Gardeazabal>
 */

namespace Mappers\Sql\DbTable;
class KarmaImgSize extends TableAbstract
{
    /**
     * $_name - name of database table
     *
     * @var string
     */
    protected $_name = 'karma_img_size';

    /**
     * $_id - this is the primary key name
     *
     * @var int
     */
    protected $_id = 'idSize';

    protected $_sequence = true; // int 

    
        
    protected $_metadata = array (
	  'idSize' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'karma_img_size',
	    'COLUMN_NAME' => 'idSize',
	    'COLUMN_POSITION' => 1,
	    'DATA_TYPE' => 'mediumint',
	    'DEFAULT' => NULL,
	    'NULLABLE' => false,
	    'LENGTH' => NULL,
	    'SCALE' => NULL,
	    'PRECISION' => NULL,
	    'UNSIGNED' => NULL,
	    'PRIMARY' => true,
	    'PRIMARY_POSITION' => 1,
	    'IDENTITY' => true,
	  ),
	  'nombre' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'karma_img_size',
	    'COLUMN_NAME' => 'nombre',
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
	  'height' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'karma_img_size',
	    'COLUMN_NAME' => 'height',
	    'COLUMN_POSITION' => 3,
	    'DATA_TYPE' => 'int',
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
	  'width' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'karma_img_size',
	    'COLUMN_NAME' => 'width',
	    'COLUMN_POSITION' => 4,
	    'DATA_TYPE' => 'int',
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
