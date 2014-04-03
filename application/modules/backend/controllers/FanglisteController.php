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
          } */

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

    public function createAction() {
        $c = 1;
        
        if ($this->getRequest()->isPost() && isset($_POST['counter'])) {
            $c = (int) $_POST['counter'];
        }
        
        $form = $this->generateFishForms($c);
        if ($this->getRequest()->isPost() && $form->valid($_POST)) {
            
            $date = $form->getValue('date');
            $gewaesser = $form->getValue('gewaesser');
            
            $fanglistId = $this->fanglisteTable->createNewContent($this->currentUserID, $date, $gewaesser);
             
            for($i = 0; $i < $c; $i++){
                $fishtyp = $form->getValue($i.'_fishtypebox');
                $count = $form->getValue($i.'_countinput');
                $gewicht = $form->getValue($i.'_weightinput');
                
                $this->fanglisteEintragTable->createNewContent($fishtyp, $count, $gewicht, $fanglistId);
            }
        }

        $this->view->form = $form;
    }

    private function generateFishForms($count) {
        $form = new Application_Model_Forms_FanglisteForm($this->fishes);
        $form->addGewaesser($this->gewaesser);
        
        for ($i = 0; $i < $count; $i++) {
            $form->addFishFormElements($i);
        }

        return $form;
    }

}
