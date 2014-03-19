<?php

class Application_Model_DbTable_GewaesserTable extends Zend_Db_Table_Abstract {

    protected $_name = "gewaesser";

    public function getAll() {
        $rows = $this->fetchAll();

        return $rows;
    }

}
