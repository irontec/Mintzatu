<?php
require_once(dirname(__FILE__) . '/../../libs/autoload.php');
class k_literalTest extends PHPUnit_Framework_TestCase
{
    protected $_l;
    protected $_insertableArray = array(
            'foo' => array('es'=>'Foo_es','en'=>'Foo_en','eu'=>'Foo_eu')
    );

    public function setUp()
    {
        $this->_l = new k_literal();
    }

    public function testAddLiteralsAddsNewLiteralToLiteralsArray()
    {
        $this->assertFalse($this->_l->exist('foo'));
        $this->_l->addLiterals($this->_insertableArray);
        $this->assertTrue($this->_l->exist('foo'));

    }

    public function testLiteralByDefaultReturnsEsResult()
    {
        $this->_l->addLiterals($this->_insertableArray);
        $this->assertEquals('Foo_es', $this->_l->literal('foo'));
    }

    public function testSetLangChangesLiteralLanguage()
    {
        $this->_l->addLiterals($this->_insertableArray);
        $this->_l->setLang('eu');
        $this->assertEquals('Foo_eu', $this->_l->literal('foo'));
        $this->_l->setLang('en');
        $this->assertEquals('Foo_en', $this->_l->literal('foo'));
    }
}
