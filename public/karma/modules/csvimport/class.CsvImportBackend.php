<?php
class CsvImportBackend
{


    const KEY_DONT_USE = 'dont';

    private $_filename;

    /**
     * __construct
     *
     * @access protected
     * @return void
     */
    function __construct($isUpload)
    {
        error_reporting( E_ALL | E_STRICT);
        ini_set('auto_detect_line_endings', true);
        session_name("karmaPrivate");
        session_start();
        session_cache_limiter("private");
        header("Expires: 0");
        header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false); // HTTP/1.1
        header("Pragma: no-cache");// HTTP/1.0
        /* fin de header anti cache */

        define("CHK_KARMA", 1); // Constante para comprobar que todos los ficheros son cargados desde index
        if (isset($_GET['DEBUG'])) define("DEBUG", 1);






        // Cargamos el "cargador" de classes
        require_once("../../../libs/autoload.php");
        //require_once('../../clases/class.con.php');
        @date_default_timezone_set($_SESSION['K_TIMEZONE']); //SYSTEM DEFAULTS



        if (isset($_SESSION["K_MYSQL_TIMEZONE"])) {
            new con("set time_zone = '".$_SESSION["K_MYSQL_TIMEZONE"]."';"); // MEJOR GMT
        }

        // Instanciamos objeto error para crear el trigger de errores a nuestro objeto.
        $oError = new iError();

        //Cargamos constantes de ficheros requeridos
        include_once("../../../../configuracion/defs.cfg");

        $this->_filename = null;
        $ret = array();


        if ($isUpload) {
            if ($this->saveFile()) {
                $ret['map'] = $this->replaceOpenCloseTags('<input type="hidden" name="filename" value="' . $this->_filename . '"/>');

                $ret['error'] = false;

            } else {
                $ret['error'] = true;
            }


        } else {
            if ($this->locateFile()) {
                $hasHeader = false;
                if (isset($_POST['has_header'])) {
                    $hasHeader = $_POST['has_header'];
                }

                $isPreview = isset($_POST['last_index']); // Estamos en el paso de crear la vista preliminar
                $isConfirmation = isset($_POST['changes_confirmed']); // Estamos en el paso final
                $ret['html'] = $this->getMapHtml($this->getFirstNRowsCSV(sys_get_temp_dir().'/'.$this->_filename,
                    $_POST['field_delimiter'],
                    $_POST['enclosure'],
                    2),
                $_POST['flds'],
                $_POST['aliases'],
                $hasHeader
            );
                if ($isPreview || $isConfirmation) {
                    $add = $this->importData($this->getRowsCSV(sys_get_temp_dir().'/'.$this->_filename, $_POST['field_delimiter'], $_POST['enclosure']), $_POST['flds'],
                        $_POST['aliases'],
                        $hasHeader,
                        !$isConfirmation
                    );
                    if ($isPreview && !$isConfirmation) {
                        $ret['preview'] = $add;
                    } elseif ($isConfirmation) {
                        $ret['confirmed'] = $add;
                    }
                }
                $ret['error'] = false;
            } else {
                $ret['error'] = true;
            }
        }
        /*
        ob_start();
        print_r($ret);
        $output = ob_get_clean();
        file_put_contents( '/tmp/lala.txt', $output );
        */
        echo json_encode($ret);

    }

    /**
     * getCsvMap
     *
     * @param mixed $fieldsAliases
     * @access private
     * @return void
     */
    private function getCsvMap($fieldsAliases)
    {
        /*
         * 0 (campo 0 del csv) => (field, default, alias)
         * 1 (campo 1 del csv) => (field, default, alias)..
         * 2 (campo 2 del csv) => null (no usar ese campo del csv)
         */
        // TODO Esta estructura de datos se podria evolucionar a una clase
        $map = array();
        $max = $_POST['last_index'];
        $i = 0;
        while ($i<=$max) {

            $map[$i] = array();
            if ($_POST['field_'.$i] != self::KEY_DONT_USE) { //TODO

                $map[$i]['field'] = $_POST['field_'.$i];
                $map[$i]['default'] = '\''.$_POST['default_'.$i].'\'';
                if ($map[$i]['default'] == '\'\'') {
                    $map[$i]['default'] = null;
                }
                $map[$i]['alias'] = $fieldsAliases[$map[$i]['field']];
            } else {
                $map[$i] = null;
            }
            $i++;
        }
        return $map;
    }









    /**
     * importData
     *
     * @param array $rows
     * @param mixed $flds
     * @param mixed $aliases
     * @param mixed $hasHeader
     * @param mixed $isPreview
     * @access private
     * @return void
     */
    private function importData(array $rows, $flds, $aliases, $hasHeader, $isPreview)
    {

        $tableName = $_POST['table_name'];
        $tableId = $_POST['id_fld'];
        $fields = explode(',',$flds);
        $aliases = explode(',',$aliases);
        $fieldsAliases = array_combine($fields, $aliases);
        $max = $_POST['last_index'];

        $map = $this->getCsvMap($fieldsAliases);
        $dbObj = con::getMysqliObj();
        $dbObj->autocommit(FALSE);

        $result = $dbObj->query('SELECT MAX(' . $tableId . ') AS `max` FROM ' . $tableName);
        $maxObj = $result->fetch_object();
        $prevMax = $maxObj->max;
        /*$nrows = array();
        foreach($rows as $cur) {
            $nrows[] = explode(',',$cur[0]);

        }*/
        $nrows = $rows; //TODO dependencia residual
        $affectedRows = 0;
        $workedIdList = array();
        $i = 0;
        foreach ($nrows as $curRow) { // TODO Esto es sobrecomplicado por no haber pensado bien las estructuras
            // Nueva insercion
            $j = 0;
            if ($i > 0 || !$hasHeader) {
                //$dbFieldsValues['flds']= '(';
                //$dbFieldsValues['vals'] = '(';
                $dbFieldsValues = array(
                    'flds' => '(',
                    'vals' => '(',
                    'alias' => '(',
                    'update' => '',
                );
                foreach ($curRow as $curCol) {

                    if ($map[$j] != null) { // Si ese campo de CSV se va a usar
                        if ($dbFieldsValues['flds'] != '(') {
                            $dbFieldsValues['flds'] .= ',';
                            $dbFieldsValues['vals'] .= ',';
                            $dbFieldsValues['alias'] .= ',';
                        }
                        if ($dbFieldsValues['update'] != '') {
                            $dbFieldsValues['update'] .= ',';
                        }
                        $dbFieldsValues['flds'] .= $map[$j]['field'];
                        $dbFieldsValues['update'] .= $map[$j]['field'] . ' = ' . 'VALUES(' . $map[$j]['field'] . ')';
                        $dbFieldsValues['alias'] .= $fieldsAliases[$map[$j]['field']];

                        if ($curCol == null || $curCol == '') { // Si el campo en este fila esta vacío
                            if ( $map[$j]['default'] != '' && $map[$j]['default'] != null ) {
                                $dbFieldsValues['vals'] .= str_replace(',', '#COMA#', utf8_encode($map[$j]['default']));
                            } else {
                                $dbFieldsValues['vals'] .= '""';
                            }
                        } else { // Usar el campo CSV
                            $curCol = str_replace(',', '#COMA#', $curCol);
                            $dbFieldsValues['vals'] .= '"'.str_replace('"', '', $curCol).'"';
                        }


                    }
                    $j++;
                }
                while ($j <= $max) {
                    if ($map[$j] != null) {
                        if ($dbFieldsValues['flds']!= '(') {
                            $dbFieldsValues['flds'].= ',';
                            $dbFieldsValues['vals'] .= ',';
                            $dbFieldsValues['alias'] .= ',';
                        }
                        if ($dbFieldsValues['update'] != '') {
                            $dbFieldsValues['update'] .= ',';
                        }
                        $dbFieldsValues['vals'] .= $map[$j]['default'];
                        $dbFieldsValues['flds'].= $map[$j]['field'];
                        $dbFieldsValues['update'] .= $map[$j]['field'] . ' = ' . 'VALUES(' . $map[$j]['field'] . ')';
                        $dbFieldsValues['alias'] .= $fieldsAliases[$map[$j]['field']];
                    }
                    $j++;
                }
                $dbFieldsValues['flds'].= ')';
                $dbFieldsValues['alias'].= ')';
                $dbFieldsValues['vals'] .= ')';
                $dbFieldsValues['update'] .= '';

                if ($dbFieldsValues['flds']== '()') {
                    $dbFieldsValues['flds']= null;
                    $dbFieldsValues['alias'] = null;
                    $dbFieldsValues['vals'] = null; //TODO no mola, esto se hace para evitar inserts basurescos
                    $dbFieldsValues['update'] = null;
                }
                $selectFlds = substr($dbFieldsValues['flds'],1,strlen($dbFieldsValues['flds'])-2);
                $selectVals = substr($dbFieldsValues['vals'],1,strlen($dbFieldsValues['vals'])-2);
                $selectFlds = explode(',', $selectFlds);
                $selectVals = explode(',', $selectVals);
                $select = array_combine($selectFlds, $selectVals);
                $sqlWhere = '';
                foreach($select as $key=>$value) {
                    if ($sqlWhere != '') {
                        $sqlWhere .= ' AND ';
                    }
                    $sqlWhere .= ' ' . $key . ' = ' . utf8_encode(str_replace('#COMA#',',',$value)) ;
                }
                $dbFieldsValues['vals'] = str_replace('#COMA#',',',$dbFieldsValues['vals']);


                $result = $dbObj->query('SELECT * FROM `'. $tableName . '` WHERE ' . $sqlWhere);
                if ($dbObj->error == '' && $result->num_rows == 0 && $dbObj->query('INSERT INTO `'.$tableName.'` ' . $dbFieldsValues['flds']. ' VALUES ' . utf8_encode($dbFieldsValues['vals']) . ' ON DUPLICATE KEY UPDATE ' . utf8_encode($dbFieldsValues['update']) . ','. $tableId.'=LAST_INSERT_ID(' . $tableId . ')') === TRUE) {
                    //$result = $dbObj->query('SELECT last_insert_id('.$tableId.') AS `lastId` FROM `'.$tableName.'`');
                    if ($dbObj->error == '') {
                        $result = $dbObj->query('SELECT LAST_INSERT_ID() AS `lastId`');
                        $currentId = $result->fetch_object();
                        $currentId = $currentId->lastId;

                        //if ($firstInsertId == -1) {
                        //    $firstInsertId = $currentId;
                        //}
                        if ($currentId <= $prevMax && !in_array($currentId, $workedIdList)) {
                            $workedIdList[] = $currentId;
                        }
                    }
                } else {
                        //echo $dbObj->error ."\n";
                }

            }
            $i++;

            $affectedRows += $dbObj->affected_rows;
        }
        $html = '';
        if ($isPreview) {
            $dbHeader = str_replace(array("(",")"), '', $dbFieldsValues['flds']);
            $dbHeaderAlias = str_replace(array("(",")"), '', $dbFieldsValues['alias']);

            $idModifiedSql = $tableId . '=' . implode(' OR ' . $tableId . '=',$workedIdList);
            $somethingToConfirm = false;
            if ($result =
                $dbObj->query('SELECT '. $dbHeader .' FROM `'.$tableName.'` WHERE '. $idModifiedSql)) {
                    if ($result->num_rows > 0) {
                        $html .= $this->getPreviewHtml($result, explode(',', $dbHeaderAlias), '<h3>Se van a realizar las siguientes actualizaciones :</h3>');
                        $somethingToConfirm = true;
                    } else {
                        $html .= '<h3>No hay actualizaciones a realizar.</h3>';
                    }
                }
            if ($result = $dbObj->query('SELECT '. $dbHeader .' FROM `'.$tableName.'` WHERE '.$tableId.'>"'.$prevMax .'"')) {
                if ($result->num_rows > 0) {
                    $html .= $this->getPreviewHtml($result, explode(',', $dbHeaderAlias), '<h3>Se van a insertar los siguientes registros :</h3>');
                    $somethingToConfirm = true;
                } else {
                    $html .= '<h3>No hay registros nuevos.</h3>';
                }
            }
            $dbObj->rollback(); // Si esto ha sido una vista preliminar hacemos rollback
            $dbObj->query('ALTER TABLE `' . $tableName . '` AUTO_INCREMENT = 1'); // Restablecemos los indices
            $html = '<h2>Paso 4 - Confirma los cambios</h2>' . $html;
            $html = $html . '<br/>';
            $html .= $this->replaceOpenCloseTags('<input type="hidden" name="changes_confirmed" value="true"/>');
            if ($somethingToConfirm) {
                $html .= $this->replaceOpenCloseTags('<input id="button_submit_final" type="submit" class="button_submit" value="Confirmar cambios"/>');
            }
        } else {
            $dbObj->commit(); // Si esto ha sido la definitica, hacemos commit
            $html.= '<h2>CSV importado</h2>';
            $html.= '<p>Se han insertado los datos con éxito</p>';
            $html = $this->replaceOpenCloseTags($html);
        }
        $dbObj->close();

        return array('affected'=>$affectedRows,
            'new_table'=>$html
        );

    }

    /**
     * getPreviewHtml
     *
     * @param mixed $rs
     * @param mixed $header
     * @access private
     * @return void
     */
    private function getPreviewHtml($rs, $header, $desc)
    {
        $out = $desc;
        $out .= '<table class="tablon">';

        $out .= '<tr class="tablon_tr_header">';
        foreach ($header as $curHeadEle) {
            $out .= '<td>'.$curHeadEle.'</td>';
        }
        $out .= '</tr>';
        $i = 0;
        while ($linea = $rs->fetch_assoc()) {
            $extra = "impar";
            if ($i % 2 == 0) $extra = "par";
            $out .= '<tr class="'.$extra.'">';
            foreach ($linea as $colVal) {
                $out .= '<td>'.$colVal.'</td>';
            }
            $out .= "</tr>";
            $i++;
            if ($i > 100) {
                $out .= "S&oacute;lo se han mostrado 100 registros"; /* Para evitar json enorme */
                break;
            }
        }
        $out .= "</table>";
        return $this->replaceOpenCloseTags($out);
    }

    /**
     * getRowsCSV
     *
     * @param mixed $file
     * @param mixed $delimiter
     * @param mixed $enclosure
     * @access private
     * @return void
     */
    private function getRowsCSV($file,$delimiter, $enclosure)
    {
        $ret = array();
        $file = new SplFileObject($file);
        $file->setCsvControl($delimiter, $enclosure);
        while ($file->valid()) {
            $data = $file->fgetcsv();
            if ($data[0] != null) {
                $ret[] = $data;
            }
        }
        return $ret;
    }

    /**
     * getFirstNRowsCSV
     *
     * @param mixed $file
     * @param mixed $delimiter
     * @param mixed $enclosure
     * @param mixed $numRows
     * @access private
     * @return void
     */
    private function getFirstNRowsCSV($file, $delimiter, $enclosure , $numRows)
    {
        $ret = array();
        //stream_get_line($file, 4096, "\r");
        $file = new SplFileObject($file);
        $file->setCsvControl($delimiter, $enclosure);

        $i = 0;
        while ($file->valid() && $i++ < $numRows) {
            $data = $file->fgetcsv();
            if ($data[0] != null) {
                $ret[] = $data;
            }
        }
        return $ret;
    }




    /**
     * locateFile
     *
     * @access private
     * @return void
     */
    private function locateFile()
    {
        $uploadfile =  $_POST['filename'];
        $this->_filename = $uploadfile;
        return file_exists(sys_get_temp_dir() . '/'.$this->_filename);


    }

    private function saveFile()
    {

        $uploaddir = sys_get_temp_dir();
        $uploadfile = $uploaddir .'/' .basename($_FILES['file_csv']['name']);
        $this->_filename = basename($_FILES['file_csv']['name']);
        return (move_uploaded_file($_FILES['file_csv']['tmp_name'], $uploadfile));

    }

    /* Transpuesta de la matriz */
    /**
     * array_transpose
     *
     * @param mixed $array
     * @param mixed $selectKey
     * @access private
     * @return void
     */
    private function array_transpose($array, $selectKey = false)
    {
        if (!is_array($array)) return false;
        $return = array();
        foreach ($array as $key => $value) {
            if (!is_array($value)) return $array;
            if ($selectKey) {
                if (isset($value[$selectKey])) $return[] = $value[$selectKey];
            } else {
                foreach ($value as $key2 => $value2) {
                    $return[$key2][$key] = $value2;
                }
            }
        }
        return $return;
    }



    /**
     * getMapHtml
     *
     * @param array $rows
     * @param mixed $flds
     * @param mixed $aliases
     * @param mixed $hasHeader
     * @access private
     * @return void
     */
    private function getMapHtml(array $rows, $flds, $aliases, $hasHeader)
    {

        $fields = explode(',', $flds);
        $aliases = explode(',', $aliases);
        $html = '<h2>Paso 3: Asigna los campos</h2><br/>';
        $html .= '<table class="tablon" id="import_table" border="0" cellpadding="0" width="100%">';
        $html .= '<tbody>';
        $row1Name = 'Fila 1';
        $row2Name = 'Fila 2';
        if ($hasHeader) {
            $row1Name = 'Cabecera';
            $row2Name = 'Fila 1';
        }
        $html .= '<tr class="tablon_tr_header">
            <td style="text-align: left;" scope="row">

            <b>Campo de la base de datos</b>&nbsp;
        </td>
            <td style="text-align: left;" scope="row">
            <b>Valor por defecto</b>&nbsp;
        </td>
            <td style="text-align: left;" scope="row">

            <b>'.$row1Name.'</b>&nbsp;
        </td>
            <td style="text-align: left;"><b>'.$row2Name.'</b></td>
            </tr>';
        $i = 0;
        /*
        $nrows = array();
        foreach($rows as $cur) {
            $nrows[] = explode(',',$cur[0]);

        }

        $nrows = $this->array_transpose($nrows);
         */

        $rows = $this->array_transpose($rows);
        $nrows = $rows; //TODO dependencia residual
        $fields = array_combine($fields, $aliases);
        foreach ($nrows as $curRow) {
            $extra = 'impar';
            if ($i % 2 == 0) {
                $extra = 'par';
            }
            $html .= '<tr id="row_'.$i.'" class="'.$extra.'">';
            $j = 0;

            $html .= '<td id="row_'.$i.'_col_'.$j.'">';
            $html .= '<select id="field_'.$i.'" name="field_'.$i.'">';
            $html .= '<option value="'.self::KEY_DONT_USE.'">No usar este campo</option>';
            foreach ($fields as $key=>$value) {
                $html .= '<option value="'.$key.'">'.$value.'</option>';
            }
            $j++;
            $html .= '</select>';
            $html .= '</td>';

            $html .= '<td id="row_'.$i.'_col_'.$j.'">';
            $html .= '<input name="default_'.$i.'" type="text" />';
            $html .= '</td>';
            $j++;
            foreach ($curRow as $curCol) {
                $html .= '<td id="row_'.$i.'_col_'.$j.'">';
                $html .= $curCol;
                $html .= '</td>';
                $j++;
            }
            $i++;

        }
        $html .= '</table><br/>';
        $html .= '<p>* Los campos '.$_POST['req_flds'].' son obligatorios</p>';
        $html .= '<input type="hidden" id="last_index" name="last_index" value="' . ($i-1) . '"/>';
        $html .= '<button type="button" id="add_field_button">A&ntilde;adir campo</button>';
        $html .= '<input id="button_submit_map" class="button_submit" type="submit" value="Siguiente (ver preliminar) "/>';
        $ret = $this->replaceOpenCloseTags($html);
        return utf8_encode($ret);
    }


    /**
     * replaceOpenCloseTags
     *
     * @param mixed $html
     * @access private
     * @return void
     */
    private function replaceOpenCloseTags($html)
    {
        return str_replace(array('<','>'), array('%OPENHTMLTAG%','%CLOSEHTMLTAG%'), $html);
    }


}




//EOF
