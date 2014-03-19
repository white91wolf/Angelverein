<?php

class Application_Model_DbTable_RolleTable extends Zend_Db_Table_Abstract {

    protected $_name = "rolle";

    public function getAll() {
        $rows = $this->fetchAll();

        return $rows;
    }

}