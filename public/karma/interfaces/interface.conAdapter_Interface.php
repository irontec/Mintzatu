<?php
/**
 * Interfaz que necesitan implementar los adaptadores de con
 *
 * @author Alayn Gortazar <alayn@irontec.com>
 * @version 2.1
 * @package karma
 */

interface conAdapter_Interface
{
    /**
     * Constructor
     * @param $host
     * @param $user
     * @param $pass
     * @param $db
     * @param $port
     */
    public function __construct($host, $user, $pass, $db, $port);

    /**
     * Devuelve la conexión a la BBDD
     */
    public function getDbConnection();

    /**
     * Realiza la query y devuelve el resulset o false en caso de estar vacio
     * @param String $sql
     * @return mixed conResultAdapterInterface
     */
    public function query($sql);

    /**
     * Devuelve el último código de error
     * @return int
     */
    public function getErrorNo();

    /**
     * Devuelve la descripción del error
     * @return string
     */
    public function getError();

    /**
     * Reconecta el servidor si es necesario
     */
    public function ping();

    /**
     * Devuelve el valor escapado y preparado para insertarse en la BBDD
     * @param $value
     * @return string
     */
    public function escapeString($value);

    /**
     * Devuelve el número de líneas afectadas por la última query
     * @return int
     */
    public function getAffectedRows();

    /**
     * Devuelve el id del último elemento insertado
     * @return int
     */
    public function getLastInsertId();

    /**
     * Cierra la conexión con la BBDD
     * @return bool
     */
    public function close();

}
