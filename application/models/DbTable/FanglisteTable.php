<?php

class Application_Model_DbTable_FanglisteTable extends Zend_Db_Table_Abstract {

    protected $_name = "fangliste";
    
    public function getEntryById($id = null) {
        $rows = null;
        
        if ($id != null) {
            $rows = $this->find($id); 
            
            if(!empty($rows)) {
                $rows = $rows->current();
            }
        } 
        
        return $rows;
    }
    
    public function getEntriesByUserId($userid = null) {
        $rows = null;
        
        if ($userid != null) {
            $select = $this->select()->where('user_id = ?', $typeid);
            $rows = $this->fetchAll($select)->toArray(); 
        } 
        
        return $rows;
    }
    
}
