<?php

class Application_Model_DbTable_FischartenTable extends Zend_Db_Table_Abstract {

    protected $_name = "fischarten";

    public function getAll() {
        $rows = $this->fetchAll();

        return $rows->toArray();
    }

}
