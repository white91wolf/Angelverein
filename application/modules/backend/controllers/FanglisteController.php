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
    protected $request;
    protected $isAdmin;

    public function init() {
        $this->isAdmin =false;
        $this->request = $this->getRequest();
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
        $form = null;
        $c = 1;

        if ($this->getRequest()->isPost() && isset($_POST['group_counter'])) {
            $c = (int) $_POST['group_counter'];
        }
 
        if ($this->getRequest()->isGet() && isset($_GET['fanglistid'])) { 
            $fanglistId = $this->getRequest()->getParam('fanglistid');
            $fanglist = $this->fanglisteTable->getEntryById($fanglistId);

            if (!empty($fanglist) && ($fanglist->user_id == $this->currentUserID || $this->isAdmin)) {
                $form = $this->generateFishForms($c);
                
                if ($form->isValid($_POST)) {
                    $fanglist->gewaesser_id = $form->getValue('gewasser');
                    $fanglist->datum = $form->getValue('date');
                    $fanglist->save();
                    
                   //Alle alten Fischeinträge löschen
                   $this->fanglisteEintragTable->deleteAllByFangId($fanglisteId);
                    
                   $this->createFishEntriesFromForm($form);
                    
                }
                
                $eintraege = $this->fanglisteEintragTable->getAllEntriesByFanglisteId($fanglistId);
                $anzahlEintraege = $eintraege->count();

                

                $form->getElement('date')->setValue($fanglist->datum);
                $form->getElement('gewaesser')->setValue($fanglist->gewaesser_id);

                for ($i = 0; $i < $anzahlEintraege; $i++) {
                    $row = $eintraege->next();
                    $form->getElement($i . '_fishtypebox')->setValue($row->fisch_id);
                    $form->getElement($i . '_countinput')->setValue($row->anzahl);
                    $form->getElement($i . '_weightinput')->setValue($row->gewicht);
                }
            }
        }
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

    public function newfishgroupAction() {
        $counter = $this->_getParam('counter');

        if ($counter !== null) {
            $fishForm = new Application_Model_Forms_FanglisteForm($this->fishes, $this->gewaesser);
            $fishForm->setCounter($counter);
            $ajaxContext = $this->_helper->getHelper('AjaxContext');
            $ajaxContext->addActionContext('newfishgroup', 'html')->initContext();

            $this->view->field_a = $fishForm->getFishTypeSelectBox();
            $this->view->field_b = $fishForm->getCountFishesTextBox();
            $this->view->field_c = $fishForm->getFishWeightTextBox();
        }
    }

    
    public function createAction() {
        $c = 1;

        if ($this->getRequest()->isPost() && isset($_POST['group_counter'])) {
            $c = (int) $_POST['group_counter'];
        }

        $form = $this->generateFishForms($c);
     
        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {

            $date = $form->getValue('date');
            $gewaesser = $form->getValue('gewaesser');

            $fanglistId = $this->fanglisteTable->createNewContent($this->currentUserID, $date, $gewaesser);
            
            $this->createFishEntriesFromForm($form, $fanglistId);
            redirect();
        }

        $this->view->form = $form;
    }

    private function generateFishForms($count) {
        $form = new Application_Model_Forms_FanglisteForm($this->fishes, $this->gewaesser);
        // Generierung in form auslagern, validator fehler zerstoeren uA form ..
        for ($i = 0; $i < $count; $i++) {
            $form->addFishFormElements();
        }

        return $form;
    }

    public function createFishEntriesFromForm($form = null, $fanglisteId = null) {
        if(!(empty($form) || empty($fanglisteId)) && $fanglisteId > 0){
            $fishtyp = $form->getValue('fishType');
            $count = $form->getValue('count_fishes');
            $gewicht = $form->getValue('weight');
            
            $count_form = count($fishtyp); 
            for ($i = 0; $i < $count_form; $i++) {
                $this->fanglisteEintragTable->createNewContent($fishtyp[$i], $count[$i], $gewicht[$i], $fanglisteId);
            }
        }
    }

}
