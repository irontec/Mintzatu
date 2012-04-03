<?php

class Mintzatu_View_Helper_Kontadorea extends Zend_View_Helper_Abstract
{
    public function Kontadorea()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $ip = $_SERVER['REMOTE_ADDR'];
        $select = $db->select()->from('contador')->where('ip = ?', $ip)->order('cuando DESC');
        $result = $db->fetchRow($select);
        if ($result) {
            $diferentzia = time() - strtotime($result['cuando']);
            if ($diferentzia/(60*60) > 2) {
                $db->insert('contador', array('ip' => $ip));
            }
        } else {
            $db->insert('contador', array('ip' => $ip));
        }
        $sel = $db->select()->from('contador', 'count(*) as count');
        $res = $db->fetchRow($sel);
        return $res['count'] . '. bisitaria zara';
    }
}