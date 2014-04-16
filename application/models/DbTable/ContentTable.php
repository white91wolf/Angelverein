<?php

class Application_Model_DbTable_ContentTable extends Zend_Db_Table_Abstract {

    protected $_name = "content";

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

    public function getAllEntriesByTypeId($typeid = null) {
        $rows = null;
        
        if ($typeid != null) {
            $select = $this->select()->where('type_id = ?', $typeid);
            $rows = $this->fetchAll($select)->toArray();            
        } 
        
        return $rows;
    }
    
    public function getEntriesByTypeId($typeid = null, $start = 0, $counter = 10) {
        $rows = null;
        
        if ($typeid != null) {
            $select = $this->select()->where('type_id = ?', $typeid)->limit($counter, $start);
            $rows = $this->fetchAll($select)->toArray();            
        } 
        
        return $rows;
    }
    
    public function createNewContent($headline, $content, $user_id, $type_id, $date = null) {
        if(empty($date)) {
            $date = new DateTime();
        }else{
            $date = new DateTime($date);
        }
        
        // TODO indexe des arrays mit DB table abgleichen
        // TODO auf null pruefen, evtl. validation
        $key = $this->insert(
                array(
                    'headline' => $headline,
                    'text' => $content,
                    'user_id' => (int)$user_id,
                    'type_id' => (int)$type_id,
                    'date' => $date->format('Y-m-d')
                )
        );
        
        return $key;
    }
}
