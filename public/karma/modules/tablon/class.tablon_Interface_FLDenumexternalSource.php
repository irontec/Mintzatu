<?php
/**
 * Interface que se tiene que implementar para poder utilizar la clase con enumexternal
 */
interface tablon_Interface_FLDenumexternalSource {
    /*
     * Devuelve un array con el par $key=>$value necesarios para los ENUMEXTERNAL
     */
    /**
     * @param string $dataset El tipo de dato que queremos obtener. Se utiliza para poder coger más de un tipo de dato desde una misma clase.
     * @return array asociativo con el par $key=>$value necesarios para los ENUMEXTERNAL
     */
    public function getKarmaEnumArray($dataset = null);
}