<?php

/**
 * Application Model DbTables
 *
 * @package Mintzatu_Mappers\Sql\DbTable
 * @subpackage DbTable
 * @author <Arkaitz Etxeberria>
 * @copyright Irontec - Internet y Sistemas sobre GNU/Linux
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Table definition for kategoria_irudiak
 *
 * @package Mintzatu_Mappers\Sql\DbTable
 * @subpackage DbTable
 * @author <Arkaitz Etxeberria>
 */

namespace Mappers\Sql\DbTable;
class KategoriaIrudiak extends TableAbstract
{
    /**
     * $_name - name of database table
     *
     * @var string
     */
    protected $_name = 'kategoria_irudiak';

    /**
     * $_id - this is the primary key name
     *
     * @var int
     */
    protected $_id = 'id_irudia';

    protected $_sequence = true; // int 

    protected $_referenceMap = array(
        'KategoriaIrudiakIbfk1' => array(
          	'columns' => 'id_kategoria',
            'refTableClass' => 'Mappers\\Sql\\DbTable\\Kategoriak',
            'refColumns' => 'id_kategoria'
        )
    );
    



}
