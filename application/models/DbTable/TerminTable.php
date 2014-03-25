<?php

class Application_Model_DbTable_TerminTable extends Zend_Db_Table_Abstract {

    protected $_name = "termin";

    public function getAll() {
        $rows = $this->fetchAll();

        return $rows;
    }

    public function getAllNextEntriesByDate($datum = null) {
        $rows = null;
        
        if ($datum != null) {
            $select = $this->select()->where('datum >= ?', $datum);
            $rows = $this->fetchAll($select);

            
        } 
        
        return $rows;
    }

}
