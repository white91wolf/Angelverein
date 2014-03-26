<?php

class IndexController extends Zend_Controller_Action
{
    protected $contentTable;

    public function init()
    {
        $this->contentTable = new Application_Model_DbTable_ContentTable();
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        $activePageId = null; // TODO aus config bla default holen!
        
        if($request->isGet()) {
            $activePageId = (int)$request->getParam('content');
        }
        
        $page = $this->contentTable->getEntryById($activePageId);
        
        if(empty($page) || !$page->public) {
            $page = null;
        }
        // TODO $this->view->page = InlineModules_Bla::EvocateModule($page);
        $this->view->page = $page;
    }


}

