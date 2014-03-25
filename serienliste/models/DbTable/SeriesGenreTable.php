<?php
	class Application_Model_DbTable_SeriesGenreTable extends Zend_Db_Table_Abstract {
		protected $_name = "serie_genre";
		
		public function getGenreBySeriesID($serieID = null){
			$genre_list = null;
			
			if(!empty($serieID)) {
				$serien_select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);
				$serien_select->where('serie_id = ?', $serieID)
					->join('genre', 'genre.id = serie_genre.genre_id');
				
				$serien = $this->fetchAll($serien_select);	

				$genre_list = array();
				if(count($serien)>0){
					foreach($serien as $genre) {
						$genre_list[] = array(
							'genre_id' => $genre['genre_id'], 
							'genre_typ' => $genre['typ']
						);
					}
				}
			} 

			return $genre_list;
		}
		
		public function deleteAllFromSerieId($serie_id = null){
			$select = $this->getAdapter()->quoteInto('serie_id = ?', $serie_id);
			//$rows = $this->fetchAll($select);
			
			$this->delete($select);
			
		}
	}