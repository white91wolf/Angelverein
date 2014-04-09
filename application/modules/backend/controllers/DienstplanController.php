<?php

class Backend_DienstplanController extends Zend_Controller_Action {

    protected $currentUserID;
    protected $currentUserName;
    protected $currentUserRole;
    protected $dienstTable;
    protected $form;
    protected $request;

    public function init() {
        $this->request = $this->getRequest();
        $this->currentUserID = Application_Plugin_Auth_AccessControl::getUserID();
        $this->currentUserName = Application_Plugin_Auth_AccessControl::getUserName();
        $this->currentUserRole = Application_Plugin_Auth_AccessControl::getUserRole();

        $this->dienstTable = new Application_Model_DbTable_ArbeitsdienstTable();
    }

    public function indexAction() {
        
    }

    public function editAction() {
        $form = $this->getForm();
        $dienstArr = null;

        if ($this->getRequest()->isPost() && isset($_POST['dienstid'])) {
            $dienstid = $this->request->getParam('dienstid');
            $dienst = $this->dienstTable->getById($dienstid);

            if (!empty($dienst)) {
                if ($form->isValid($_POST)) {
                    $dienst->datum = $form->getValue('date');
                    $dienst->beschreibung = $form->getValue('description');
                    $dienst->stunden = $form->getValue('hours');
                    //TODO meinung von flo und so geht darum, dass wenn jemand aus 2 stunden zb. 20 macht
                    $dienst->bestaetigt = false;
                }
                
                $form->getElement('date')->setValue($dienst->datum);
                $form->getElement('description')->setValue($dienst->beschreibung);
                $form->getElement('hours')->setValue($dienst->stunden);
               
            }
        }

        $this->view->form = $form;
    }

    public function createAction() {
        $form = $this->getForm();

        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {

            $date = $form->getValue('date');
            $description = $form->getValue('description');
            $hours = $form->getValue('hours');

            $this->dienstTable->createNewContent($description, $hours, $date, $this->currentUserID);
        }

        $this->view->form = $form;
    }
    
    public function confirmdienstAction() {
        if($this->currentUserRole == 'vorstand' && isset($_POST['dienstid'])) {
            $dienstid = $this->request->getParam('dienstid');
            $this->dienstTable->confirmDienstById($dienstid);
        }
        
    }

    private function getForm() {
        $form = new Application_Model_Forms_DienstplanForm();


        return $form;
    }

}
