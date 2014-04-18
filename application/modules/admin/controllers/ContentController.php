<?php

// TODO Linkgeadline fuer Pages
class Admin_ContentController extends Zend_Controller_Action {

    protected $contentTable;
    protected $contentTypeTable;

    public function init() {
        $this->contentTable = new Application_Model_DbTable_ContentTable();
        $this->contentTypeTable = new Application_Model_DbTable_ContentTypeTable();
    }

    public function indexAction() {
        $request = $this->getRequest();
        $activePageId = null; // TODO aus config bla default holen!

        if ($request->isGet()) {
            $activePageId = (int) $request->getParam('content');
        }

        $page = $this->contentTable->getEntryById($activePageId);

        if (empty($page) || $page->public) {
            $page = null;
        }
        // TODO $this->view->page = InlineModules_Bla::EvocateModule($page);
        $this->view->page = $page;
    }

    public function editAction() {
        $request = $this->getRequest();
        $content = null;

        if ($request->isGet() || $request->isPost()) {
            $contentid = (int) $request->getParam('contentid');

            // pruefe ob contentid korrekt gesetzt wurde
            // empty == true wenn id = 0 - ist hier in Ordnung, da ids bei 1 anfangen
            if (!empty($contentid)) {
                $content = $this->contentTable->getEntryById($contentid);

                // pruefen ob zu der ID ueberhaupt content existiert
                if (!empty($content)) {
                    $form = new Application_Model_Forms_ContentForm();

                    if ($request->isGet() && $request->isPost() && $form->isValid($_POST)) {
                        // Content updaten
                        $content->text = $form->getValue('text');
                        $content->headline = $form->getValue('headline');

                        $content->save();
                    }

                    $this->_redirect('content/edit/contentid/' . $contentid);
                }

                //Form mit daten füllen
                if (!empty($content)) {
                    $contentArr = $content->toArray();
                    $form->populate($contentArr);
                }
            }
        }

        $this->view->content = $content;
        $this->view->form = $form;
    }

    public function createAction() {
        $request = $this->getRequest();

        $form = new Application_Model_Forms_ContentForm();
        $type_id = (int) $request->getParam('type_id');

        if ($request->isGet() && $request->isPost() && !empty($type_id) && $form->isValid($_POST)) {
            if (!empty($this->contentTypeTable->getContentTypeByID($type_id))) {
                $newContentid = $this->contentTable->createNewContent($form->getValue('headline'), $form->getValue('text'), $user_id, $type_id);
                $this->_redirect('content/edit/contentid/' . $newContentid);
            }
        }

        $this->view->form = $form;
        $this->view->type_id = $type_id;
    }

}
