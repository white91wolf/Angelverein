<?php

class Application_Model_DbTable_FanglisteEintragTable extends Zend_Db_Table_Abstract {

    protected $_name = "fangliste_eintrag";
    
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
    
    public function getAllEntriesByFanglisteId($id = null) {
        $rows = null;
        
        if ($id != null) {
            $select = $this->select()->where('fanglist_id = ?', $id);
            $rows = $this->fetchAll($select); 
        } 
        
        return $rows;
    }
    
    public function createNewContent($fischId, $count, $gewicht, $fanglisteId) {
        // TODO auf null pruefen, evtl. validation
        $key = $this->insert(
                array(
                    'fisch_id' => $fischId,
                    'anzahl' => (int)$count,
                    'gewicht' => (int)$gewicht,
                    'fangliste_id' => (int)$fanglisteId
                )
        );
        
        return $key;
    }
}