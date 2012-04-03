<?php

/**
 *
 * @package Mintzatu_Model
 * @subpackage Paginator
 * @author <Arkaitz Etxeberria>
 * @copyright Irontec - Internet y Sistemas sobre GNU/Linux
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Paginator class that extends Zend_Paginator_Adapter_DbSelect to return an
 * object instead of an array.
 *
 * @package Mintzatu_Model
 * @subpackage Paginator
 * @author <Arkaitz Etxeberria>
 */
class Mintzatu_Model_Paginator extends Zend_Paginator_Adapter_DbSelect
{
    /**
     * Object mapper
     *
     * @var Mintzatu_Model_MapperAbstract
     */
    protected $_mapper = null;

    /**
     * Constructor.
     *
     * @param Zend_Db_Select $select The select query
     * @param Mintzatu_Model_MapperAbstract $mapper The mapper associated with the object type
     */
    public function __construct(Zend_Db_Select $select, Mintzatu_Model_Mapper_MapperAbstract $mapper)
    {
        $this->_mapper = $mapper;
        parent::__construct($select);
    }

    /**
     * Returns an array of items as objects for a page.
     *
     * @param  integer $offset Page offset
     * @param  integer $itemCountPerPage Number of items per page
     * @return array An array of Mintzatu_ModelAbstract objects
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $items = parent::getItems($offset, $itemCountPerPage);
        $objects = array();

        foreach ($items as $item) {
            $objects[] = $this->_mapper->loadModel($item, null);
        }

        return $objects;
    }
}
