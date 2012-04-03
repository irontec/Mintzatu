<?php
/**
 * Interface que se tiene que implementar para poder implementar Opciones en popup
 */
interface tablon_InterfacePopupOption {

    /**
     * Constructor
     * @param $id id de la fila de tablon en la que se ha clickado
     */
	public function __construct($id);

	/**
	 * Método encargado de devolver los datos necesarios para mostrar en el popup. También se le llama después de haber recibido los datos y haberse ejecutado feedData...
	 * @return json con los siguientes datos (los datos con un asterisco son obligatorios)
	 *     - html*: HTML que se mostrará en el mensaje
     *     - exec: Javascript que se ejecutará en el cliente (a lo banzai)
	 *     - fields: Array asociativo.Por cada opción del array se muestra un campo para introducir datos. Estas opciones pueden ser de dos tipos:
	 *         - Boolean: Si el dato es de tipo booleano (da igual TRUE o FALSE) se muestra un campo de texto ¿?¿?
	 *         - Array asociativo: Si es un array se muestra una select con las opciones del array
     *     - postexec: Javascript que se ejecutará en el cliente (a lo banzai también), una vez se de a OK. Solo se ejecuta si fields está vacio
	 */
    public function resolveIt();

	/**
	 * Método que se llama al recibir datos de los fields especificados en el método resolveIt
	 * Si no se quieren recibir datos pero es necesario llamar a este método poner el campo fields del resolveIt a true
	 */
	public function feedData();

}
