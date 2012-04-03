<?php

class Application_Form_LekuIrudia extends Zend_Form
{
    protected $_lekuaUrl;
    
    public function __construct($url)
    {
        $this->_lekuaUrl = $url;    
        parent::__construct();   
    }
    
    public function init()
    {
        $irudia = $this->createElement('file','irudia');
        $irudia->setLabel('Irudia:*')
                ->setRequired(true);
                
        $iruzkina = $this->createElement('textarea', 'iruzkina');
        $iruzkina->setLabel('Iruzkina:');
                
        $bidali = $this->createElement('submit','bidali');
        $bidali->setLabel('Bidali')
                ->setIgnore(true);

        $this->addElements(array(
                        $irudia,
                        $iruzkina,
                        $bidali
        ));
        
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl')),
            'Fieldset',
            'Form'
        ));
        
        $this->setAttrib('enctype', 'multipart/form-data');
        
        $url = Zend_Layout::getMvcInstance()->getView()->url(array(
            'controller' => 'lekuak',
            'action' => 'irudi-berria',
            'lekua' => $this->_lekuaUrl
        ), '',true);
        
        $this->setAction($url);
    }


}

