<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AuthenticateController
 * TODO user objekt in session ablegen oder so
 * @author FloH
 */
class AuthenticateController extends Zend_Controller_Action {
    protected $userTable;
    protected $request;

    public function init() {
        $this->request = $this->getRequest();	

        $this->userTable = new Application_Model_DbTable_UserTable();
    }

    public function indexAction() {
        
    }
    
    
    public function loginAction() {
        $form = new Application_Model_Forms_UserLoginForm();
        $string = '';
        if (($redirect = $this->request->getParam('redirect_after_login')) != null) {
            $form->setRedirectAfterLoginField($redirect);
        }
        
        if ($this->request->isPost() && $form->isValid($_POST) && empty($this->currentUserID)) {
            $user = $this->userTable->getUserByName($form->getValue('login_user'));
            if($user != null && $user->freigeschaltet == false){
                $string = 'Benutzer wurde noch nicht freigeschaltet';
            }else{
                $string = 'Benutzername oder Kennwort falsch!';
            }
            $form->getElement('login_user')->addError($string);
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
                    $bcrypt = new Zend2_Crypt_Password_Bcrypt();
                    
                    $user = $this->userTable->createRow();
                    $user->username = $username;
                    $user->email = $mail;
                    //TODO password besser verschlüsseln - nutzt nun bcrypt - brauch aber php 5.3 +
                    $user->password = $bcrypt->create($form->getValue('password'));
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
    /*
    public function recoverusernameAction() {
        $form = new Application_Model_Forms_UserRecoverPasswordForm();
        if ($this->request->isPost() && $form->isValid($_POST)) {
            
        }
    }
    */
    public function recoveruserpasswordAction() {
        $form = new Application_Model_Forms_UserRecoverPasswordForm();
        if ($this->request->isPost() && $form->isValid($_POST)) {
            $rowsByMail = $this->userTable->getUserByMail($mail);
                if (count($rowsByMail) > 0) {
                    /*  //Config usw auslagern nur damit es iwo ist wos gebraucht wird
                        $config = array('ssl' => 'tls', 'port' => 587, 'auth' => 'login', 'username' => 'armaserielist@gmail.com', 'password' => 'H0chschule0snabrueck');
                        $smtpConnection = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);
                        Zend_Mail::setDefaultTransport($smtpConnection);
                        $mail = new Zend_Mail();
                        $mail->setBodyText('Ihr neues Passwort lautet: '.$newPw);
                        $mail->setFrom('pw-recovery@serieslist.com', 'Neues Passwort!');
                        $mail->addTo($userMail, $userName);
                        $mail->setSubject('Neues Passwort');
                        $mail->send();
                     */
                }  else {
                    $form->getElement('email')->addError('Es wurde kein Nutzer zu dieser Mail Adresse gefunden!');
                }
            
        }
    }
    
    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        $this->currentUserID = 0;
        $this->_redirect('index');
    }

    
    
    public function requiredloginAction() {
        //TODO besseren text ausdenken
        die("DIE MOTHERFUCKER DIE!!!");
    }
}
