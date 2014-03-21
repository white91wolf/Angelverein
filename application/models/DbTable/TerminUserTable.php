<?php

class Application_Model_DbTable_TerminUserTable extends Zend_Db_Table_Abstract {

    protected $_name = "termin_user";

    public function getAll() {
        $rows = $this->fetchAll();

        return $rows;
    }

    public function getAllUsersByTerminId($termin_id = null) {
        $rows = null;
        
        if (!empty($termin_id)) {
            $select = $this->select()->where('termin_id = ?', (int)$termin_id);
            $rows = $this->fetchAll($select);
        }
        
        return $rows;
    }

}
