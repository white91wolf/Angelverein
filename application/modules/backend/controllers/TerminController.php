<?php

class Backend_TerminController extends Zend_Controller_Action {

    protected $currentUserID;
    protected $currentUserName;
    protected $currentUserRole;
    protected $terminTable;
    protected $form;
    protected $request;
    protected $isAdmin;
    protected $terminRoleTable;
    protected $terminUserTable;
    protected $roles;

    public function init() {
        $this->isAdmin = false;
        $this->request = $this->getRequest();
        $this->currentUserID = Application_Plugin_Auth_AccessControl::getUserID();
        $this->currentUserName = Application_Plugin_Auth_AccessControl::getUserName();
        $this->currentUserRole = Application_Plugin_Auth_AccessControl::getUserRole();

        $this->terminTable = new Application_Model_DbTable_TerminTable();
        $this->terminRoleTable = new Application_Model_DbTable_TerminRolleTable();
        $this->terminUserTable = new Application_Model_DbTable_TerminUserTable();

        $roleTable = new Application_Model_DbTable_RolleTable();
        $this->roles = $roleTable->getAll()->toArray();
    }

    public function indexAction() {
        
    }

    /*
      public function createAction() {
     * editAction(null);
     * }
     * 
     * public function editAction() {
     * if(getgedönst) {
     * //usw
     * $this->createAction($formData);
     * }
     * 
     * public function createAction($formData = null) {
     * if(!empty($formdata)) {
     * eintragen bla
     * }
     * $muh = "createAction";
     * $this->$muh()
     */

//TODO in adminbereich auslagern
    public function editAction() {
        $form = $this->getForm();

        if (($this->request->isGet() || $this->request->isPost()) && isset($_GET['terminid'])) {
            $terminid = $this->request->getParam('terminid');
            $termin = $this->terminTable->getById($terminid);

            if (!empty($termin) && ($this->isAdmin || $this->currentUserRole == 'Vorstand')) {
                if ($this->request->isPost() && $form->isValid($_POST)) {
                    $termin->datum = $form->getValue('date');
                    $termin->beschreibung = $form->getValue('description');


                    $termin->save();
                }

                $form->getElement('date')->setValue($termin->datum);
                $form->getElement('description')->setValue($termin->beschreibung);
                $form->getElement('name')->setValue($termin->name);
                $form->getElement('register')->setValue($termin->anmeldung);

                $enabledRoles = $this->terminRoleTable->getRollesIdByTerminId($terminid);

                $form->selectDisabledRolesByArray($this->roles, $enabledRoles);
            }
        }

        $this->view->form = $form;
    }

//TODO in adminbereich auslagern
    public function createAction() {
        $form = $this->getForm();

        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {

            $name = $form->getValue('name');
            $date = $form->getValue('date');
            $description = $form->getValue('description');
            $register = $form->getValue('register');

            $terminId = $this->terminTable->createContent($name, $date, $description, $register);

            $enabledRoles = $form->getEnabledRoles();

            if (!empty($enabledRoles)) {
                foreach ($enabledRoles as $enabledValue) {
                    $this->terminRoleTable->createContent($terminId, $enabledValue);
                }
            }
        }

        //echo('Name: '.$name.'<br/>Datum: '.$date.'<br/>Beschreibung: '.$description.'<br/>Ausgeschlossene Rollen:'.$disabledRoles);
        //echo($description .'  -  '. $hours.'  -  '. $date.'  -  '. $this->currentUserID);die();
        //TODO redirect auf übersicht oder so


        $this->view->form = $form;
    }

    private function getForm() {
        $form = new Application_Model_Forms_TerminForm();
        //var_dump($this->roles);die();
        $form->addRoleSelect($this->roles);

        return $form;
    }

}
