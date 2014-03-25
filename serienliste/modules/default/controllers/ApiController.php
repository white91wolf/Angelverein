<?php
class ApiController extends Zend_Controller_Action {
	protected $userTable;
	protected $serieTable;
	protected $userSerieTable;
	protected $genreTable;
	protected $currentUserID;
	protected $serieCat;
	protected $request;
	
	public function init()
	{
		$this->userTable = new Application_Model_DbTable_UserTable();
		$this->serieTable = new Application_Model_DbTable_SerieTable();	
		$this->userSerieTable = new Application_Model_DbTable_SerieUserTable();	
		$serieCatTable = new Application_Model_DbTable_SeriesCategoryTable();
		$this->serieCat = $serieCatTable->getAllCategorys();
		$this->genreTable = new Application_Model_DbTable_GenreTable();
		
		$this->request = $this->getRequest();	
		
		// Rendern abschalten
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true); 
	}
	
	/**
	 * Gibt die Listen + Serien eines bestimmten Benutzers
	 * zurück, insofern diese public ist
	 */
	public function userseriesAction() {
		if($this->request->isGet() && ($userID = (int)$this->request->getParam('userid')) > 0) {
			$user = $this->userTable->find($userID);
			
			if(count($user) > 0) {
				$user = $user->current();
				$userName = $user->nickname;
				$isPublic = $user->ispublic == 1;	
				
				if($isPublic) {
					// User series Array vorbereiten
					// extra function in UserController
					// evtl. auslagern zur stärkeren verzahnung
					$userSeries = array();
		
					foreach($this->serieCat as $cat_name) {
						$userSeries[$cat_name] = array();
					}
					
					// Serien abrufen
					$serien = $this->userSerieTable->getSerienListByUserId($userID);
					$i = 0;
					foreach($serien as $k => $serie) {
						$userSeries[$serie->typ][$i++] = array(
							'serie_id' => $serie->serien_id,
							'serie_image' => $this->view->baseUrl().'/image/index/imageid/'.$serie->image_id.'/imagemode/crop/imagetype/icon'
						);
					}
					
					$this->_helper->json($userSeries);
				}
			}
		}
	}
	
	/**
	 * Gibt alle Serien zurück
	 * Todo: evtl. nur eine gewisse Anzahl erlauben?
	 */
	public function serieslistAction() {
		$page = 0;
		$count = 10;
		if($this->request->isGet() && ($getpage = $this->request->getParam('page')) != null) {
			$page = (int)$getpage;
		}
		
		$series = $this->serieTable->getAllSeries($count, $page * $count);
		
		$this->_helper->json($series);
	}
	
	/**
	 * Zeigt eine einzelene Serie an, bzw. die Daten
	 */
	public function serieAction() {
		$serie = null;
		if($this->request->isGet() && ($serieID = $this->request->getParam('serienid')) != null){
			$serieID = (int) $serieID;
			if($serieID > 0){
				$serie = $this->serieTable->getSerieById($serieID);
			}
		}
		$this->_helper->json($serie);
	}
	
	/**
	 * Gibt alle verfügbaren Genres zurück
	 */
	public function genresAction() {
		$this->_helper->json($this->genreTable->getAllGenres());
	}
}