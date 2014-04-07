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
        $form = null;
        //erst prüfen ob form valid dann änderungen speichern damit das nächste if auch greifft und die daten eingetragen werden

        if ($this->getRequest()->isPost() && isset($_POST['fanglistid'])) {
            $fanglistId = $this->getRequest()->getParam('fanglistid');
            $fanglist = $this->fanglisteTable->getEntryById($fanglistId);

            if (!empty($fanglist) && ($fanglist->user_id == $this->currentUserID || $this->currentUserRole == 'vorstand')) {
                $eintraege = $this->fanglisteEintragTable->getAllEntriesByFanglisteId($fanglistId);
                $anzahlEintraege = $eintraege->count();

                $form = $this->generateFishForms($anzahlEintraege);

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
        if (!$this->getRequest()->isPost()) {
            $this->view->headScript()->appendFile($this->view->baseUrl() . '/resource/js/catchlist.js'); // TODO pfad irgendwo einheitlich notieren
        }
        $c = 1;

        if ($this->getRequest()->isPost() && isset($_POST['group_counter'])) {
            $c = (int) $_POST['group_counter'];
        }

        $form = $this->generateFishForms($c);
     
        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {

            $date = $form->getValue('date');
            $gewaesser = $form->getValue('gewaesser');

            $fanglistId = $this->fanglisteTable->createNewContent($this->currentUserID, $date, $gewaesser);

            $fishtyp = $form->getValue('weight');
            $count = $form->getValue('count_fishes');
            $gewicht = $form->getValue('fishType');

            $count_form = count($fishtyp);
            for ($i = 0; $i < $count_form; $i++) {
                $this->fanglisteEintragTable->createNewContent($fishtyp[$i], $count[$i], $gewicht[$i], $fanglistId);
            }
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

}
