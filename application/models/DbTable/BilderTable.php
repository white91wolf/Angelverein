<?php

class Application_Model_DbTable_BilderTable extends Zend_Db_Table_Abstract {

    protected $_name = "bilder";
    
    public function getAllPicsByUserId($userid = null) {
        if($userid != null){
            $select = $this->select()->where('user_id = ?', $userid);
            $rows = $this->fetchAll($select);

            return $rows;
        }  else {
            return null;
        }
        
    }
}
