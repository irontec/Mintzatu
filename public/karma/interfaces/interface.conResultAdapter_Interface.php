<?php
/**
 * Interfaz que necesitan implementar los adaptadores de resultados de con
 *
 * @author Alayn Gortazar <alayn@irontec.com>
 * @version 2.1
 * @package karma
 */


interface conResultAdapter_Interface
{
    /**
     * Constructor recibe el resultado de una consulta realizada con el adaptador correspondiente
     * @param $result
     */
    public function __construct($result);

    /**
     * Limpia el objeto de resultados
     */
    public function freeResult();

    /**
     * Devuelve la siguiente fila en un array
     * @param string $resultType:
     *      valores permitidos:
     *          'ASSOC'
     *          'NUM'
     *          'BOTH'
     * @return array
     */
    public function fetchArray($resultType = 'ASSOC');

    /**
     * Indica si el resultado está vacio o no
     * @return bool
     */
    public function isEmpty();
}