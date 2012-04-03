<?php
/**
 * Application Models
 *
 * @package Mintzatu_Model_Raw
 * @subpackage Model
 * @author <Lander Ontoria Gardeazabal>
 * @copyright Irontec - Internet y Sistemas sobre GNU/Linux
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Abstract class that is extended by all base models
 *
 * @package Mintzatu_Model_Raw  - rel_erabiltzaileak * @subpackage Model
 * @author <Lander Ontoria Gardeazabal>
 */

abstract class Mintzatu_Model_Raw_ModelAbstract implements Iterator
{
    /**
     * Mapper associated with this model instance
     *
     * @var Mintzatu_Model_ModelAbstract
     */
    protected $_mapper;

    /**
     * Validator associated with this model instance
     *
     * @var Mintzatu_Model_ModelValidatorAbstract
     */
    protected $_validator;

    /**
     * Associative array of columns for this model
     *
     * @var array
     */
    protected $_columnsList;

    /**
     * Associative array of columns for this model
     *
     * @var array
     */
    protected $_multiLangColumnsList;
	
    /**
     * Associative array of multilang columns for this model
     *
     * @var array
     */
	protected $_availableLangs = array();

    /**
     * Associative array of parent relationships for this model
     *
     * @var array
     */
    protected $_parentList;

    /**
     * Associative array of dependent relationships for this model
     *
     * @var array
     */
    protected $_dependentList;

	/**
	 * Orphan elements to remove on save()
	 */
	protected $_orphans  = array();

    /**
     * Sql triggers
     *
     * @var bool
     */
    protected $_onDeleteCascade = array();
    protected $_onDeleteSetNull = array();
	
	/**
	 * Default language for multilang field setters/getters
	 */
	protected $_defaultUserLanguage = '';

    /**
     * Initializes common functionality in Model classes
     */
    protected function init()
    {

    }

    public function __construct() 
    {
    	if (count($this->getAvailableLangs()) > 0) {

            $bootstrap = \Zend_Controller_Front::getInstance()->getParam('bootstrap');

            if (is_null($bootstrap)) {

                $conf = new \Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini',APPLICATION_ENV);
                $conf = (Object) $conf->toArray();

            } else {

                $conf = (Object) $bootstrap->getOptions();
            }

			if ( isset($conf->defaultLanguageZendRegistryKey) ) {

		        if (Zend_Registry::isRegistered($conf->defaultLanguageZendRegistryKey)) {

		        	$this->_defaultUserLanguage = Zend_Registry::get($conf->defaultLanguageZendRegistryKey);
		        }
			}
    	}
    }

    protected function getDefaultUserLanguage()
    {
		return $this->_defaultUserLanguage;    
    }

    /**
     * Set the list of columns associated with this model
     *
     * @param array $data
     * @return Mintzatu_Model_ModelAbstract
     */
    public function setColumnsList($data)
    {
        $this->_columnsList = $data;
        return $this;
    }

    /**
     * Returns columns list array
     *
     * @return array
     */
    public function getColumnsList()
    {
        return $this->_columnsList;
    }
    
    /**
     * Set the list of columns associated with this model
     *
     * @param array $data
     * @return Mintzatu_Model_ModelAbstract
     */
    public function setMultiLangColumnsList($data)
    {
        $this->_multiLangColumnsList = $data;
        return $this;
    }

    /**
     * Returns columns list array
     *
     * @return array
     */
    public function getMultiLangColumnsList()
    {
        return $this->_multiLangColumnsList;
    }

	/**
     * Returns language list array
     *
     * @param array
     * @return EKT_Model_ModelAbstract
	 */
	public function setAvailableLangs($langs)
	{
		$this->_availableLangs = $langs;
		return $this;
	}

    /**
     * Returns columns list array
     *
     * @return array
     */
    public function getAvailableLangs()
    {
        return $this->_availableLangs;
    }

    /**
     * Set the list of relationships associated with this model
     *
     * @param array $data
     * @return Mintzatu_Model_ModelAbstract
     */
    public function setParentList($data)
    {
        $this->_parentList = $data;
        return $this;
    }

    /**
     * Returns relationship list array
     *
     * @return array
     */
    public function getParentList()
    {
        return $this->_parentList;
    }

    /**
     * Set the list of relationships associated with this model
     *
     * @param array $data
     * @return Mintzatu_Model_ModelAbstract
     */
    public function setDependentList($data)
    {
        $this->_dependentList = $data;
        return $this;
    }

    /**
     * Returns relationship list array
     *
     * @return array
     */
    public function getDependentList()
    {
        return $this->_dependentList;
    }

	/**
	 * Get orphan elements
	 */
	public function getOrphans()
	{
		return $this->_orphans;
	}

	public function resetOrphans()
	{
		$this->_orphans = array();
		return $this;
	}

    /*
     * Set the list of relationships to delete when this object is erased
     *
     * @param array $data
     * @return Mintzatu_Model_ModelAbstract
     */
    public function setOnDeleteCascadeRelationships($data)
    {
        $this->_onDeleteCascade = $data;
        return $this;
    }

    /**
     * Return relationships to delete when this object is erased
     *
     * @param array $data
     * @return Mintzatu_Model_ModelAbstract
     */
    public function getOnDeleteCascadeRelationships()
    {
        return $this->_onDeleteCascade;
    }
    
    /*
     * Set the list of relationships to delete when this object is erased
     *
     * @param array $data
     * @return Mintzatu_Model_ModelAbstract
     */
    public function setOnDeleteSetNullRelationships($data)
    {
        $this->_onDeleteSetNull = $data;
        return $this;
    }

    /**
     * Return relationships to delete when this object is erased
     *
     * @param array $data
     * @return Mintzatu_Model_ModelAbstract
     */
    public function getOnDeleteSetNullRelationships()
    {
        return $this->_onDeleteSetNull;
    }

    /**
     * Returns the mapper associated with this model
     *
     * @return Mintzatu_Model_Mapper_MapperAbstract
     */
    public abstract function getMapper();

    /**
     * Sets the mapper class
     *
     * @param Mintzatu_Model_Mapper_MapperAbstract $mapper
     * @return Mintzatu_Model_ModelAbstract
     */
    public function setMapper($mapper)
    {
        $this->_mapper = $mapper;
        return $this;
    }

    public abstract function getValidator ();

    public function setValidator($validator)
    {
        $this->_validator = $validator;
        return $this;
    }

	public function dateTimeToGMT($data)
	{
    	if ('Europe/London' !== date_default_timezone_get()) {

			$date = new DateTime($data);
			$timeZone = new DateTimeZone('Europe/London');
			$date->setTimeZone($timeZone);
			$data = $date->format('Y-m-d H:i:s');
    	}

		return $data;
	}

    /**
     * Converts database column name to php setter/getter function name
     * @param string $column
     */
    public function columnNameToVar($column)
    {
        if (! isset($this->_columnsList[$column])) {
            throw new Exception("column '$column' not found!");
        }

        return $this->_columnsList[$column];
    }

    /**
     * Converts database column name to PHP setter/getter function name
     * @param string $column
     */
    public function varNameToColumn($thevar)
    {
        foreach ($this->_columnsList as $column => $var) {
            if ($var == $thevar) {
                return $column;
            }
        }

        return null;
    }

    /**
     * Recognize methods for Belongs-To cases:
     * <code>findBy&lt;field&gt;()</code>
     * <code>findOneBy&lt;field&gt;()</code>
     * <code>load&lt;relationship&gt;()</code>
     *
     * @param string $method
     * @throws Exception if method does not exist
     * @param array $args
     */
    public function __call($method, array $args)
    {
        $matches = array();
        $result = null;

        if (preg_match('/^find(One)?By(\w+)?$/', $method, $matches)) {
            $methods = get_class_methods($this);
            $check = 'set' . $matches[2];

            $fieldName = $this->varNameToColumn($matches[2]);

            if (! in_array($check, $methods)) {
                throw new Exception(
                    "Invalid field {$matches[2]} requested for table"
                );
            }

            if ($matches[1] != '') {
                $result = $this->getMapper()->findOneByField($fieldName, $args[0],
                                                           $this);
            } else {
                $result = $this->getMapper()->findByField($fieldName, $args[0],
                                                        $this);
            }

            return $result;
        } elseif (preg_match('/load(\w+)/', $method, $matches)) {
            $result = $this->getMapper()->loadRelated($matches[1], $this);

            return $result;
        }

        throw new Exception("Unrecognized method '$method()'");
    }

    /**
     *  __set() is run when writing data to inaccessible properties overloading
     *  it to support setting columns.
     *
     * Example:
     * <code>class->column_name='foo'</code> or <code>class->ColumnName='foo'</code>
     *  will execute the function <code>class->setColumnName('foo')</code>
     *
     * @param string $name
     * @param mixed $value
     * @throws Exception if the property/column does not exist
     */
    public function __set($name, $value)
    {
        $name = $this->columnNameToVar($name);

        $method = 'set' . ucfirst($name);

        if (('mapper' == $name) || ! method_exists($this, $method)) {
            throw new Exception("name:$name value:$value - Invalid property");
        }

        $this->$method($value);
    }

    /**
     * __get() is utilized for reading data from inaccessible properties
     * overloading it to support getting columns value.
     *
     * Example:
     * <code>$foo=class->column_name</code> or <code>$foo=class->ColumnName</code>
     * will execute the function <code>$foo=class->getColumnName()</code>
     *
     * @param string $name
     * @param mixed $value
     * @throws Exception if the property/column does not exist
     * @return mixed
     */
    public function __get($name)
    {
        $method = 'get' . ucfirst($name);

        if (('mapper' == $name) || ! method_exists($this, $method)) {
            $name = $this->columnNameToVar($name);
            $method = 'get' . ucfirst($name);
            if (('mapper' == $name) || ! method_exists($this, $method)) {
                    throw new Exception("name:$name  - Invalid property");
            }
        }

        return $this->$method();
    }

    /**
     * Array of options/values to be set for this model. Options without a
     * matching method are ignored.
     *
     * @param array $options
     * @return Mintzatu_Model_ModelAbstract
     */
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {

            $key = preg_replace_callback('/_(.)/', function ($matches) {
                   		return ucfirst($matches[1]);
                   }, $key);

            $method = 'set' . ucfirst($key);

            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }

        return $this;
    }

    /**
     * Returns the primary key column name
     *
     * @see Mintzatu_Model_DbTable_TableAbstract::getPrimaryKeyName()
     * @return string|array The name or array of names which form the primary key
     */
    public function getPrimaryKeyName()
    {
        return $this->getMapper()->getDbTable()->getPrimaryKeyName();
    }

    /**
     * Returns an associative array of column-value pairings if the primary key
     * is an array of values, or the value of the primary key if not
     *
     * @return any|array
     */
    public function getPrimaryKey()
    {
        $primary_key = $this->getPrimaryKeyName();

        if (is_array($primary_key)) {
            $result = array();
            foreach ($primary_key as $key) {
                $result[$key] = $this->$key;
            }

            return $result;

        } else {
            return $this->$primary_key;
        }

    }

    /**
     * Finds row by primary key
     *
     * @param string|array $primary_key
     * @return Mintzatu_Model_ModelAbstract
     *
     */
    public function find($primary_key)
    {
        $this->getMapper()->find($primary_key, $this);
        return $this;
    }

    /**
     * Returns an array, keys are the field names.
     *
     * @see Mintzatu_Model_Mapper_MapperAbstract::toArray()
     * @return array
     */
    public function toArray()
    {
        return $this->getMapper()->toArray($this);
    }

    /**
     * Saves current loaded row
     *
     *  $ignoreEmptyValues by default is true.
     *  This option will not update columns with empty values or
     *  will insert NULL values if inserting
     *
     * @see Mintzatu_Model_Mapper_MapperAbstract::save()
     * @param boolean $ignoreEmptyValues
     * @param boolean recursive
     * @return boolean If the save was sucessful
     */
    public function save($ignoreEmptyValues = false, $recursive = false, $useTransaction = true)
    {
        return $this->getMapper()->save($this, $ignoreEmptyValues, $recursive, $useTransaction);
    }

    /**
     * Checks if current object values make sense
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->getValidator()->isValid($this->toArray());
    }

    /**
     * Deletes current loaded row
     *
     * @return int
     */
    public function delete()
    {
        return $this->getMapper()->delete($this);
    }

    /**
     * Returns the name of the table that this model represents
     *
     * @return string
     */
    public function getTableName() 
    {
        return $this->getMapper()->getDbTable()->getTableName();
    }

	/**
	 * Setea los mappers a null
	 */
	public function __sleep()
	{
		$this->setMapper(null);
		$vars = get_object_vars($this);

		$attrs = array();

		foreach (array_keys($vars) as $key => $val) {

			$attrs[] = $val;
		}

		return array_keys($vars);
	}

    public function getColumnForParentTable($parentTable) 
    {
        $parents = $this->getParentList();

        foreach ($parents as $_fk => $parentData) {

            if ($parentData['table_name'] == $parentTable) {

                return $this->columnNameToVar(
                    		$this->getMapper()->getDbTable()->getReferenceMap($_fk)
                       );
                break;
            }
        }

        return false;
   }

   /**
    * Iterator stuff
    *
    * @return string
    */ 
    public function rewind() 
    {
        if (is_null($this->_columnsListKeys)) {

            $this->_columnsListKeys = array_keys($this->_columnsList);    
        }

        $this->_position = 0;
    }

    public function current() 
    {
        $key = $this->_columnsListKeys[$this->_position];
        return $this->_columnsList[$key];
    }

    public function key() 
    {
        return $this->_position;
    }

    public function next() 
    {
        ++$this->_position;
    }

    public function valid() 
    {
        if (isset($this->_columnsListKeys[$this->_position])) {

            $key = $this->_columnsListKeys[$this->_position];
            return isset($this->_columnsList[$key]);
        }

        return false;
    }
}
