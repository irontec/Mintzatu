<?php
require_once(dirname(__FILE__) . '/../../../libs/autoload.php');
define('CHK_KARMA', true);
class tablon_FLDsafetextTest extends PHPUnit_Framework_TestCase
{
    protected $_field;

    public function __construct()
    {
        $this->_field = new tablon_FLDsafetext(array(), 'testField');
        $this->_field->setSearchValue('frógÁtxueÑ');
    }

    public function testGetsearchopReturnsArray()
    {
        $retValue = $this->_field->getSearchOp();
        $this->assertTrue(is_array($retValue));
    }

    public function testGetsearchopReturnsLikeOp()
    {
        $retValue = $this->_field->getSearchOp();
        $this->assertEquals('like', $retValue['op']);
    }

    public function testGetsearchopReturnsLowerizedAndQuotizedStringWithPercentsSurroundingVals()
    {
        $retValue = $this->_field->getSearchOp();
        $this->assertEquals("'%frógátxueñ%'", $retValue['vals'][0]);
    }

    public function testGetsearchopReturnsUpperizedAndQuotizedStringWithPercentsSurroundingVals()
    {
        $retValue = $this->_field->getSearchOp();
        $this->assertEquals("'%FRÓGÁTXUEÑ%'", $retValue['vals'][1]);
    }

    public function testGetsearchopReturnsQuotizedDefaultStringWithPercentsSurroundingVals()
    {
        $retValue = $this->_field->getSearchOp();
        $this->assertEquals("'%frógÁtxueÑ%'", $retValue['vals'][2]);
    }
}