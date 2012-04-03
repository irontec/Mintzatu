<?php

class Application_Form_PasahitzaAldatu extends Zend_Form
{

    public function init()
    {
        $pasahitza = $this->createElement('password','pasahitza');
        $pasahitza->setLabel('Pasahitza: *')
            ->setRequired(true);
                
        $pasahitzBerria = $this->createElement('password','pasahitzBerria');
        $pasahitzBerria->setLabel('Pasahitz Berria: *')
            ->setRequired(true);
            
        $pasahitzaKonfirmatu = $this->createElement('password','pasahitzakonfirmatu');
        $pasahitzaKonfirmatu->setLabel('Pasahitz Berria errepikatu: *')
            ->setRequired(true);
            
        $bidali = $this->createElement('submit','bidali');
        $bidali->setLabel('Bidali')
                ->setIgnore(true)
                ->setDecorators(array(
                'ViewHelper',
                array(array('data' => 'HtmlTag'), array('tag' => '<span>', 'class' => 'botoia')),
            ));
                
        $ezeztatu = new Zend_Form_Element_Button('Ezeztatu');
        $ezeztatu->setRequired(false)
            ->setLabel('Ezeztatu')
            ->setValue('ezeztatu')
            ->setAttribs(array('type' => 'submit'))
            ->setDecorators(array(
                'ViewHelper',
                array(array('data' => 'HtmlTag'), array('tag' => '<span>', 'class' => 'botoia')),
            ));

        $this->addElements(array(
            $pasahitza,
            $pasahitzBerria,
            $pasahitzaKonfirmatu,
            $bidali,
            $ezeztatu
        ));
        
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl')),
            'Fieldset',
            'Form'
        ));
    }


}

