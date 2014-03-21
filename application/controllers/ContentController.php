<?php

class ContentController extends Zend_Controller_Action {
    protected $contentTable;
    protected $contentTypeTable;

    public function init() {
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
            $contentid = (int)$request->getParam('contentid');
            
            // pruefe ob contentid korrekt gesetzt wurde
            // empty == true wenn id = 0 - ist hier in Ordnung, da ids bei 1 anfangen
            if(!empty($contentid)) {
                $content = $this->contentTable->getEntryById($contentid);
                
                // pruefen ob zu der ID ueberhaupt content existiert
                if(!empty($content)) {
                    $form = new Application_Model_Forms_ContentForm();

                    if ($request->isGet() && $request->isPost() && $form->isValid($_POST)) {
                        // Content updaten
                    }
                }
            }
        }
        
        $this->view->content = $content;
    }
    
    public function createAction() {
        $request = $this->getRequest();
        
        if($request->isGet() && $request->isPost()) {
            
        }
    }
}
