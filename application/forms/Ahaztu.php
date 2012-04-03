<?php

class Application_Form_Ahaztu extends Zend_Form
{
    public function init()
    {
        $erabil = $this->createElement('text', 'posta');
        $erabil->setLabel('Posta Elektronikoa')
            ->setRequired(true);
        
        $bidali = $this->createElement('submit','bidali');
        $bidali->setLabel('Bidali')
                ->setIgnore(true);
                
        $this->addElements(array(
            $erabil,
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

