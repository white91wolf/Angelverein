<?php

class Application_Model_DbTable_ContentTypeTable extends Zend_Db_Table_Abstract {

    protected $_name = "content_type";
    
    public function getAllContentTypes() {

        $rows = $this->fetchAll();

        return $rows;
    }

}
