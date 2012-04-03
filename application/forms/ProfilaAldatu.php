<?php

class Application_Form_ProfilaAldatu extends Zend_Form
{
    protected $_urlIrudia;
    
    public function __construct($urlIrudia)
    {
        $this->_urlIrudia = $urlIrudia;
        parent::__construct();
    }
    
    public function init()
    {
        $izena = $this->createElement('text','izena');
        $izena->setLabel('Izena: *')
                    ->setRequired(true);
                    
        $abizenak = $this->createElement('text','abizenak');
        $abizenak->setLabel('Abizenak:')
                    ->setRequired(false);
                    
        $jaiotzeData = $this->createElement('text','jaiotze_data');
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

        if ($this->_urlIrudia) {
            $options = array(
                'label'      => 'Irudia',
                'required'   => false,
                'attribs'    => array(),
                'filters'    => array(),
                'publicPath' => $this->_urlIrudia,
                'validators' => array(
                     array('Count', false, 1),
                     array('Size', false, 5242880), 
                     array('IsImage', false, 'png,jpeg,jpg,gif'),
                ),
            ); 
            $irudia = new Mintzatu_Form_Element_ImageFile('irudia', $options);
        } else {
            $irudia = $this->createElement('file','irudia');
            $irudia->setLabel('Irudia: ')
                ->setRequired(false);
        }
                
        $facebook = $this->createElement('text','facebook');
        $facebook->setLabel('Facebook: ')
                ->setRequired(false);
                
        $twitter = $this->createElement('text','twitter');
        $twitter->setLabel('Twitter: ')
                ->setRequired(false);
                
        $bidali = $this->createElement('submit','bidali');
        $bidali->setLabel('Bidali')
                ->setIgnore(true)
                ->setDecorators(array(
                    'ViewHelper',
                    array(array('data' => 'HtmlTag'), array('tag' => '<span>', 'class' => 'bidali')),
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
            $izena,
            $abizenak,
            $erabiltzailea,
            $irudia,
            $eposta,
            $herria,
            $jaiotzeData,
            $deskribapena,
            $facebook,
            $twitter,
            $bidali,
            $ezeztatu
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

