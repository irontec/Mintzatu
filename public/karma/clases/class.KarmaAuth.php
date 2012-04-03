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
 * Clase para abstraer la autenticación de Karma y facilitar su uso desde el exterior
 * @author Alayn Gortazar <alayn+karma@irontec.com>
 *
 */
class KarmaAuth
{
    protected static $_user = null;

    /**
     * Devuelve si se está autenticado o no
     * @return bool
     */
    public static function isAuthenticated()
    {
        if (isset($_SESSION['karmaAuth']) && $_SESSION['karmaAuth'] === true) {
            return true;
        }
        return false;
    }

    /**
     * Devuelve la clase con el usuario autenticado
     * @return KarmaUser_Interface
     */
    public static function getUser()
    {
        if (self::isAuthenticated()) {
            if (is_null(self::$_user)) {
                self::$_user = unserialize($_SESSION['karmaAuthUser']);
            }
            return self::$_user;
        }
        return null;
    }

    /**
     * Autenticar el usuario
     * @param string $user
     * @param string $password
     * @param string $tmppassword
     * @return bool
     */
    public static function authenticate($user, $password, $tmppassword = false)
    {
        $_SESSION['karmaAuth'] = false;
        $login = self::getAuthClass();

        if ($login->authenticate($user, $password, $tmppassword)) {
            self::setAuthUser($login->getUser());
            $_SESSION['__USER'] = $user;
            $_SESSION['__PW'] = $login->getTmpPassword();
        }
        return $_SESSION['karmaAuth'];
    }

    /**
     * Establece el usuario de sesión y lo marca la sesión como autenticada
     * @param KarmaUser_Interface $user
     */
    public static function setAuthUser(KarmaUser_Interface $user) {
        self::$_user = $user;
        $_SESSION['karmaAuth'] = true;
        $_SESSION['karmaAuthUser'] = serialize(self::$_user);
    }

    /**
     * Desloguear al usuario
     * @return bool Siempre devuelve TRUE
     */
    public static function logout()
    {
        if (self::isAuthenticated()) {
            unset($_SESSION['karmaAuth']);
            unset($_SESSION['karmaAuthUser']);
        }
        return true;
    }

    /**
     * Devuelve la clase encarga de realizar el login.
     * @return KarmaLogin_Interface
     */
    protected static function getAuthClass()
    {
        $cfg = parse_ini_file(i::getConfigPath() . DIRECTORY_SEPARATOR . 'karma.cfg', true);
        $authClass = $cfg['main']['auth'];
        $authConfFile = i::getConfigPath() . DIRECTORY_SEPARATOR . $cfg['main']['authfile'];
        return new $authClass($authConfFile);
    }

    /**
     * Devuelve los roles disponibles
     * @param string $aplication Nombre de la aplicación de la que se quieren obtener los roles
     * @return KarmaRol_Interface[]
     */
    public static function getRoles($application, $treeMode)
    {
        $auth = self::getAuthClass();
        $roles = $auth->getRoles($application, $treeMode);
        return $roles;
    }
}