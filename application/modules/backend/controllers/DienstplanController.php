<?php

class Backend_DienstplanController extends Zend_Controller_Action {

    protected $currentUserID;
    protected $currentUserName;
    protected $currentUserRole;
    protected $dienstTable;
    protected $form;
    protected $request;
    protected $isAdmin;

    public function init() {
        $this->isAdmin = false;
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

        if (($this->request->isGet() || $this->request->isPost()) && isset($_GET['dienstid'])) {
            $dienstid = $this->request->getParam('dienstid');
            $dienst = $this->dienstTable->getById($dienstid);

            if (!empty($dienst) && ($dienst->bestaetigt == false) && ($dienst->user_id == $this->currentUserID || $this->isAdmin)) {
                if ($this->request->isPost() && $form->isValid($_POST)) {
                    $dienst->datum = $form->getValue('date');
                    $dienst->beschreibung = $form->getValue('description');
                    $dienst->stunden = $form->getValue('hours');

                    $dienst->bestaetigt = false;
                    $dienst->save();
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
            //echo($description .'  -  '. $hours.'  -  '. $date.'  -  '. $this->currentUserID);die();
            $this->dienstTable->createNewContent($description, $hours, $date, $this->currentUserID);

            //TODO redirect auf übersicht oder so
        }

        $this->view->form = $form;
    }

    //TODO in adminbereich packen
    public function confirmdienstAction() {
        $confirmed = false;
        if ($this->currentUserRole == 'Vorstand' && isset($_GET['dienstid'])) {
            $dienstid = $this->request->getParam('dienstid');
            $this->dienstTable->confirmDienstById($dienstid);

            $confirmed = true;
        }
        $this->view->confirmed = $confirmed;
    }

    private function getForm() {
        $form = new Application_Model_Forms_DienstplanForm();


        return $form;
    }

}
