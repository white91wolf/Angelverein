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
            $this->_redirect('backend/user/userid/' . $this->currentUserID);
            //TODO entweder extra action oder alles in viewhelper packen
            //$html = viewhelper->genHtmlKot()
            //$this->view->html = $html
        } else {
            $this->_redirect('backend/user/login');
        }
    }
    
        public function activateuserAction() {
        $confirmed = false;
        if (isset($_GET['userid'])) {
            $userid = $this->request->getParam('userid');
            $confirmed = $this->userTable->activateUser($userid);
        }
        $this->view->confirmed = $confirmed;
    }
}
