<?php

class FanglisteController extends Zend_Controller_Action {

    protected $currentUserID;
    protected $currentUserName;
    protected $currentUserRole;
    protected $fanglisteTable;
    protected $fanglisteEintragTable;

    public function init() {
        $this->currentUserID = Application_Plugin_Auth_AccessControl::getUserID();
        $this->currentUserName = Application_Plugin_Auth_AccessControl::getUserName();
        $this->currentUserRole = Application_Plugin_Auth_AccessControl::getUserRole();

        $this->fanglisteTable = new Application_Model_DbTable_FanglisteTable();
        $this->fanglisteEintragTable = new Application_Model_DbTable_FanglisteEintragTable();
    }

    public function indexAction() {
        
    }

    public function editAction() {
        
    }

    private function getAllEntries() {
        $fentries = $this->fanglisteTable->getEntriesByUserId($this->currentUserID);
        $result[] = null;


        foreach ($fentries as $fentry => $fvalue) {
            $entries = $this->fanglisteEintragTable->getAllEntriesByFanglisteId($fvalue);
            foreach ($entries as $entry => $value) {
                $result[$fentry][$entry];
            }
        }
        return $result;
    }

}
