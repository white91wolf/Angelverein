<?php

class Application_Model_DbTable_TerminRolleTable extends Zend_Db_Table_Abstract {

    protected $_name = "termin_rolle";

    public function getAll() {
        $rows = $this->fetchAll();

        return $rows;
    }
    
    
    public function getAllByRolleId($rolle_id = null) {
        $rows = null;
        
        if(!empty($rolle_id)) {
            $select = $this->select()->where('rolle_id = ?', (int)$rolle_id);
            $rows = $this->fetchAll($select);
        } 
        
        return $rows;
    }
    
    public function getRolleIdByTerminId($termin_id = null) {
        $rows = null;
        
        if(!empty($termin_id)) {
            $select = $this->select()->where('termin_id = ?', (int)$termin_id);
            $rows = $this->fetchAll($select);            
        }  
        
        return $rows;
    }

}