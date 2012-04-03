<?php
Class Mintzatu_Validator_Kontaktua extends Mintzatu_Validator_ValidatorAbstract
{
    /**
     * Izena betetzen dela baieztatu.
     * @param unknown_type $datua
     */
    protected function setIzena($datua)
    {
        if (empty($datua)) {
            $this->_errorMessages['izena'][] = 'Izena beharrezkoa da.';
        }
        return $this;
    }
    
    protected function setMezua($datua)
    {
        if (empty($datua)) {
            $this->_errorMessages['mezua'][] = 'Mezua beharrezkoa da.';
        }
        return $this;
    }
    
    /**
     * Posta elektronikoa benetakoa dela eta aurretik erabili barik dagoela baieztatu.
     * @param unknown_type $datua
     */
    protected function setPosta($datua)
    {
        if (empty($datua)) {
            $this->_errorMessages['posta'][] = 'Eposta datua beharrezkoa da';
        } else {
            $validator = new Zend_Validate_EmailAddress();
            if (!$validator->isValid($datua)) {
                $this->_errorMessages['posta'][] = 'Sartu duzun posta helbidea ez da zuzena.';
            }
        }

        return $this;
    }
    
}
