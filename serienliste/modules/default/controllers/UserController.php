<?php
class UserController extends Zend_Controller_Action
{

	protected $userTable;
	protected $serieListTable;
	protected $serieCat;
	protected $currentUserID;
	protected $currentUserName;
	protected $currentUserRole;
	protected $imageTable;
	protected $serienTable;
	protected $request;
	protected $imgDir;
	
	public function init() {
		$this->userTable = new Application_Model_DbTable_UserTable();
		$this->serieListTable = new Application_Model_DbTable_SerieUserTable();	
		$this->imageTable = new Application_Model_DbTable_ImageTable();
		$this->serienTable = new Application_Model_DbTable_SerieTable();	
		$this->currentUserID = Application_Plugin_Auth_AccessControl::getUserID();
		$this->currentUserName = Application_Plugin_Auth_AccessControl::getUserName();
		$this->currentUserRole = Application_Plugin_Auth_AccessControl::getUserRole();
		$this->request = $this->getRequest();	
		$this->imgDir = '/upload/users/';
		$serieCatTable = new Application_Model_DbTable_SeriesCategoryTable();
		$this->serieCat = $serieCatTable->getAllCategorys();
	}

	public function indexAction() {
		if($this->currentUserID > 0) {
			$this->_redirect('user/userseries/userid/'.$this->currentUserID);
		} else {
			$this->_redirect('user/login');
		}
	}
	
	public function edituserAction(){
		$form = null;
		$userArr = array();

		if(($this->request->isGet() || $this->request->isPost()) && ($userID = $this->request->getParam('userid')) != null && 
			$this->currentUserID > 0 && $this->currentUserRole == 'admin') {
			$user = $this->userTable->getUserById($userID);
			if(count($user) > 0) {
				$form = $this->getEditForm($userID);
				
				if($this->request->isPost() && $form->isValid($_POST) && $form->image_form->receive()){
					if($form->image_form->getFileSize() != null){
						//Bild empfangen renamen und in DB schreiben	
						$picId = $this->saveImageFromForm($form);
						$user->user_image_id = $picId;
					}

					$user->nickname = $form->getValue('username');
					$user->ispublic = $form->getValue('public');
					$password = $form->getValue('password');
					if(!empty($password)){
						$user->password = sha1($password);
					}
					$user->email = $form->getValue('newEmail');
					
					$user->save();
					
				}
		
			

			}	
			
			$userArr['username'] = $user->nickname;
			$userArr['public'] = $user->ispublic == 1;
			$userArr['newEmail'] = $user->email;
			
			$form->populate($userArr);
		}	
		$this->view->form = $form;
	}
	
	public function recoverloginAction(){
		if($this->request->isPost() && ($userMail = $this->request->getParam('recover_email')) != null && ($userName = $this->request->getParam('recover_name')) != null) {
			$user = $this->userTable->getUserByMail($userMail);

			if(count($user) > 0) {
				if($user->nickname == $userName) {
					/**
					 * generiere neues zufallspasswort
					 */
					$newPw = uniqid();
					$user->password = sha1($newPw);
					$user->save();
					
					$config = array('ssl' => 'tls', 'port' => 587, 'auth' => 'login', 'username' => 'armaserielist@gmail.com', 'password' => 'H0chschule0snabrueck');
					$smtpConnection = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);
					Zend_Mail::setDefaultTransport($smtpConnection);
					$mail = new Zend_Mail();
					$mail->setBodyText('Ihr neues Passwort lautet: '.$newPw);
					$mail->setFrom('pw-recovery@serieslist.com', 'Neues Passwort!');
					$mail->addTo($userMail, $userName);
					$mail->setSubject('Neues Passwort');
					$mail->send();
				}
				
				$this->view->pwReset = true;
			} else {
				$this->view->pwReset = false;
			}
			
		} else {
			$this->view->pwReset = null;
			$this->view->form = new Application_Model_Forms_RecoverloginForm();
		}
		
	}
	
	public function userlistAction() {
		$isAdmin = false;
		$users = null;
		
		if($this->currentUserID > 0) {
			if($isAdmin = $this->currentUserRole == 'admin') {
				$users = $this->userTable->getAllUser();
			} else {
				$users = $this->userTable->getOnlyPublicUser();
			}
			
			foreach($users as $k => $user) {
				$users[$k]['user_image'] = array(
					'type' => 'img',
					'src' => '/image/index/imageid/'.$user['user_image_id'].'/imagemode/crop/imagetype/icon',
					'width' => '32',
					'height' => '32'
				);
				
				$users[$k]['created'] = $this->view->formattedDate($user['created']);
				$users[$k]['nickname_linked'] = array(
					'type' => 'a',
					'href' => '/user/userseries/userid/'.$user['id'],
					'text' => $user['nickname']
				);
				
				if($isAdmin) {
					$users[$k]['admin_controls'] = array(
						array(
							'type' => 'a',
							'class' => 'edit_user',
							'href' => 'user/edituser/userid/'.$user['id'],
							'text' => 'Bearbeiten',
							'title' => 'Benutzer bearbeiten'
						),
						array(
							'type' => 'a',
							'class' => 'del_user',
							'href' => '/user/deleteuser/userid/'.$user['id'],
							'text' => 'Löschen',
							'title' => 'Benutzer löschen',
							'onclick' => 'return confirm("Benutzer wirklich löschen?")'
						)
					);
				}
			}
		}
		
		$this->view->users = $users;
		$this->view->isAdmin = $isAdmin;
	}
	
	public function deletuserAction() {
		if($this->request->isGet() && ($userID = $this->request->getParam('userid')) != null && 
			$this->currentUserID > 0 && $this->currentUserRole == 'admin') {
			$user = $this->userTable->getUserById($userID);
			if(count($user) > 0) {
				$user->delete();
			}
		}
	}
	
	public function settolistAction() {
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		if($this->currentUserID > 0 && $this->request->isGet() && ($serienID = $this->request->getParam('serienid')) != null 
			&& ($catID = $this->request->getParam('listid')) != null) {
			$row = $this->serieListTable->getEntrieByUserAndSerie($this->currentUserID, $serienID);
			if(count($row)>0){
				$row->category_id = $catID;
				$row->save();
				
				$this->_helper->json($this->serieCat[$catID]);
			} 
		} else {
			$this->_helper->json(null);
		}
	}
		
	public function getlistpdfAction() {
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$onlyCat = null;
		
		if($this->request->isGet() && ($catID = $this->request->getParam('catid')) != null &&
			isset($this->serieCat[$catID])) {
			$onlyCat = $this->serieCat[$catID];
		}
		
		if($this->currentUserID > 0) {
			/**
			 * Setup header
			 **/
			header('Content-type: application/pdf');
			header('Content-Disposition: inline; filename="buecher.pdf"');
			
			/**
			 * Start von Text usw
			 */
			$start = 800;
			$top = $start;
			
			/**
			 * Daten holen
			 */
			$serien = $this->serieListTable->getSerienListByUserId($this->currentUserID);
			$userSeries = $this->getPreparedSeriesArray();
			
			$pdf = new Zend_Pdf();
			$style = new Zend_Pdf_Style(); // evtl für allgemeine styles? atm unnötig
			$fontSmall = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
			$fontBold = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
			
			$style->setFont($fontSmall, 14);
			
			
			$page = $this->setUpPdfPage($pdf, $style);
			$pdf->pages[] = $page;
			
			$page->drawText('Serienliste von '.$this->currentUserName, 30, $top);
			$top -= 26;
			
			// sort series by Category
			foreach($serien as $serie) {
				$userSeries[$serie->typ][] = $serie;
			}
			
			foreach($userSeries as $cat => $seriebycat) {
				if($onlyCat != null && $onlyCat != $cat) {
					continue;
				}
				
				if(is_array($seriebycat) && count($seriebycat) > 0) {
					$page->setFont($fontBold, 14);
					$page->drawText('Serien \''.$cat.'\':', 30, $top);
					$page->setFont($fontSmall, 14);
					$top -= 26;
				}
				$i = 1;
				
				foreach($seriebycat as $serie) {
					if($top > 0) {
						$page->drawText($i++.'. '.$serie->name, 60,$top);
						$top -= 22;/*
						$page->drawText('   Letzte Folge: #'.$serie->folge, 60,$top);
						$top -= 22;*/
					} else { // Seitenumbruch
						$top = $start;
						$pdf->pages[] = ($page = $this->setUpPdfPage($pdf, $style));
					}
				}
				
				$top -= 35;
			}
			
			/**
			 * Send PDF 
			 */
			echo $pdf->render();
		}
	}
	
	private function setUpPdfPage($pdf, $style, $format = 'A4') {
		$page = $pdf->newPage($format);
		$page->setStyle($style);
		
		return $page;
	}
	
	public function deletefromlistAction() {
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$del = false;
		
		if($this->currentUserID > 0 && $this->request->isGet() && ($serienID = $this->request->getParam('serienid')) != null) {
			$row = $this->serieListTable->getEntrieByUserAndSerie($this->currentUserID, $serienID);
			if(count($row)>0) {
				$row->delete();
				$del = true;
			} 
		}
		
		$this->_helper->json($del);
	}
	
	private function isInList($serieID) {
		if($this->currentUserID > 0) {
			$serieUser = $this->serieListTable->getEntrieByUserAndSerie($this->currentUserID, (int)$serieID);
			
			return (count($serieUser) > 0);
		}
		
		return false;
	}
	
	private function addtolist($serieID = null, $cat = null) {
		if($serieID != null && $cat != null && $this->currentUserID > 0) {
			if(!$this->isInList($serieID) && isset($this->serieCat[$cat])) {
				$row = $this->serieListTable->createRow();
				$row->user_id = $this->currentUserID;
				$row->serien_id = $serieID;
				$row->category_id = $cat;

				$row->save();
				return true;
			}
		} 
		
		return false;
	}

	public function addtolistajaxAction() {
		$response = false;
		if($this->request->isGet() && ($serienID = $this->request->getParam('serienid')) != null) {
			$entry = each($this->serieCat);
			$response = $this->addtolist($serienID, $entry['key']);
		}
		
		$this->_helper->json($response);
	}
	
	public function addtolistAction(){	
		$added = 'true';
		
		if($this->request->isGet() || $this->request->isPost()){
			$form = new Application_Model_Forms_AddSerieToListForm($this->view->baseUrl());
			//$form->setAction($this->view->baseUrl().'/user/addtolist');
			$form->addSerieCats($this->serieCat);
		
		
			if($form->isValid($_POST)){
				$serieID = (int)$this->request->getParam('serie_id');
				$serieC =  $this->request->getParam('serie_cat');				
				if(!$this->addtolist($serieID, $serieC)){
					$added = 'false';
				}
			}
		}
		
		if($this->currentUserID != null && $this->currentUserID > 0) {
			$redirect = 'user/userseries/userid/'.$this->currentUserID.'/added/'.$added;
		} else {
			$redirect = 'index/index';
		}
		
		$this->_redirect($redirect);
	}
	
	public function changefolgeAction() {
		if($this->currentUserID > 0 && $this->request->isGet() && ($setto = $this->request->getParam('setto')) != null 
			&& ($serie_id = $this->request->getParam('serienid')) != null) {
			$serie = $this->serienTable->getSerieById($serie_id);
			$serieUser = $this->serieListTable->getEntrieByUserAndSerie($this->currentUserID, $serie_id);

			
			if(count($serieUser) > 0 && $serie != null) {
				$folgen = $serie->folgen;
				if($setto > 0 && $serieUser->folge != $setto){ // spare DB abfrage
					$serieUser->folge = (int)$setto; 
					$serieUser->save();
				}
				
				$this->_helper->json($serieUser->folge);
			}
		} elseif($this->currentUserID <= 0) {
			$this->_helper->json(null);
		}
		
		// Bereits abschalten, falls kein User gesetzt ist
		$this->_helper->layout()->disableLayout(); 
		$this->_helper->viewRenderer->setNoRender(true); //YOLO
	}
	
	private function getPreparedSeriesArray() {
		$userSeries = array();
		
		foreach($this->serieCat as $cat_name) {
			$userSeries[$cat_name] = array();
		}
		
		return $userSeries;
	}
	
	public function userseriesAction() {
		$userSeries = null;
		$isOwner = false;
		$isPublic = false;
		$emt = null;
		$form = null;
		$userName = null;
		$userImageID = 0;
		$added = false;
		$jsFile = 'js/userseries.js'; 
		
		if($this->request->isGet() && ($userID = (int)$this->request->getParam('userid')) > 0) {
			$added = $this->request->getParam('added') == null ? false : true;
			if($userID == $this->currentUserID) {
				$isOwner = true;
			}
			
			$user = $this->userTable->find($userID);
			
			if(count($user) > 0) {
				$user = $user->current();
				$userName = $user->nickname;
				$userImageID = $user->user_image_id;
				$isPublic = $user->ispublic == 1;
				if($this->currentUserRole == 'admin'){
					$isPublic = true;
				}
			}
			
			if($isPublic || $isOwner){				
				$serien = $this->serieListTable->getSerienListByUserId($userID);
				
				// Prepare Userseries array				
				$userSeries = $this->getPreparedSeriesArray();
				
				if(count($serien) > 0){
					$i = 0;
					foreach($serien as $serie) {
						$userSeries[$serie->typ][$i] = array(
							'serie_id' => $serie->serien_id,
							'serie_link' => array(
								'type' => 'a',
								'href' => 'serie/show/serienid/'.$serie->serien_id, 
								'text' => $serie->name
							),
							'serie_image' => array(
								'type' => 'img',
								'src' => '/image/index/imageid/'.$serie->image_id
							),
							'serie_icon_image' => array(
								'type' => 'a',
								'href' => '/image/index/imageid/'.$serie->image_id,
								'noescape' => true,
								'text' => array(
									'type' => 'img',
									'src' => '/image/index/imageid/'.$serie->image_id.'/imagemode/crop/imagetype/icon',
									'width' => '32',
									'height' => '32'
								),
								'class' => 'fancybox'
							),
							'serie_folge' => array(
								array(
									'type' => 'span',
									'class' => 'count',
									'text' => $serie->folge
								),
								array(
									'type' => 'span',
									'class' => 'gesamtfolgen',
									'text' => ' / '.$serie->folgen
								)
							),
							'serie_name' => $serie->name,
							'serie_cat' => $serie->typ
						);
						
						// Besitzer spezifische funktionen
						if($isOwner) {
							$userSeries[$serie->typ][$i]['serie_folge'] = array(
								$userSeries[$serie->typ][$i]['serie_folge'],
								array(
									'type' => 'a',
									'text' => 'Erhöhen',
									'class' => 'increase',
									'href' => '#'.$serie->serien_id,
									'nobaseurl' => true
								)
							);
							$userSeries[$serie->typ][$i]['serie_cat'] = $this->view->genHTMLSelect($this->serieCat, array('class' => 'select', 'selected' => $serie->typ));
							$userSeries[$serie->typ][$i]['delete'] = array(
								'type' => 'a',
								'text' => 'Löschen',
								'class' => 'delete',
								'href' => '#'.$serie->serien_id,
								'nobaseurl' => true
							);
						}
						++$i;
					}
				}
				
				
			}		
			if($isOwner) {
				$this->view->headScript()->appendFile($this->view->baseUrl().'/'.$jsFile);
				/**
				 * Hinzufügen Form erstellen
				 */
				$form = new Application_Model_Forms_AddSerieFromShowToListForm($this->view->baseUrl());
				$form->setAction($this->view->baseUrl().'/user/addtolist');
				$form->addSerieCats($this->serieCat);
				
				if($this->request->getParam('added') == 'false') {
					$form->getElement('autocomplete_form')->addError('Die Serie befindet sich bereits in deiner Liste.');
				}
			}			
		} else {
			if(($userID = $this->currentUserID) != null) {
				$redirect = 'user/userseries/userid/'.$userID;
			} else {
				$redirect = 'index/index';
			}
			
			$this->_redirect($redirect);
		}
		
		/**
		 * erstelle array mit benutzten categorys
		 * alternativ statt typ key der cat verwenden
		 *
		 * $catids = array_flip($this->serieCat);
		 * $catids[$serie->typ] // gibt id zurückl
		 * im userseries.phtml dann auslesen per $this->serieCat[$key]
		 */
		$usedCats = array();
		if(is_array($userSeries)) {
			foreach($this->serieCat as $k => $cat) {
				if(!empty($userSeries[$cat])) {
					$usedCats[$k] = $cat;
				}
			}		
		}
		
		$this->view->userSeries = $userSeries; // Wenn null dann wurde nichts per get übergeben
		$this->view->isOwner = $isOwner; // Besitzer?
		$this->view->isPublic = $isPublic; // Für alle Öffentlich?
		$this->view->serieCat = $this->serieCat; // Array mit den Categorys (Liste, Abgeschlossen, etc)
		$this->view->usedCats = $usedCats;
		$this->view->userImageID = $userImageID;
		$this->view->addForm = $form; // Hinzufügen Form
		$this->view->userName = $userName; // Benutzername
		$this->view->added = $added; // wurde das "Serie zur Liste hinzufügen" Form benutzt?
	}
	
	public function requiredloginAction() {
		$form = $this->getLoginForm();
		
		$this->view->form = $form;
	}
	
	public function loginAction() {		
		$form = $this->getLoginForm();
		
		
		$this->view->form = $form;
	}
	
	private function getLoginForm() {
		$form = new Application_Model_Forms_LoginForm();
		
		if(($redirect = $this->request->getParam('redirect_after_login')) != null) {
			$form->setRedirectAfterLoginField($redirect);
		}
		
		if($this->request->isPost() && $form->isValid($_POST) && empty($this->currentUserID)) {
			$form->getElement('login_user')->addError('Benutzername oder Kennwort falsch!');
		}
		
		return $form;
	}
	
	public function controlAction() {
		$form = null;
		if($this->currentUserID != null && $this->currentUserID > 0) {
			$form = $this->getControlForm();
			$user = $this->userTable->getUserById($this->currentUserID);			
			/**
			 * Form erstellen und füllen bzw setzen 
			 * der checkbox (is public) und setzen der alten email
			 */
			if($this->request->isPost() && $form->isValid($_POST) && $form->image_form->receive()) {
				if(sha1($form->getValue('oldPw')) == $user->password){
					if($form->getValue('public') != null){
						$user->ispublic = $form->getValue('public') == 1;
					}
					if($form->image_form->getFileSize() != null){
						//Bild empfangen renamen und in DB schreiben	
						$picId = $this->saveImageFromForm($form);
						$user->user_image_id = $picId;
					}
					if($form->getValue('newPw') != null){
						$user->password = sha1($form->getValue('newPw'));
					}
					if($form->getValue('newEmail') != null){
						$user->email = $form->getValue('newEmail');
					}
					$user->save();
				}else{
					$form->getElement('oldPw')->addError('Falsches Password');
				}
			}
			$user = $this->userTable->getUserById($this->currentUserID);
			if($user != null && count($user) > 0){
				$form->getElement('public')->setChecked($user->ispublic == 1);
			
				$this->view->form = $form;
			}			

		} 
	}
	
	public function registerAction() {
		$form = $this->getRegisterForm();
		$registred = false;
				
		if($this->request->isPost() && $form->isValid($_POST)) {
			/**
			 * Prüfen ob User bereits existiert
			 * und ob Email nicht schon vergeben ist
			 */
	
			$name = $form->getValue('register_name');	
			$mail = $form->getValue('register_email');	
			
			$rows = $this->userTable->getUserByName($name);
	
			if(count($rows) == 0){
				$rowsByMail = $this->userTable->getUserByMail($mail);
				if(count($rowsByMail) == 0) {
					/**
					* User in DB festschreiben 
					*/
					$user = $this->userTable->createRow();
					$user->nickname = $form->getValue('register_name');
					$user->email = $mail;
					$user->password = sha1($form->getValue('register_password'));
					$user->save();
					
					$registred = true;
				} else {
					$form->getElement('register_email')->addError('Email Adresse wird bereits verwendet!');
				}
			} else {
				$form->getElement('register_name')->addError('Username bereits vergeben!');
				
			}
		} 
		
		$this->view->form = $form;
		$this->view->registred = $registred;
	}
	
	private function saveImageFromForm($form = null){
		if($form != null){
			$real_filename = $form->image_form->getFileName(null, false);
			$locationFile = $form->image_form->getFileName();
			$unique_filename = uniqid(time()).substr($real_filename, strrpos($real_filename, '.')); 
			
			$img_size = getimagesize($locationFile);
			
			$fullPath = APPLICATION_PATH.$this->imgDir.$unique_filename; 
			$filterRename = new Zend_Filter_File_Rename(
				array(
					'target' => $fullPath, 
					'overwrite' => true
				)
			); 
			$filterRename->filter($locationFile);
			$picId = 0;
			$newPic = $this->imageTable->createRow();
			$newPic->path = $this->imgDir;
			$newPic->width = $img_size[0];
			$newPic->height= $img_size[1];
			$newPic->unique_name = $unique_filename;
			$newPic->real_name = $real_filename;
			$picId = $newPic->save();
		
			return $picId;
		}
		return null;
	}
	
	private function getControlForm() {
		$form = new Application_Model_Forms_ControlForm();
		$form->setAction('');
		
		return $form;
	}
		
	private function getRegisterForm() {
		$form = new Application_Model_Forms_RegisterForm();
		$form->setAction('register');
		
		return $form;
	}	

	private function getEditForm($userID = null) {
		$form = new Application_Model_Forms_EditUserForm();
		$form->setAction('');
		
		return $form;
	}
	
	public function logoutAction() {
		Zend_Auth::getInstance()->clearIdentity();
		$this->currentUserID = 0;
		$this->_redirect('index');
	}

}