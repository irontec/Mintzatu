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
 * Table definition for erabiltzaileak
 *
 * @package Mintzatu_Mappers\Sql\DbTable
 * @subpackage DbTable
 * @author <Lander Ontoria Gardeazabal>
 */

namespace Mappers\Sql\DbTable;
class Erabiltzaileak extends TableAbstract
{
    /**
     * $_name - name of database table
     *
     * @var string
     */
    protected $_name = 'erabiltzaileak';

    /**
     * $_id - this is the primary key name
     *
     * @var int
     */
    protected $_id = 'id_erabiltzaile';

    protected $_sequence = true; // int 

    
    protected $_dependentTables = array(
        'Checks',
        'Lekuak',
        'RelErabiltzaileak'
    );    
    protected $_metadata = array (
	  'id_erabiltzaile' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'erabiltzaileak',
	    'COLUMN_NAME' => 'id_erabiltzaile',
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
	  'erabiltzailea' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'erabiltzaileak',
	    'COLUMN_NAME' => 'erabiltzailea',
	    'COLUMN_POSITION' => 2,
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
	  'pasahitza' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'erabiltzaileak',
	    'COLUMN_NAME' => 'pasahitza',
	    'COLUMN_POSITION' => 3,
	    'DATA_TYPE' => 'char',
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
	  'rekuperatu' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'erabiltzaileak',
	    'COLUMN_NAME' => 'rekuperatu',
	    'COLUMN_POSITION' => 4,
	    'DATA_TYPE' => 'char',
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
	  'izena' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'erabiltzaileak',
	    'COLUMN_NAME' => 'izena',
	    'COLUMN_POSITION' => 5,
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
	  'abizenak' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'erabiltzaileak',
	    'COLUMN_NAME' => 'abizenak',
	    'COLUMN_POSITION' => 6,
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
	  'herria' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'erabiltzaileak',
	    'COLUMN_NAME' => 'herria',
	    'COLUMN_POSITION' => 7,
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
	  'irudi_izena' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'erabiltzaileak',
	    'COLUMN_NAME' => 'irudi_izena',
	    'COLUMN_POSITION' => 8,
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
	  'irudi_tamaina' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'erabiltzaileak',
	    'COLUMN_NAME' => 'irudi_tamaina',
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
	  'irudi_mota' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'erabiltzaileak',
	    'COLUMN_NAME' => 'irudi_mota',
	    'COLUMN_POSITION' => 10,
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
	  'deskribapena' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'erabiltzaileak',
	    'COLUMN_NAME' => 'deskribapena',
	    'COLUMN_POSITION' => 11,
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
	  'posta' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'erabiltzaileak',
	    'COLUMN_NAME' => 'posta',
	    'COLUMN_POSITION' => 12,
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
	  'facebook' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'erabiltzaileak',
	    'COLUMN_NAME' => 'facebook',
	    'COLUMN_POSITION' => 13,
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
	  'twitter' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'erabiltzaileak',
	    'COLUMN_NAME' => 'twitter',
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
	  'jaiotze_data' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'erabiltzaileak',
	    'COLUMN_NAME' => 'jaiotze_data',
	    'COLUMN_POSITION' => 15,
	    'DATA_TYPE' => 'date',
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
	    'TABLE_NAME' => 'erabiltzaileak',
	    'COLUMN_NAME' => 'url',
	    'COLUMN_POSITION' => 16,
	    'DATA_TYPE' => 'varchar',
	    'DEFAULT' => NULL,
	    'NULLABLE' => true,
	    'LENGTH' => '400',
	    'SCALE' => NULL,
	    'PRECISION' => NULL,
	    'UNSIGNED' => NULL,
	    'PRIMARY' => false,
	    'PRIMARY_POSITION' => NULL,
	    'IDENTITY' => false,
	  ),
	  'alta' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'erabiltzaileak',
	    'COLUMN_NAME' => 'alta',
	    'COLUMN_POSITION' => 17,
	    'DATA_TYPE' => 'datetime',
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
	  'aldaketa' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'erabiltzaileak',
	    'COLUMN_NAME' => 'aldaketa',
	    'COLUMN_POSITION' => 18,
	    'DATA_TYPE' => 'datetime',
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
	  'giltza' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'erabiltzaileak',
	    'COLUMN_NAME' => 'giltza',
	    'COLUMN_POSITION' => 19,
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
	  'aktibatua' => 
	  array (
	    'SCHEMA_NAME' => NULL,
	    'TABLE_NAME' => 'erabiltzaileak',
	    'COLUMN_NAME' => 'aktibatua',
	    'COLUMN_POSITION' => 20,
	    'DATA_TYPE' => 'enum(\'0\',\'1\')',
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
