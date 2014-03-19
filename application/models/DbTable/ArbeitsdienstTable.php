<?php

class Application_Model_DbTable_ArbeitsdienstTable extends Zend_Db_Table_Abstract {

    protected $_name = "arbeitsdienst";

    public function getAllByUserId($userid = null) {
        if ($userid != null) {
            $select = $this->select()->where('user_id = ?', $userid);
            $rows = $this->fetchAll($select);

            return $rows;
        } else {
            return null;
        }
    }

    public function getAllNotConfirmed() {

        $select = $this->select()->where('bestaetigt = ?', false);
        $rows = $this->fetchAll($select);

        return $rows;
    }

}
