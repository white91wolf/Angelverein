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
    

}
