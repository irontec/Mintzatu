<?php

class Application_Form_LekuBerria extends Zend_Form
{
    protected $_kategoriak;
    
    public function __construct()
    {
        $katMapper = new Mappers\Sql\Kategoriak;
        $kategoriak = $katMapper->fetchAllToArray();
        foreach ($kategoriak as $kat) {
            $this->_kategoriak[$kat['id_kategoria']] = $kat['izena'];
        }
        parent::__construct();
    }
    
    public function init()
    {
        $izena = $this->createElement('text','izena');
        $izena->setLabel('Izena: *')
            ->setRequired(true);
                    
        $deskribapena = $this->createElement('textarea','deskribapena');
        $deskribapena->setLabel('Deskribapena:')
            ->setRequired(false);
            
        $kategoriak = $this->createElement('select', 'kategoria');
        $kategoriak->setLabel('Kategoria:')
            ->addMultiOptions($this->_kategoriak);

        $postakodea = $this->createElement('text','postakodea');
        $postakodea->setLabel('Posta-Kodea:')
            ->setRequired(false);    
        
        $helbidea = $this->createElement('text','helbidea');
        $helbidea->setLabel('Helbidea:')
            ->setRequired(false);
                    
        $herria = $this->createElement('text','herria');
        $herria->setLabel('Herria:')
            ->setRequired(false);
        
        $probintzia = $this->createElement('text','probintzia');
        $probintzia->setLabel('Probintzia:')
            ->setRequired(false);
            
        $estatua = $this->createElement('text','estatua');
        $estatua->setLabel('Estatua:')
            ->setRequired(false);
        
        $latitudea = $this->createElement('hidden', 'latitudea');
        $longitudea = $this->createElement('hidden', 'longitudea');
        
        $bidali = $this->createElement('submit','sortu');
        $bidali->setLabel('Bidali')
            ->setIgnore(true);
            
        $this->addElements(array(
            $izena,
            $deskribapena,
            $kategoriak,    
            $helbidea,
            $postakodea,
            $herria,
            $probintzia,
            $estatua,
            $latitudea,
            $longitudea,
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

