<?php
/**
 * @author Alayn Gortazar <alayn+karma@irontec.com>
 * @copyright Irontec S.L. 2011
 * @see The GNU Public License (GPL)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */

interface KarmaUser_Interface extends Serializable
{
    /**
     * Devuelve el nombre del usuario
     * @return string
     */
    public function getUserName();

    /**
     * Devuelve el nombre "real" del usuario
     * @return string
     */
    public function getFullName();

    /**
     * Devuelve el identificador único del usuario (clave)
     *  Id en MySQL
     *  uid en LDAP
     *  etc...
     * @return mixed
     */
    public function getId();

    /**
     * Devuelve un array con los roles asociados al usuario
     * @return array de KarmaRol_Interface
     */
    public function getRoles();

    /**
     * Comprueba si el usuario tiene un rol específico
     * @return bool
     */
    public function hasRol($rolName);

    /**
     * Devuelve true si es admin basándose en el parámetro adminID de karma.cfg
     * @return bool
     */
    public function isAdmin();

    /**
     * Devuelve el valor correspondiente al dato pedido
     * @param $data
     */
    public function getValue($data);
}