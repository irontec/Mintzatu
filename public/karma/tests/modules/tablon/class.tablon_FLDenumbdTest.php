<?php
require_once(dirname(__FILE__) . '/../../../libs/autoload.php');
define('CHK_KARMA', true);
class tablon_FLDenumbdTest extends PHPUnit_Framework_TestCase
{
    protected $_field;
    protected $_searchValue = 5;

    public function __construct()
    {
        $this->_field = new tablon_FLDenumbd(array(), 'testField');
        $this->_field->setSearchValue($this->_searchValue);
    }

    public function testGetsearchopReturnsEqualsStatementWithQuotizedValue()
    {
        $retValue = $this->_field->getSearchOp();
        $this->assertEquals(" = '" . $this->_searchValue . "'", $retValue);
    }
}