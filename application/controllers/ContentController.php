<?php

class ContentController extends Zend_Controller_Action {

    protected $contentTable;
    protected $contentTypeTable;

    public function init() {
        /* Initialize action controller here */
        $this->contentTable = new Application_Model_DbTable_ContentTable();
        $this->contentTypeTable = new Application_Model_DbTable_ContentTypeTable();
    }

    public function indexAction() {
        // action body
    }

    public function editAction() {
        $request = $this->getRequest();
        $content = null;

        if ($request->isGet() || $request->isPost()) {
            $contentid = $request->getParam('contentid');
            $content = $this->contentTable->getEntryById($contentid);
            
            $form = new Application_Model_Forms_ContentForm();
            
            if ($request->isGet() && $request->isPost() && $form->isValid($_POST)) {
                //$content
            }
        }
        
        $this->view->content = $content;
    }

}
