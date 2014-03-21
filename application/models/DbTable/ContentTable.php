<?php

class Application_Model_DbTable_ContentTable extends Zend_Db_Table_Abstract {

    protected $_name = "content";

    public function getEntryById($id = null) {
        $rows = null;
        if ($id != null) {
            $rows = $this->find($id);            
        } 
        return $rows;
    }

    public function getAllEntriesByTypeId($typeid = null) {
        $rows = null;
        if ($typeid != null) {
            $select = $this->select()->where('type_id = ?', $typeid);
            $rows = $this->fetchAll($select)->toArray();            
        } 
        return $rows;
        
    }
    
    public function getEntriesByTypeId($typeid = null, $startID = 0, $counter = 10) {
        $rows = null;
        if ($typeid != null) {
            $select = $this->select()->where('type_id = ?', $typeid)->limit($counter, $startID);
            $rows = $this->fetchAll($select)->toArray();            
        } 
        return $rows;
    }

}
