<?php
/*
 *	Genera .plt automáticamente pasando como parámetro t el nombre de la tabla
 */
require(dirname(__FILE__)."/../libs/autoload.php");



function isKarmaStandardDate($field)
{
    return ($field == 'created_at') || ($field == 'modified_at');
}

function isEnumbd($field, $type)
{
    return (stristr(strtolower($field), '_id_') !== FALSE) && (stristr(strtolower($type), 'int') !== FALSE);
}



function getAlias($key)
{
    $ret = '';
    switch ($key) {
    case 'name':
        $ret = 'Nombre';
        break;
    case 'surname':
        $ret = 'Apellido';
        break;
    case 'surname1':
        $ret = 'Primer apellido';
        break;
    case 'surname2':
        $ret = 'Segundo apellido';
        break;
    case 'address':
        $ret = 'Dirección';
        break;
    case 'mobile_phone':
        $ret = 'Teléfono móvil';
        break;
    case 'created_at':
        $ret = 'Fecha creación';
        break;
    case 'modified_at':
        $ret = 'Fecha modificación';
        break;
    case 'start_date':
        $ret = 'Fecha comienzo';
        break;
    case 'end_date':
        $ret = 'Fecha final';
        break;
    case 'fixed_price':
        $ret = 'Precio fijado';
        break;
    case 'country':
        $ret = 'País';
        break;
    case 'descr':
        $ret = 'Descripción';
        break;
    case 'deleted':
        $ret = 'Borrado';
        break;
    case 'status':
        $ret = 'Estatus';
        break;
    case 'type':
        $ret = 'Tipo';
        break;
    case 'number':
        $ret = 'Número';
        break;
    case 'price':
        $ret = 'Precio';
        break;
    case 'type':
        $ret = 'Tipo';
        break;
    case 'phone1':
        $ret = 'Teléfono';
        break;
    default:
        $ret = $key;
    }

    return $ret;


}

chdir(dirname(__FILE__));
$path = "../../configuracion/tablon/";

$help="
    -t nombre de la tabla 'fotos'
    [-f fichero destino ó nombre_de_tabla.plt si no está presente]
    [-g genero a|o 'a']
    [-e entidad 'foto']
    [-d campo_por_defecto]
    [-b campo borrado]
    [-n evitar quotes SQL]
    [-x detectar ENUMBDs, deben ser en plan TABLA_DESTINO_id_tabla_destino] (como hace el mysql-workbench)

    Ejemplo:  karma_plt_gen.php -t kws_zone -x -n
    \n";

$args = "t:f::g::e::n::x";
$opts = getopt($args);
if ($opts == false) {
    echo $help;
    exit(1);
}

if ((!isset($opts['f'])) || ( !preg_match("/\-plt$/", $opts['f']))) {
    $file = $path.$opts['t'].".plt";
} else {
    $file = $path.$opts['f'];
}

if (!$fp = fopen($file, "w")) {


    exit(3);
}


$c = new con("show columns from ".$opts['t']);
if ($c->error()) {
    echo $c->getError();
    echo "\n";
    exit(2);
}
$data = "";
$headData = "";
$headData .= "[::main]\n";
$headData .= "tab=\"".$opts['t']."\"\n";

if (isset($opts['g'])) {
    $headData .= "genero=\"".$opts['g']."\"\n";
} else {
    $headData .= "genero=\"a\"\n";
}

if (isset($opts['e'])) {
    $headData .= "entity=\"".$opts['e']."\"\n";
} else {
    $headData .= "entity=\"CUSTOM\"\n";
}

if (isset($opts['d'])) {
    $headData .= "defaultFLD=\"".$opts['d']."\"\n";
}
if (isset($opts['b'])) {
    $headData .= "deleted=\"".$opts['b']."\"\n";
}

$headData .= "id=\"%id%\"\n";
$headData .= "\n";


$id = '';
$name = '';
$delete = '';
while ($r = $c->getResult()) {
    if ($r['Key'] == "PRI") {
        //$headData = str_replace("%id%", $r['Field'], $data);
        $id = $r['Field'];
        continue;
    }

    if ($r['Field'] == 'name') {
        $name = 'name';
    }

    if ($r['Field'] == 'delete') {
        $delete = 'delete';
        continue; // El campo borrado no se mete
    }

    $quote = '`';
    if (isset($opts['n'])) {
        $quote = '';
    }

    $data .= "[". $quote .$r['Field']. $quote . "]\n";




    $type = 'SAFETEXT';
    $matches = array();
    if (preg_match_all("/'(.*?)'/", $r['Type'], $matches)) {
        $type = 'ENUM';
    }
    if ($r['Type'] == 'timestamp') {
        $type = 'DATETIME';
    }
    if (isKarmaStandardDate($r['Field'])) {
        $type = 'GHOST';
    }
    if (strtoupper($r['Field']) == 'COLOR') { /* Si se llama 'color' lo mismo quieres un campo color */
        $type = 'COLOR';
    }
    if (isset($opts['x']) && isEnumbd($r['Field'], $r['Type'])) {
        $type = 'ENUMBD';

    }

    switch($type) {
    case 'ENUMBD':
        list($tabName, $tabId) = explode('_id_', $r['Field']);
        $tabId = 'id_' . $tabId;
        $data .= "type=\"ENUMBD\"\n";
        $data .= "tab=\"" . $tabName . "\"\n";
        $data .= "id=\"" . $tabId . "\"\n";
        $data .= "fld=\"" . $tabId . "\" ; TODO CUSTOM \n";
        $data .= "fldorder=\"" . $tabId . " asc \" ; TODO CUSTOM \n";
        $data .= ";nullable=\"Elige\"\n";
        $data .= "condicion=\"deleted='0'\"\n";
        $data .= "morefld=\"" . $tabId . "\" ; TODO CUSTOM \n";

        break;
    case 'COLOR':
        $data .= "type=\"COLOR\"\n";
        break;

    case 'GHOST':
        $data .= "type=\"GHOST\"\n";
        $data .= "sql=\"" . $r['Field'] . "\"\n";
        break;

    case 'ENUM':
        $data .= "type=\"ENUM\"\n";
        $data .= "keys=\"" . implode('|', $matches[1]) . "\"\n";
        if ($matches[1][0] == "0" && count($matches[1]) == 2) { /* Parecen booleanos*/
            $data .= "values=\"desactivado|activado\"\n";
        } else {
            $data .= "values=\"" . implode('|', $matches[1]) . "\"\n";
        }
        $data .= "defaultKey=\"" . $matches[1][0] . "\"\n";
        break;

    case 'DATETIME':
        $data .= "type=\"DATETIME\"\n";
        break;

    default:
        $data .= "type=\"SAFETEXT\"\n";
        break;
    }
    $data .="alias=\"". getAlias($r['Field']) ."\"\n";
    $data .= "\n";

}

$headData = "";
$headData .= "[::main]\n";
$headData .= "tab=\"".$opts['t']."\"\n";

if (isset($opts['g'])) {
    $headData .= "genero=\"".$opts['g']."\"\n";
} else {
    $headData .= "genero=\"a\"\n";
}

if (isset($opts['e'])) {
    $headData .= "entity=\"".$opts['e']."\"\n";
} else {
    $headData .= "entity=\"CUSTOM\"\n";
}

if (isset($opts['d'])) {
    $headData .= "defaultFLD=\"".$opts['d']."\"\n";
} else {
    if ($name != '') {
        $headData .= "defaultFLD=\"".$name."\"\n";
    } else {
        $headData .= "defaultFLD=\"".$id."\"\n";
    }
}


if (isset($opts['b'])) {
    $headData .= "deleted=\"".$opts['b']."\"\n";
} elseif ($delete != '') {
    $headData .= "deleted=\"".$delete."\"\n";
}

$headData .= "id=\"$id\"\n";
$headData .= "\n";




fwrite($fp, $headData . $data);
fclose($fp);


