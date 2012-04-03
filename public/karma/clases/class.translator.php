<?php


class translator
{
    protected $_currentLang;

    protected $_configFilesLiterals;

    protected $_langFileName = 'langFile.php';

    protected $_langSeparator = "::";

    public function __construct()
    {
        $this->_currentLang = KarmaRegistry::getInstance()->get('lang');
        $this->_loadLiterals();
    }

    protected function _loadLiterals()
    {
        $file = dirname(__FILE__) . '/../../configuracion/lang/' . $this->_langFileName;
        if (file_exists( $file )) {
            try {
                include($file);
            } catch (Exception $e) {
            }
            if (isset($multiLangLiterals) && is_array($multiLangLiterals) && sizeof($multiLangLiterals)>0) {
                $this->_configFilesLiterals = $multiLangLiterals;
            }
        }
    }

    public function isTranslated($key, $arr, $q="alias") {
        if ( isset($arr[$q.$this->_langSeparator.$this->_currentLang]) ) {
            return true;
        }
        if (
            isset($this->_configFilesLiterals[$key][$this->_currentLang])
            &&
            $this->_configFilesLiterals[$key][$this->_currentLang]!=""
            &&
            $this->_configFilesLiterals[$key][$this->_currentLang]!=false
        ) {
            return true;
        }
        return false;
    }

    public function translate($key, $arr, $q="alias") {
        if ( isset($arr[$q.$this->_langSeparator.$this->_currentLang]) ) {
            return $arr[$q.$this->_langSeparator.$this->_currentLang];
        }
        if (
            isset($this->_configFilesLiterals[$key][$this->_currentLang])
            &&
            $this->_configFilesLiterals[$key][$this->_currentLang]!=""
            &&
            $this->_configFilesLiterals[$key][$this->_currentLang]!=false
        ) {
            return $this->_configFilesLiterals[$key][$this->_currentLang];
        }
        return $key;
    }
}