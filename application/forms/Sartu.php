<?php

class Application_Form_Sartu extends Zend_Form
{

    public function init()
    {
        $erabil = $this->createElement('text', 'erabiltzailea');
        $erabil->setLabel('Erabiltzailea')
            ->addValidator('NotEmpty', true, array('messages' => 'Mesedez sartu ezazu Erabiltzailea.'));
        
        $pasahitza = $this->createElement('password', 'pasahitza');
        $pasahitza->addValidator('StringLength', false, array(6, 'messages' => 'Pasahitzak 6 karaktere baino gehiago eduki behar ditu.'))
            ->setLabel('Pasahitza')    
            ->addValidator('NotEmpty', true, array('messages' => 'Mesedez sartu ezazu Pasahitza.'));
        
        $gogoratu = $this->createElement('checkbox', 'gogoratu');
        $gogoratu->setLabel('Gogoratu')
            ->setRequired(false);
        
        $bidali = $this->createElement('submit','bidali');
        $bidali->setLabel('Sartu')
                ->setIgnore(true);
                
        $this->addElements(array(
            $erabil,
            $pasahitza,
            $gogoratu,
            $bidali
        ));
        
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl')),
            'Fieldset',
            'Form'
        ));
    }
}

