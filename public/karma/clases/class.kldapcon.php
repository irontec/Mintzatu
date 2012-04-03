<?php
/**
 * Conector que trata de generar una abstracción para el acceso LDAP
 * @author alayn
 *
 */
class kldapcon
{
    static protected $_links = array();

    /**
     * Obtener la conexión ldap
     * @param string $link nombre de la conexión. Por defecto: default
     * @return LdapCon
     */
    public static function getLdapCon($link = 'default')
    {
        if (!isset(self::$_links[$link])) {
            $conf = self::_getConf($link);
            self::$_links[$link] = new LdapCon($conf);
        }
        return self::$_links[$link];
    }

    /**
     * Obtener la configuración de la conexión pedida
     * @param string $link Nombre de la conexión
     */
    protected static function _getConf($link)
    {
        $filePath = i::base_path() . 'db/ldap_access.cfg';
        if (!file_exists($filePath)) {
            throw new Exception('LdapFileNotFound');
        }

        $aConf = parse_ini_file($filePath, true);
        if (!isset($aConf[$link])) {
            throw new Exception('NoLdapLinkConfFound');
        }

        return $aConf[$link];
    }
}