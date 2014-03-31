<?php

class Backend_FanglisteController extends Zend_Controller_Action {

    protected $currentUserID;
    protected $currentUserName;
    protected $currentUserRole;
    protected $fanglisteTable;
    protected $fanglisteEintragTable;
    protected $gewaesser;
    protected $fishes;
    protected $form;

    public function init() {
        $this->currentUserID = Application_Plugin_Auth_AccessControl::getUserID();
        $this->currentUserName = Application_Plugin_Auth_AccessControl::getUserName();
        $this->currentUserRole = Application_Plugin_Auth_AccessControl::getUserRole();

        $this->fanglisteTable = new Application_Model_DbTable_FanglisteTable();
        $this->fanglisteEintragTable = new Application_Model_DbTable_FanglisteEintragTable();
        
        $gewaesserTable = new Application_Model_DbTable_GewaesserTable();
        $this->gewaesser = $gewaesserTable->getAll();
        
        $fishTable = new Application_Model_DbTable_FischartenTable();
        $this->fishes = $fishTable->getAll();
        
    }

    public function indexAction() {
        
    }

    public function editAction() {
        $this->form = new Application_Model_Forms_FanglisteForm();
        /*
        if(($this->request->isGet() || $this->request->isPost()) && ($userID = $this->request->getParam('fanglistId')) != null && 
			$this->currentUserID > 0) {
            $fangliste = $this->fanglisteTable->getEntryById();
            //checken ob besitzer oder vorstand
            if(empty($fangliste) && $fangliste ){
                
            }
        }*/
        
        $this->form->addGewaesser($this->gewaesser);
        $this->form->addFishFormElements($this->fishes);
        $this->view->form = $this->form;
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
    
    private function createAction() {
        $this->form = new Application_Model_Forms_FanglisteForm();
        $request = $this->getRequest();
        
        if($request->isGet() || $request->isPost()){
            if($this->form->isValid($_POST)){
                //Fangliste eintragen
                $date = $this->form->getValue('date');
                
                //Fangleisteneinträge foreach eintragen
            }
        }
        
        $this->view->form = $form;
    }
    
    private function addfishformelementsAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
                
        $this->form->addFishFormElements($this->fishes);
    }

}
