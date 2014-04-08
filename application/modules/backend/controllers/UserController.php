<?php

class Backend_UserController extends Zend_Controller_Action {

    protected $currentUserID;
    protected $currentUserName;
    protected $currentUserRole;
    protected $userTable;
    protected $request;

    public function init() {
        $this->request = $this->getRequest();	
        $this->currentUserID = Application_Plugin_Auth_AccessControl::getUserID();
        $this->currentUserName = Application_Plugin_Auth_AccessControl::getUserName();
        $this->currentUserRole = Application_Plugin_Auth_AccessControl::getUserRole();

        $this->userTable = new Application_Model_DbTable_UserTable();
    }

    public function indexAction() {
        if ($this->currentUserID > 0) {
            $this->_redirect('user/userid/' . $this->currentUserID);
        } else {
            $this->_redirect('user/login');
        }
    }

    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        $this->currentUserID = 0;
        $this->_redirect('index');
    }

    public function loginAction() {
        $form = new Application_Model_Forms_UserLoginForm();

        if (($redirect = $this->request->getParam('redirect_after_login')) != null) {
            $form->setRedirectAfterLoginField($redirect);
        }

        if ($this->request->isPost() && $form->isValid($_POST) && empty($this->currentUserID)) {
            $form->getElement('username')->addError('Benutzername oder Kennwort falsch!');
        }
        $this->view->form = $form;
    }

    public function registerAction() {
        $form = new Application_Model_Forms_UserRegisterForm();
        $registred = false;

        if ($this->request->isPost() && $form->isValid($_POST)) {
            /**
             * Prüfen ob User bereits existiert
             * und ob Email nicht schon vergeben ist
             */
            $username = $form->getValue('username');
            $mail = $form->getValue('register_email');

            $rows = $this->userTable->getUserByName($username);

            if (count($rows) == 0) {
                $rowsByMail = $this->userTable->getUserByMail($mail);
                if (count($rowsByMail) == 0) {
                    /**
                     * User in DB festschreiben 
                     */
                    //var_dump($form);die();
                    
                    $user = $this->userTable->createRow();
                    $user->username = $username;
                    $user->email = $mail;
                    //TODO password besser verschlüsseln
                    $user->password = sha1($form->getValue('password'));
                    $user->vorname = $form->getValue('vorname');
                    $user->nachname = $form->getValue('nachname');
                    $user->save();

                    $registred = true;
                } else {
                    $form->getElement('email')->addError('Email Adresse wird bereits verwendet!');
                }
            } else {
                $form->getElement('username')->addError('Username bereits vergeben!');
            }
        }

        $this->view->form = $form;
        $this->view->registred = $registred;
    }
}
