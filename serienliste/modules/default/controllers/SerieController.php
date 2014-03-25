<?php
class SerieController extends Zend_Controller_Action{

	protected $serieTable;
	protected $genreTable;
	protected $serieGenreTable;
	protected $imageTable;
	protected $request;
	protected $genreList;
	protected $imgDir;
	protected $commentTable;
	protected $serieUserTable;
	protected $sereCatTable;
	protected $serieCat;
		
	public function init(){
		$this->serieTable = new Application_Model_DbTable_SerieTable();
		$this->serieUserTable = new Application_Model_DbTable_SerieUserTable();
		$this->genreTable = new Application_Model_DbTable_GenreTable();
		$this->genreList = $this->genreTable->getAllGenres();
		$this->imageTable = new Application_Model_DbTable_ImageTable();
		$this->serieGenreTable = new Application_Model_DbTable_SeriesGenreTable();
		$this->commentTable = new Application_Model_DbTable_CommentTable();
		$serieCatTable = new Application_Model_DbTable_SeriesCategoryTable();
		$this->serieCat = $serieCatTable->getAllCategorys();
		
		$this->imgDir = '/upload/';
		$this->request = $this->getRequest();
		
	}

	public function searchseriesAction() {
		if($this->request->isGet() && ($search = $this->request->getParam('term')) != null 
			&& strlen($search) >= 2) {
			$result = $this->serieTable->getSeriesNamesLike($search);
			
			foreach($result as $k => $row) {
				$result[$k]['label'] = $result[$k]['value'];
				$result[$k]['icon'] = $this->view->baseUrl().'/image/index/imageid/'.$result[$k]['image_id'].'/imagemode/crop/imagetype/thumb';
			}

			$this->_helper->json(array_values($result));
		} else {
			$this->_helper->layout()->disableLayout(); 
			$this->_helper->viewRenderer->setNoRender(true); //YOLO
		}
	}
	
	//Zeigt einzelne Serie mit allen Daten an
	public function showAction(){
		$serie = null;
		$commentForm = null;
		$addToListForm = null;
		$countComments = 5;
		$paginator = null;
		$comments = null;
		
		if($this->request->isGet() && ($serieID = (int)$this->request->getParam('serienid')) > 0) {
		
			$serie = $this->serieTable->getSerieById($serieID);
			$arrPaginator = $this->getPaginator($countComments, $this->commentTable->getCommentsRowCount($serieID));
			$comments = $this->commentTable->getCommentsBySerieId($serieID, $arrPaginator['offset'], $countComments);
			$ser_genres  = $this->serieGenreTable->getGenreBySeriesID($serieID);
			
			if(count($serie) > 0) {
				$this->view->serie = $serie;
				$paginator = $arrPaginator['paginator'];
				
			}
		} else {
			$this->_redirect('serie/index');
		}
		
		$user_role = Application_Plugin_Auth_AccessControl::getUserRole();
		if($user_role == 'user' || $user_role == 'admin') {
			$addToListForm = new Application_Model_Forms_AddSerieToListForm();
			$addToListForm->addSerieCats($this->serieCat);
			$addToListForm->setHiddenSerieIdElement($serieID);
			$addToListForm->setAction($this->view->baseUrl().'/user/addtolist');
			
			$commentForm = new Application_Model_Forms_CreateCommentForm();
			$commentForm->getElement('serie_id')->setValue($serieID);
			$commentForm->setAction($this->view->baseUrl().'/comment/create');
		}
		
		$this->view->comments = $comments;
		$this->view->paginator = $paginator;
		$this->view->serie = $serie;
		$this->view->ser_genres = $ser_genres;
		$this->view->commentForm = $commentForm;
		$this->view->addToListForm = $addToListForm;
		
	}
	
	private function getPaginator($itemsPerPage, $itemsCount) {
		if(!$this->request->isGet() || ($page = $this->request->getParam('page')) == null) {
			$page = 1;
		}
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Null($itemsCount));
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($itemsPerPage);
		
		$offset = ($page - 1) * $itemsPerPage;
		
		if($offset >= $itemsCount) {
			$offset = $itemsCount - ($itemsCount % $itemsPerPage); 
		}
		
		
		return array('paginator' => $paginator, 'offset' => $offset);
	}
	
	public function indexAction(){
		$jsFile = 'js/serieindex.js'; 
		$this->view->headScript()->appendFile($this->view->baseUrl().'/'.$jsFile);
		
		$countItems = 10;
		$countSeries = $this->serieTable->getSeriesRowCount();
		/**
		 * Create and Setup Paginator
		 */
		
		
		$arrPaginator = $this->getPaginator($countItems, $this->serieTable->getSeriesRowCount());
		$sort = array();
		if(($sort['by'] = $this->request->getParam('sortby')) != null) {
			$sort['desc'] = $this->request->getParam('sortorder');
			$sort['desc'] = ($sort['desc'] == 'desc') ? true : false;
			
			$series = $this->serieTable->getAllSeries($countItems, $arrPaginator['offset'], $sort);
		} elseif(($genreID = $this->request->getParam('genre')) != null) {
			$arrPaginator = $this->getPaginator($countItems, $this->serieTable->getRowCountsGenre($genreID));
			$series = $this->serieTable->getSeriesByGenre($genreID, $countItems, $arrPaginator['offset']);
		} else {
			$series = $this->serieTable->getAllSeries($countItems, $arrPaginator['offset']);
		}
		
		if($arrPaginator != null) {
			$paginator = $arrPaginator['paginator'];
		}
		
		/**
		 * Pass Genres by Series
		 */
		if(count($series) > 0) {
			foreach($series as $k => $serie) {
				$genre =  $this->serieGenreTable->getGenreBySeriesID($serie['id']);
				$genres = '';
				$i = 0;
				$count = count($genre);
				
				foreach($genre as $g) {
					$genres .= $g['genre_typ'];
					
					if($i++ < $count - 1) {
						$genres .= ', ';
					} 
				}
 
				$series[$k]['beschreibung'] = substr($serie['beschreibung'], 0, 100).'...';
				$series[$k]['genres'] = $genres; 
				$series[$k]['release'] = $this->view->formattedYear($series[$k]['release']);
				
				$series[$k]['serie_link'] = array(
					'type' => 'a',
					'href' => 'serie/show/serienid/'.$serie['id'], 
					'text' => $serie['name']
				);
				
				$series[$k]['serie_image'] = array(
					'type' => 'img',
					'src' => 'image/index/imageid/'.$serie['image_id'].'/imagetype/icon/imagemode/crop',
					'alt' => $serie['name'],
					'width' => '32',
					'height' => '32'
				);
				
				$series[$k]['serie_icon_image'] = array(
					'type' => 'a',
					'href' => '/image/index/imageid/'.$serie['image_id'],
					'noescape' => true,
					'text' => array(
						'type' => 'img',
						'src' => '/image/index/imageid/'.$serie['image_id'].'/imagemode/crop/imagetype/icon',
						'width' => '32',
						'alt' => $serie['name'],
						'height' => '32'
					),
					'class' => 'fancybox'
				);
			}
		} else {
			$series = null;
		}
		
		if(!$this->request->isGet() || ($selectedGenre = $this->request->getParam('genre')) == null) {
			$selectedGenre = null;
		}
		
		$this->view->selectedGenre = $selectedGenre;
		$this->view->series = $series;
		$this->view->countItems = $countItems;
		$this->view->paginator = $paginator;
		$this->view->sortOrder = $sort;
		$this->view->genreList = $this->genreList;
	}
	
	
	public function editAction(){		
		$request = $this->getRequest();
		$serie = null;
		$currentSerieImageId = 0;
		$form = null;
		
		if($request->isGet() || $request->isPost()){
			$serieID = $request->getParam('serienid');

			if($serieID > 0){
				$serie = $this->serieTable->getSerieById($serieID);	
				$form = $this->getEditForm($serie);
				$ser_genres  = $this->serieGenreTable->getGenreBySeriesID($serieID);
				$currentSerieImageId = $serie->image_id;
				$genreArr = Array();
				
				foreach($ser_genres as $genre){
					$genreArr[] = (int)$genre['genre_id'];
				}
			}
			
		}
		
		if($this->request->isPost()&& $form->isValid($_POST) && $form->image_form->receive()){
			$serie = $this->getSerieFromForm($form, $serie);
			$serie_check = $this->serieTable->getSeriesNamesLike($form->getValue('name'));
			
			if($serie_check == null || $serie_check[0]['id'] == $serie->id){
				if($form->image_form->getFileSize() != null){
					//Bild empfangen renamen und in DB schreiben	
					$picId = $this->saveImageFromForm($form);
					$serie->image_id = $picId;
				}
				$serie->save();

				$this->serieGenreTable->deleteAllFromSerieId($serieID); 
				$this->saveGenresFromFormToSerieId($form, $serieID);
			} else {
				$form->getElement('name')->addError('Serie mit dem Namen bereits vorhanden!');
			}
		
			$this->_redirect('serie/edit/serienid/'.$serieID);
		}
		
		//Form daten eintragen
		if($serie != null){
			$serieArr = $serie->toArray();
			$serieArr['release'] = $this->view->formattedYear($serieArr['release']);
			$serieArr['serien_multiGenre'] = $genreArr;
			$form->populate($serieArr);		
		}
		
		$this->view->currentSerieImageId = $currentSerieImageId;
		$this->view->form = $form;			
	}
	
	public function createAction(){		
		$form = $this->getCreateForm();
		$createdid = 0;
		
		if($this->request->isPost() && $form->isValid($_POST)
			&& $form->image_form->receive()) {
			
			$serie_check = $this->serieTable->getSeriesNamesLike($form->getValue('name'));
			
			if($serie_check == null){
				//Bild empfangen renamen und in DB schreiben		
				$picId = $this->saveImageFromForm($form);
				
				$newSerie = $this->serieTable->createRow();
				$newSerie = $this->getSerieFromForm($form, $newSerie);
				if($newSerie != null){
					$newSerie->image_id = $picId;
				
					$serienId = $newSerie->save();
					
					
					$this->saveGenresFromFormToSerieId($form, $serienId);
					 
					// auf neu erstellte Serie verweisen
					$flashMessenger = $this->_helper->getHelper('FlashMessenger');
					$flashMessenger->addMessage('Serie wurde angelegt.');
					$this->_redirect('serie/show/serienid/'.$serienId);
				}
			}else{
				//Serie mit diesem Namen schon vorhanden
				$form->getElement('name')->addError('Serie mit dem Namen bereits vorhanden!');
			}
		} 

		$this->view->form = $form;
	}
	

	
	
	private function saveGenresFromFormToSerieId($form = null, $serie_id = null){
		if($form != null && $serie_id != null){
			$genres = $form->getValue('serien_multiGenre');
			foreach($genres as $genre){
				$newSerieGenre = $this->serieGenreTable->createRow();
				$newSerieGenre->genre_id = (int)$genre;
				$newSerieGenre->serie_id = $serie_id;
				$newSerieGenre->save();
			}
		}
	}
	
	private function getSerieFromForm($form = null, $serie = null){
		if($form != null && $serie != null){
			$serie->name = $form->getValue('name');
			$serie->beschreibung = $form->getValue('beschreibung');
			$serie->folgen = $form->getValue('folgen');
			$serie->release = $form->getValue('release').'-01-01';
			$serie->dauer = $form->getValue('dauer');
			
			return $serie;
		}
		return null;
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
	
	private function getCreateForm() {
		$form = new Application_Model_Forms_CreateSerieForm();
		$form->addGenreSelect($this->genreList);
		$form->setAction('create');

		return $form;
	}
	
	private function getEditForm($serie) {
		$form = new Application_Model_Forms_EditSerieForm();
		$form->addGenreSelect($this->genreList);
		$form->setAction($this->view->baseUrl().'/serie/edit/serienid/'.$serie->id)->setMethod('post');
		
		return $form;
	}
	

	

}