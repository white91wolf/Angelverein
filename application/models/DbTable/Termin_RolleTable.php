<?php

class Application_Model_DbTable_Termin_RolleTable extends Zend_Db_Table_Abstract {

    protected $_name = "termin_rolle";

    public function getAll() {
        $rows = $this->fetchAll();

        return $rows;
    }
    
    
    public function getAllByRolleId($rolle_id = null) {
        if($rolle_id != null){
            $select = $this->select()->where('rolle_id = ?', $rolle_id);
            $rows = $this->fetchAll($select);

            return $rows;
        }  else {
            return null;
        }
        
    }
    
    public function getRolleIdByTerminId($termin_id = null) {
        if($termin_id != null){
            $select = $this->select()->where('termin_id = ?', $termin_id);
            $rows = $this->fetchAll($select);

            return $rows;
        }  else {
            return null;
        }
        
    }

}