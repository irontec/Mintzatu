<?php

require_once(dirname(__FILE__) . '/../../libs/autoload.php');
class conPdoMysqlAdapterTest extends PHPUnit_Framework_TestCase
{
    protected $_db;

    public function __construct()
    {
        $this->_db = new conPdoMysqlAdapter('localhost', 'karmaTestUser', 'karmaTestPass', 'karmaTestDb', 3306);
    }

    public function testNoExistingColumnErrorReturns1054()
    {
        $result = $this->_db->query('SELECT noExistCol FROM test1');
        $this->assertEquals(1054, $this->_db->getErrorNo());
    }

    public function testNoExistingColumnErrorStringMatchesColumnNotFoundMessage()
    {
        $result = $this->_db->query('SELECT noExistCol FROM test1');
        $this->assertRegExp("/column '(.*)' in/", $this->_db->getError());
    }

}