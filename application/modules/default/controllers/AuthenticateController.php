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
    public function init() {
        $this->userTable = new Application_Model_DbTable_UserTable();
    }

    public function indexAction() {
        
    }
}
