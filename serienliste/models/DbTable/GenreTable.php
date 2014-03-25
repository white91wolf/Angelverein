<?php
	class Application_Model_DbTable_GenreTable extends Zend_Db_Table_Abstract {
		protected $_name = "genre";

			//protected $_dependentTables = array('Application_Model_DbTable_SerieTable');
			
			
		public function getAllGenres() {
			$genres = array();
			$rows = $this->fetchAll();
	
			foreach($rows as $genre) {
				
				$genres[$genre->id] = $genre->typ;
			}
			
			return $genres;
		}
		
	}
	