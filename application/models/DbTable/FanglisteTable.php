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
        var_dump($rows); die();
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
    
    public function createNewContent($userId, $date, $gewaesser) {
        if(empty($date) || !($date instanceof Date)) {
            $date = new Date();
        }
        
        // TODO auf null pruefen, evtl. validation
        $key = $this->insert(
                array(
                    'user_id' => $userId,
                    'gewaesser_id' => $gewaesser,
                    'datum' => $date
                )
        );
        
        return $key;
    }
    
}
