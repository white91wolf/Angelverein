<?php

class Application_Model_DbTable_ResetPasswordTable extends Zend_Db_Table_Abstract {

    protected $_name = "resetPassword";

    
    public function createNewContent($userid) {
        $return = false;
        if(!empty($userid)) {
            $hash; //TODO supertolle hashcreate methode oda so
            
           $this->insert(
                array(
                    'hash' => $hash,
                    'user_id' => (int)$userid
                )
            );
        }

        return $return;
    }
    
    public function confirmHash ($hash = null){
        $rows = null;
        
        if(!empty($hash)){
            $select = $this->select()->where('hash = ?', $hash)->where('used = ?', false);
            $rows = $this->fetchAll($select)->current();
            
            
        }
        
        return $rows;

    }
    
    public function setHashUsed ($hash = null){
        $rowscount = 0;
        
        if(!empty($hash)){
            $data = array(
                'used' => true
            );

            $where = $this->getAdapter()->quoteInto('hash = ?', $hash);
            
            $rowscount = $this->update($data, $where);
            
            
        }
        
        return ($rowscount == 1);

    }
    
    
    

}
