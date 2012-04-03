<?php
require_once(dirname(__FILE__) . '/../../libs/autoload.php');
class modules_ngTest extends PHPUnit_Framework_TestCase
{
    protected $_modNg;

    public function setUp()
    {
        $this->_modNg = new modules_ng(dirname(__FILE__) . '/_files/modules.ini');
        $_GET = array();
        $_SESSION = array();
    }

    public function testSetLanguageSetsCorrectSessionLanguageWhenLangIsSpecifiedInGetRequest()
    {
        $_GET = array(
            'lang' => 'jp'
        );
        $this->_modNg->setLanguage();
        $this->assertEquals('jp', $_SESSION['lang']);

        $_GET = array(
            'lang' => 'es'
        );
        $this->_modNg->setLanguage();
        $this->assertEquals('es', $_SESSION['lang']);
    }

    public function testSetLanguageSetsCorrectDeflangAsLanguageWhenNoLanguageSpecified()
    {
        $this->_modNg->setLanguage();
        $this->assertEquals('jp', $_SESSION['lang']);
    }

    public function testJavascriptFilesAreInsertedInJsArray()
    {
        $this->_modNg->loadModule();
        $this->assertTrue(in_array('scripts/cosa.js',$this->_modNg->js));
    }

    public function testIndexIsDefaultModule()
    {
        $this->_modNg->loadModule();
        $this->assertEquals('index', $this->_modNg->modulo);
    }

}