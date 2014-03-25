<?php
class CommentController extends Zend_Controller_Action
{

	protected $userTable;
	protected $serieTable;
	protected $commentTable;
	protected $currentUserID;
	protected $request;
	
	public function init()
	{
		$this->userTable = new Application_Model_DbTable_UserTable();
		$this->serieTable = new Application_Model_DbTable_SerieTable();	
		$this->currentUserID = Application_Plugin_Auth_AccessControl::getUserID();
		$this->request = $this->getRequest();	
		$this->commentTable = new Application_Model_DbTable_CommentTable();
		
		// Rendern abschalten
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true); //YOLO
	}

	public function indexAction()
	{
		$this->_redirect('index');
	}

	public function serieCommentAction() { /*comment/serie*/
		/* trage in serie table*/
		/*redirect*/
	}
	
	
	public function createAction() {
		$form = new Application_Model_Forms_CreateCommentForm();
				
		if($this->request->isPost() && $form->isValid($_POST)) {
			$serie_id = $form->getValue('serie_id');			
			$serie = $this->serieTable->getSerieById($serie_id);

			if($serie != null){
				$comment = $this->commentTable->createRow();
				/*
					Form auslesen und in comment objekt stecken
				*/
				$comment->serien_id = $serie_id;
				$comment->user_id = $this->currentUserID;
				$comment->content = $form->getValue('comment_content');
				$comment->time = date('Y-m-d H:i:s', time());
				
				$comment->save();
				
				$this->redirect('serie/show?serienid='.$serie_id);
			
			}
		} 
		
		$this->view->error = true;
	}
		
	private function getCommentForm() {
		$form = new Zend_Form();
		$form->setAction('create')->setMethod('post');
		$textarea = new Zend_Form_Element_Textarea('comment', array(
				'label' => 'Dein Kommentar:',
				'required' => true
			)
		);
		$textarea->addFilter('StripTags');
		
		return $form;
	}


}