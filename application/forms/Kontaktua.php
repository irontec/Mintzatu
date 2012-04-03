<?php

class Application_Form_Kontaktua extends Zend_Form
{

    public function init()
    {
        
        $izena = $this->createElement('text','izena');
        $izena->setLabel('Izena Abizenak: *')
                    ->setRequired(false);
                    
        $deskribapena = $this->createElement('textarea','mezua');
        $deskribapena->setLabel('Mezua: *')
                    ->setRequired(false);
                    
        $eposta = $this->createElement('text','posta');
        $eposta->setLabel('Posta Elektronikoa: *')
                ->setRequired(false);

        $captcha = $this->createElement('captcha', 'captcha',
        array('required' => true,
        'captcha' => array('captcha' => 'Image',
        'font' => APPLICATION_PATH.'/../public/css/arial.ttf',
        'fontSize' => '24',
        'wordLen' => 6,
        'height' => '50',
        'width' => '150',
        'imgDir' => APPLICATION_PATH.'/../public/captcha',
        'imgUrl' => Zend_Controller_Front::getInstance()->getBaseUrl().
        '/captcha',
        'dotNoiseLevel' => 40,
        'messages' => array(
            'badCaptcha' => 'Idatzi irudian agertzen den berdina'
          ),
        'lineNoiseLevel' => 2)));

        $captcha->setLabel('Idatzi hurrengo hizki eta zenbakiak:');
                
        
        $bidali = $this->createElement('submit','bidali');
        $bidali->setLabel('Bidali')
                ->setIgnore(true);

        $this->addElements(array(
                        $izena,
                        $eposta,
                        $deskribapena,
                        $captcha,
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

