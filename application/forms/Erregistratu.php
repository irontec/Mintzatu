<?php

class Application_Form_Erregistratu extends Zend_Form
{

    public function init()
    {
        $izena = $this->createElement('text','izena');
        $izena->setLabel('Izena: *')
                    ->setRequired(true);
                    
        $abizenak = $this->createElement('text','abizenak');
        $abizenak->setLabel('Abizenak:')
                    ->setRequired(false);
                    
        $jaiotzeData = $this->createElement('text','jaiotzeData');
        $jaiotzeData->setLabel('Jaiotze Data:')
                    ->setRequired(false);
        $jaiotzeData->setOptions(array('class'=>'datePicker'));
        
        $herria = $this->createElement('text','herria');
        $herria->setLabel('Herria:')
                    ->setRequired(false);
                    
        $deskribapena = $this->createElement('textarea','deskribapena');
        $deskribapena->setLabel('Deskribatu zure burua:')
                    ->setRequired(false);
                    
        $eposta = $this->createElement('text','posta');
        $eposta->setLabel('Posta Elektronikoa: *')
                ->setRequired(true);
                
        $erabiltzailea = $this->createElement('text','erabiltzailea');
        $erabiltzailea->setLabel('Erabiltzaile Izena: *')
                ->setRequired(true);
                
        $pasahitza = $this->createElement('password','pasahitza');
        $pasahitza->setLabel('Pasahitza: *')
                ->setRequired(true);
                
        $pasahitzaKonfirmatu = $this->createElement('password','pasahitzakonfirmatu');
        $pasahitzaKonfirmatu->setLabel('Pasahitza errepikatu: *')
                ->setRequired(true);
                
        $irudia = $this->createElement('file','irudia');
        $irudia->setLabel('Irudia: ')
                ->setRequired(false);
                
        $facebook = $this->createElement('text','facebook');
        $facebook->setLabel('Facebook Erabiltzailea: ')
                ->setRequired(false);
                
        $twitter = $this->createElement('text','twitter');
        $twitter->setLabel('Twitter Erabiltzailea: ')
                ->setRequired(false);
                
        $bidali = $this->createElement('submit','bidali');
        $bidali->setLabel('Bidali')
                ->setIgnore(true);

        $this->addElements(array(
                        $izena,
                        $abizenak,
                        $erabiltzailea,
                        $irudia,
                        $eposta,
                        $pasahitza,
                        $pasahitzaKonfirmatu,
                        $herria,
                        $jaiotzeData,
                        $deskribapena,
                        $facebook,
                        $twitter,
                        $bidali
        ));
        
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl')),
            'Fieldset',
            'Form'
        ));
        
        $this->setAttrib('enctype', 'multipart/form-data');
    }


}

