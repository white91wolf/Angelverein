<?php

class Application_Model_DbTable_ArbeitsdienstTable extends Zend_Db_Table_Abstract {

    protected $_name = "arbeitsdienst";

    public function getAllByUserId($userid = null) {
        $rows = null;
        
        if (!empty($userid)) {
            $select = $this->select()->where('user_id = ?', (int)$userid);
            $rows = $this->fetchAll($select);
        } 
        
        return $rows;
    }
    
    public function getById($id = null) {
        $rows = null;
        
        if (!empty($id)) {
            $rows = $this->find($id);
            
            if(!empty($rows)){
                $rows = $rows->current();
            }
        } 
   
        return $rows;
    }

    public function getAllNotConfirmed() {

        $select = $this->select()->where('bestaetigt = ?', false);
        $rows = $this->fetchAll($select);

        return $rows;
    }
    
    public function createNewContent($description, $hours, $date, $userid) {
        if(empty($date) || !($date instanceof Date)) {
            $date = new Date();
        }
        
        // TODO indexe des arrays mit DB table abgleichen
        // TODO auf null pruefen, evtl. validation
        $key = $this->insert(
                array(
                    'beschreibung' => $description,
                    'stunden' => (int)$hours,
                    'user_id' => (int)$user_id,
                    'datum' => $date
                )
        );
        
        return $key;
    }
    
    public function confirmDienstById ($id = null){
        $rowscount = 0;
        
        if(!empty($id)){
            $data = array(
                'bestaetigt' => true
            );

            $where = $this->getAdapter()->quoteInto('id = ?', $id);
            
            $rowscount = $this->update($data, $where);
            
            
        }
        
        return ($rowscount == 1);
        //return (bool)$rownscount; 

    }
    

}
