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
        $content = null;

        if ($request->isGet() || $request->isPost()) {
            $form = new Application_Model_Forms_ContentForm();
            if ($request->isGet() && $request->isPost() && $form->isValid($_POST)) {
                $newContentid = $this->contentTable->createNewContent($form->getValue('headline'), $form->getValue('text'), $user_id, $form->getValue('type_id'));
            }

            $this->_redirect('content/edit/contentid/' . $newContentid);
        }

        //Form mit daten füllen
        if (!empty($content)) {
            $contentArr = $content->toArray();
            $form->populate($contentArr);
        }

        $this->view->content = $content;
        $this->view->form = $form;
    }
}
