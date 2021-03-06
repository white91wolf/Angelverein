<?php

class Application_Model_DbTable_BilderTable extends Zend_Db_Table_Abstract {

    protected $_name = "bilder";

    public function getAllPicsByUserId($userid = null) {
        $rows = null;

        if (!empty($userid)) {
            $select = $this->select()->where('user_id = ?', (int) $userid);
            $rows = $this->fetchAll($select);
        }

        return $rows;
    }

    public function getImagePathByID($id = null) {
        $rows = $this->find($id);
        $path = null;

        if (count($rows) > 0) {
            $row = $rows->current();

            $path = APPLICATION_PATH . $row->dateiname;
        }

        return $path;
    }

}
