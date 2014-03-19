<?php

class Application_Model_DbTable_Termin_UserTable extends Zend_Db_Table_Abstract {

    protected $_name = "termin_user";

    public function getAll() {
        $rows = $this->fetchAll();

        return $rows;
    }
    
    public function getAllUsersByTerminId($termin_id = null) {
        if($termin_id != null){
            $select = $this->select()->where('termin_id = ?', $termin_id);
            $rows = $this->fetchAll($select);

            return $rows;
        }  else {
            return null;
        }
        
    }

}
