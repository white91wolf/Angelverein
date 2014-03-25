<?php
	class Application_Model_DbTable_SerieTable extends Zend_Db_Table_Abstract {
		protected $_name = "serien";
		private $orders = array(
			'name' => 'name',
			'year' => 'release',
			'epi' => 'folgen'
		);
		
		public function getSerieById($id = null){
			if($id != null){
				$series = $this->find($id);
				if(count($series)>0){
					return $series->current();
				}
			}
			return null;
		}
		

		public function getAllSeries($count = 10, $start = 0, $sort = null){
			$serien_select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false)
				->limit($count, $start);

			if($sort != null && is_array($sort) && 
				isset($sort['by']) && isset($sort['desc']) && isset($this->orders[$sort['by']])) {
				$order = $sort['desc'] ? 'DESC' : 'ASC';
				$serien_select = $serien_select->order(array($this->orders[$sort['by']].' '.$order));
				//die($serien_select);
			}
			
			/*
				->join('imagetable', 'imagetable.id = serien.image_id', 
					array(
						'image_width' => 'imagetable.width',
						'image_height' => 'imagetable.height'
					)
				);
;
			/*$serien_select->join('serie_genre', 'serie_genre.serie_id = serien.id', array())
				->join('genre', 'genre.id = serie_genre.genre_id', array('genre_id' => 'genre.id', 'genre_name' => 'genre.typ'));
			exit($serien_select);	*/
			
			$serien = $this->fetchAll($serien_select);	
			
			$serie_arr = array();
			if(count($serien)>0){
				foreach($serien as $serie){
					$serie_arr[] = $serie->toArray();
				}
			}

			return $serie_arr;
		}
		
		public function getSeriesRowCount() {
			$select = $this->select()->from($this->_name, 'COUNT(id) as count');
			$set = $this->fetchRow($select);
			
			return $set['count'];
		}

		public function getSeriesNamesLike($like) {
			$select = $this->select()
				->from($this->_name, 
					array('id AS id', 'name AS value', 'beschreibung AS description', 'image_id'))
				->where('name LIKE ?', '%'.$like.'%');
			$rows = $this->fetchAll($select);
			
			if(count($rows) > 0) {
				$rows = $rows->toArray();
			} else {
				$rows = null;
			}
			return $rows;
		}
		
		public function getRowCountsGenre($genreID = null) {
			$serien_select = $this->select()->from($this->_name, array('COUNT(*) as count'))
				->where('genre.id = ?', $genreID)
				->join('serie_genre', 'serie_genre.serie_id = '.$this->_name.'.id', array())
				->join('genre', 'genre.id = serie_genre.genre_id', array());

			$set = $this->fetchRow($serien_select);
			
			if($set == null || count($set) == 0) {
				return null;
			}

			return $set['count'];
		}
		
		public function getSeriesByGenre($genreID = null, $count = 10, $start = 0) {
			$serien_select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);
			$serien_select = $serien_select->where('genre.id = ?', $genreID)
				->join('serie_genre', 'serie_genre.serie_id = '.$this->_name.'.id', array())
				->join('genre', 'genre.id = serie_genre.genre_id', array())
				->limit($count, $start);
			
			$series = $this->fetchAll($serien_select);
			
			if($series != null && count($series) > 0) {
				$series = $series->toArray();
			}
			
			return $series;
		}
	}
		
		
	
	