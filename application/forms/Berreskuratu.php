<?php

class Application_Form_Berreskuratu extends Zend_Form
{

    public function init()
    {
        $pasahitza = $this->createElement('password','pasahitza');
        $pasahitza->setLabel('Pasahitza: *')
            ->setRequired(true);
                
        $pasahitzaKonfirmatu = $this->createElement('password','pasahitzakonfirmatu');
        $pasahitzaKonfirmatu->setLabel('Pasahitza errepikatu: *')
            ->setRequired(true);
            
        $bidali = $this->createElement('submit','bidali');
        $bidali->setLabel('Bidali')
                ->setIgnore(true);

        $this->addElements(array(
            $pasahitza,
            $pasahitzaKonfirmatu,
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

