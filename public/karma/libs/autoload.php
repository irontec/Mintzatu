<?php
/**
 * Fichero para la carga automática de clases
 *
 *
 * @author Javier Infante <jabi@irontec.com>
 * @version 1.0
 * @package karma
 */

function __autoload($class)
{
    $curDir = dirname(__FILE__);

    switch ($class) {
        case 'con':
            if (defined("OLD_CON")) {
                $class = "con.old";
            }
            break;
        case 'FB':
            require $curDir . '/FirePHPCore/fb.php';
            return;
            break;
        case 'FirePHP':
            require $curDir . '/FirePHPCore/FirePHP.class.php';
            return;
            break;
    }

    $aClass = explode('_', $class);

    if (sizeof($aClass) > 1) {
        /**
         * Si es una interfaz miramos en el directorio de interfaces de Karma
         */
        if ($aClass[sizeof($aClass) - 1] == 'Interface') {
            $f = $curDir . '/../interfaces/interface.' . $class . '.php';
            if (file_exists($f)) {
                require($f);
                return;
            }
        }
        $classPrimaria = $aClass[0];
    } else {
        $classPrimaria = $class;
    }

    $f = $curDir . "/../clases/class." . $class.".php";
    if (file_exists($f)) {
        require($f);
        return;
    } else {
        if (defined("CHK_KARMA")) {



            $karmaPaths = array(
                $curDir . "/../modules/" . $classPrimaria . "/class." . $class.".php",
                $curDir . "/../../karma_clases/class." . $class . ".php",
                $curDir . "/../../karma_modules/" . $classPrimaria . "/class." . $class . ".php",
                $curDir . "/../../karma_modules/" . implode('/', $aClass) . '.php'
            );

            $pathCount = sizeof($karmaPaths);
            for($i = 0; $i < $pathCount && !file_exists($karmaPaths[$i]); $i++);
            if ($i < $pathCount) {
                require($karmaPaths[$i]);
                return;
            }
        }
        if (defined("CHK_PUBLIC")) {
            $f = $curDir . "/../../public_clases/class." . $class.".php";
            if (file_exists($f)) {
                require_once($f);
                return;
            } else {
                $f = $curDir . '/../../public_clases/' .  str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
                if (file_exists($f)) {
                    require_once($f);
                    return;
                }
            }
        }

        /*
         * Esto es por si estamos usando clases de Pear y tal,
         * por lo que no se puede comprobar si el fichero existe...
         * de ahí lo de la @
         * :p
         */
        $f = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
        @include_once($f);
    }
}
