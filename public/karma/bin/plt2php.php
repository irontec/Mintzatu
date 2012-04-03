<?php
/**
 * @author: Ivan Mosquera Paulo <ivan@irontec.com
 */

if (!isset($argv[1])) {
    die("Dime el plt\n");
}

$plt = parse_ini_file($argv[1], true);
$suffix = "";
if (isset($argv[2])) {
    $suffix = $argv[2];
}
$className = basename($argv[1], ".plt") . $suffix;
$oPHP = "<?php";
$cPHP = "?>";


function to_camel_case($str, $capitalise_first_char = false) {
    if($capitalise_first_char) {
      $str[0] = strtoupper($str[0]);
    }
    $func = create_function('$c', 'return strtoupper($c[1]);');
    return preg_replace_callback('/_([a-z])/', $func, $str);
}

ob_start();
echo $oPHP
?>

class <?php echo $className ?> implements Iterator, Countable, ArrayAccess
    {
        private $_list;
        private $_pendingUpdateList; //array con los campos por actualizar
        private $_current;

        private $_meta = <?php var_export($meta = array_shift($plt)) ?>;
        private $_fields= <?php var_export($plt) ?>;
        private $_con;
        private $_id;

        /**
         * __construct
         *
         * @access public
         * @return void
         */
        <?php
        $requiredPlt = array();
        foreach ($plt as $key=>$value) {
            if (isset($value['req']) && $value['req'] == "1") {
                $requiredPlt[$key] = $key;
            }
        }
        ?>
        public function __construct(<?php if (count($requiredPlt)) {
                                        echo "$" . implode(',$', array_keys($requiredPlt));
                                    }
                                    ?>)
        {
            $this->_id = null;
            $this->_con = null;
            $this->_list = array(); // Si no es una lista esto se mantendrá vacío
            $this->_current = 0; // Mientras no sea una lista esto será cero
            <?php
                echo "\n";
                $strValues = array();
                foreach($requiredPlt as $key=>$value) {
                    //$strValues[] = $oPHP . " echo \$this->_fields[\"$key\"][\"value\"]" . $cPHP;
                    $strValues[] = "{\$this->_fields[\"$key\"][\"value\"]}";
                    echo "\t\t\$this->_fields[\"$key\"][\"value\"] = con::escape(\$$key) ;\n";
                }
            ?>
        }

        /**
         * offsetExists
         *
         * @param mixed $offset
         * @access public
         * @return void
         */
        public function offsetExists($offset)
        {
            return isset($this->_fields[$offset]);
        }

        /**
         * offsetGet
         *
         * @param mixed $offset
         * @access public
         * @return void
         */
        public function offsetGet($offset)
        {
            if ($offset == '_fields') {
                return $this->_fields;
            }
            return $this->_fields[$offset];
        }


        /**
         * offsetSet
         *
         * @param mixed $offset
         * @access public
         * @return void
         */
        public function offsetSet($offset, $value)
        {
            return new Exception('Setting via arrayaccess is disallowed');
        }

        /**
         * offsetUnset
         *
         * @param mixed $offset
         * @access public
         * @return void
         */
        public function offsetUnset($offset)
        {
            return new Exception('Unsetting via arrayaccess is disallowed');
        }

        /**
         * __get
         *
         * @param mixed $offset
         * @access public
         * @return void
         */
        public function __get($offset)
        {
            return $this->offsetGet($offset);
        }

        /**
         * _query
         *
         * @param mixed $sql
         * @access private
         * @return void
         */
        private function _query($sql)
        {
            if (is_null($this->_con)) {
                $this->_con = new con($sql);
            } else {
                $this->_con->query($sql);
            }

        }

        /**
         * getCon
         *
         * @access public
         * @return void
         */
        public function getCon()
        {
            return $this->_con;
        }

        /**
         * insert
         *
         * @access public
         * @return void
         */
        public function insert()
        {

            //$insertArr = array();
            $strValues = array();
            foreach ($this->_fields as $key=>$value) {
                if (isset($value["value"])) {
                    $strValues[] = "\"" . $value["value"] . "\"";
                } else if ($key == "status") {
                    $strValues[] = 1;
                } else if ($key == "deleted") {
                    $strValues[] = 0;
                } else if ($key == "created_at") {
                    $strValues[] = "\"0000-00-00 00:00:00\"";
                } else if ($key == "modified_at") {
                    $strValues[] = "\"0000-00-00 00:00:00\"";
                } else {
                    $strValues[] = "\"\"";
                }
            }

            $this->_query("INSERT INTO `<?php echo $meta['tab']?>` (<?php echo '`' . implode('`,`', array_keys($plt)) . '`'  ?>) VALUES (" . implode(",", $strValues) . ")");
            $this->_id = $this->_con->getId();
            return $this;
        }


        /**
         * populate
         *
         * @access private
         * @return void
         */
        private function populate()
        {
            if ($this->_con->getNumRows() == 1) {
                $res = $this->_con->getResult();
                foreach ($res as $key=>$value) {
                    if ($key == $this->_meta['id']) { // Actualizar el campo id independiente
                        $this->_id = $value;
                    }
                    $this->_fields[$key]["value"] = con::escape($value);;
                }
            } else {
                while($res = $this->_con->getResult()) {
                $newMember = new <?php echo $className ?>();
                    foreach ($res as $key=>$value) {
                        $newMember->setField($key, $value);
                    }
                    $this->add($newMember);
                }
            }
            return $this;
        }


        /**
         * findById
         *
         * @param mixed $id
         * @access public
         * @return void
         */
        public function findById($id, $operator = '=', $extraAlreadyEscaped = '')
        {
            $cleanId = (int) $id;
            $this->_query("SELECT * FROM `<?php echo $meta['tab'] ?>` WHERE `<?php echo $meta['id'] ?>` $operator $cleanId $extraAlreadyEscaped");
            $this->populate();
            return $this;
        }

        /**
         * findAll
         *
         * @access public
         * @return void
         */
        public function findAll()
        {
            $this->_query("SELECT * FROM `<?php echo $meta['tab'] ?>`");
            $this->populate();
            return $this;
        }

<?php
        foreach ($plt as $key=>$value) {
?>
        public function findBy<?php echo ucfirst(to_camel_case($key)) ?>($value, $operator = '=', $extraAlreadyEscaped = '')
        {

            $this->_query("SELECT * FROM `<?php echo $meta['tab'] ?>` WHERE `<?php echo $key ?>` $operator \"$value\" $extraAlreadyEscaped");

            $this->populate();
            return $this;
        }
<?php
            if ($key != "id" && $key != "Id") {
?>
        public function get<?php echo ucfirst(to_camel_case($key)) ?>()
        {
            return $this->_fields[<?php echo '"' . $key . '"' ?>]["value"];
        }

        public function getMaxValue<?php echo ucfirst(to_camel_case($key)) ?>()
        {
            $this->_query("SELECT MAX(`<?php echo $key ?>`) AS `max` FROM `<?php echo $meta['tab'] ?>`");
            $result = $this->_con->getResult();
            return $result['max'];
        }

        public function set<?php echo ucfirst(to_camel_case($key)) ?>($value)
        {
            if (isset($this->_fields[<?php echo '"' . $key . '"' ?>]["value"])) { // If there is prev value then this is an update
                $this->_pendingUpdateList[] = "<?php echo $key ?>"; // Add key to pending list
            }
            $this->_fields[<?php echo '"' . $key . '"' ?>]["value"] = $value;
            return $this;
        }
<?php
            }
        }
?>

        public function select(<?php implode(',', array_keys($plt)) ?>)
        {
            $this->_query("");
        }

        /**
         * delete
         *
         * @access public
         * @return void
         */
        public function delete()
        {
            if ($this->_id != null) {
<?php
                if (isset($meta['deleted'])) { // borrado logico
?>
                $this->_query("UPDATE <?php echo $meta['tab'] ?> SET `<?php echo $meta['deleted'] ?>` = 1");
<?php
                } else { // borrado fisico
?>
                $this->_query("DELETE FROM <?php echo $meta['tab'] ?> WHERE <?php echo $meta['id'] ?>=" . $this->getId());
<?php
                }
?>
            }
        }

        /**
         * update
         *
         * @access public
         * @return void
         */
        public function update()
        {
            $sql = "UPDATE <?php echo $meta['tab'] ?> ";
            $i = 0;
            foreach($this->_pendingUpdateList as $pendingUpdate) {
                if ($i == 0) {
                    $sql .= 'SET `' . $pendingUpdate . '`="' . $this->_fields[$pendingUpdate]["value"] . '" ';
                } else {
                    $sql .= ',`' . $pendingUpdate . '`="' . $this->_fields[$pendingUpdate]["value"] . '" ';
                }
            }
            $sql .= ' WHERE <?php echo $meta['id'] ?>=' . $this->getId();
            $this->_query($sql);
            $this->_pendingUpdateList = array();
            return $this;
        }

        /**
         * getMeta
         *
         * @access public
         * @return void
         */
        public function getMeta()
        {
            return $this->_meta;
        }

        /**
         * getId
         *
         * @access public
         * @return void
         */
        public function getId()
        {
            return $this->_id;
        }

        /**
         * getField
         *
         * @param mixed $key
         * @access public
         * @return void
         */
        public function getField($key)
        {
            return $this->_fields[$key];
        }

        /**
         * getFieldValue
         *
         * @param mixed $key
         * @access public
         * @return void
         */
        public function getFieldValue($key)
        {
            return $this->_fields[$key]["value"];
        }

        /**
         * getFields
         *
         * @access public
         * @return void
         */
        public function getFields()
        {
            return $this->_fields;
        }

        /**
         * getList
         *
         * @access public
         * @return void
         */
        public function getList()
        {
            return $this->_list;
        }

        /**
         * count
         * Countable interface
         * @access public
         * @return void
         */
        public function count()
        {
            return count($this->_list);
        }

        /**
         * setField
         *
         * @param mixed $key
         * @param mixed $value
         * @param mixed $doEscape (whether escape is needed)
         * @access public
         * @return void
         */
        public function setField($key, $value, $doEscape = true)
        {
            if ($key == $this->_meta['id']) { // Actualizar el campo id independiente
                $this->_id = $value;
            }
            if ($doEscape) {
                $value = con::escape($value);
            }
            if (isset($this->_fields[$key]["value"])) { // If there was prev value then this is an update
                $this->_pendingUpdateList[] = $key;
            }
            $this->_fields[$key]["value"] = $value;
            return $this;
        }

        /**
         * setFields
         *
         * @param array $fields
         * @param mixed $doEscape
         * @access public
         * @return void
         */
        public function setFields(array $fields, $doEscape = true)
        {
            foreach ($fields as $key=>$value) {
                $this->setField($key, $value, $doEscape);
            }
            return $this;

        }


        /* Iterator pattern stuff */


        /**
         * current
         *
         * @access public
         * @return void
         */
        public function current()
        {
            return $this->_list[$this->_current];
        }

        /**
         * next
         *
         * @access public
         * @return void
         */
        public function next()
        {
            $this->_current += 1;
        }

        /**
         * rewind
         *
         * @access public
         * @return void
         */
        public function rewind()
        {
            $this->_current = 0;
        }

        /**
         * key
         *
         * @access public
         * @return void
         */
        public function key()
        {
            return $this->_current;
        }

        /**
         * valid
         *
         * @access public
         * @return void
         */
        public function valid()
        {
            return isset($this->_list[$this->_current]);
        }

        public function add(<?php echo $className ?> $obj)
        {
            $this->_list[] = $obj;
        }


        /* Useful for ajax */

        /**
         * toArray
         *
         * @access public
         * @return void
         */
        public function toArray() {
            return get_object_vars($this);
        }

}
<?php

@include_once('PHP/Beautifier.php');
if (class_exists('PHP_Beautifier')) {
    /**
     * El Beautifier lanza la hostia de deprecateds en PHP 5.3,
     * asi que no reportamos errores y a cascarla
     */
    error_reporting(0);
    $oBeautifier = new PHP_Beautifier();
    $oBeautifier->addFilter('Lowercase');
    $oBeautifier->addFilter('ArrayNested');
    $oBeautifier->addFilter('Pear', array('add_header' => 'lgpl'));
    $oBeautifier->setIndentChar(' ');
    $oBeautifier->setIndentNumber(4);
    $oBeautifier->setNewLine("\n");
    $oBeautifier->setInputString(ob_get_clean());
    $oBeautifier->process();
    $out = $oBeautifier->get();
    $out = str_replace('<one line to give the library\'s name and a brief idea of what it does.>', $className, $out);
    $out = str_replace('<year>  <name of author>', date('Y') . ' Irontec', $out);
    echo $out;
} else {
    ob_end_flush();
    error_log("Please, remember that PHP_Beautifier is recommended for a cleaner code\n #pear install PHP_Beautifier-beta\n");
}
