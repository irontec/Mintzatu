<?php
/**
 * @author Alayn Gortazar <alayn+karma@irontec.com>
 * @copyright Irontec S.L. 2010
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

/**
 * Interfaz para las clases de Login de Karma que queramos usar a través de KarmaAuth
 * No sirve para el login de Karma (al menos por ahora)
 *
 * @author Alayn Gortazar <alayn+karma@irontec.com>
 *
 */
interface KarmaLogin_Interface
{
    /**
     * Constructor
     * @param $loginConfFile Ruta al archivo de configuración del login
     */
    public function __construct($loginConfFile = null);


    public function getTmpPassword();

    /**
     * Autenticar el usuario
     * @param string $user
     * @param string $password
     * @return bool
     */
    public function authenticate($user, $password, $tmppassword);

    /**
     * Devuelve el usuario autenticado
     * @return KarmaUser_Interface
     */
    public function getUser();

    /**
     * Devuelve todos los roles asociados a una aplicación
     * @param string $appId
     * @return KarmaRol_Interface[]
     */
    public static function getRoles($appId, $treeMode);

}