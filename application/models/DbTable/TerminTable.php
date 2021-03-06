<?php

class Application_Model_DbTable_TerminTable extends Zend_Db_Table_Abstract {

    protected $_name = "termin";

    public function getAll() {
        $rows = $this->fetchAll();

        return $rows;
    }
    
    public function getAllByRolleId($rolle_id = null) {
        $rows = null;

        if (!empty($rolle_id)) {
            $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                    ->setIntegrityCheck(false)              
                    ->join('termin_rolle', $this->_name.'.id = termin_rolle.termin_id')
                    ->where('termin_rolle.rolle_id = ?', $rolle_id);
            $rows = $this->fetchAll($select)->toArray();
        }

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

    public function getById($id = null) {
        $rows = null;

        if (!empty($id)) {
            $rows = $this->find($id);

            if (!empty($rows)) {
                $rows = $rows->current();
            }
        }

        return $rows;
    }

    public function createContent($name, $date, $description, $register = false) {
        if (empty($date)) {
            $date = new DateTime();
        } else {
            $date = new DateTime($date);
        }

        $key = $this->insert(
                array(
                    'name' => $name,
                    'beschreibung' => $description,
                    'anmeldung' => (bool) $register,
                    'datum' => $date->format('Y-m-d')
                )
        );

        return $key;
    }

}
