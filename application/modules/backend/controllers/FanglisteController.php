<?php

class Backend_FanglisteController extends Zend_Controller_Action {

    protected $currentUserID;
    protected $currentUserName;
    protected $currentUserRole;
    protected $fanglisteTable;
    protected $fanglisteEintragTable;
    protected $gewaesser;

    public function init() {
        $this->currentUserID = Application_Plugin_Auth_AccessControl::getUserID();
        $this->currentUserName = Application_Plugin_Auth_AccessControl::getUserName();
        $this->currentUserRole = Application_Plugin_Auth_AccessControl::getUserRole();

        $this->fanglisteTable = new Application_Model_DbTable_FanglisteTable();
        $this->fanglisteEintragTable = new Application_Model_DbTable_FanglisteEintragTable();
        
        $gewaesserTable = new Application_Model_DbTable_GewaesserTable();
        $this->gewaesser = $gewaesserTable->getAll();
        
    }

    public function indexAction() {
        
    }

    public function editAction() {
        $form = new Application_Model_Forms_FanglisteForm();
        $form->addGewaesser($this->gewaesser);
        $this->view->form = $form;
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
