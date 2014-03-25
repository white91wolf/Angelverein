<?php
	class Application_Model_DbTable_SeriesCategoryTable extends Zend_Db_Table_Abstract {
		protected $_name = "series_category";
		
		public function getAllCategorys() {
			$serieCat = array();
			$rows = $this->fetchAll();
			
			foreach($rows as $cat) {
				$serieCat[$cat->id] = $cat->typ;
			}
			
			return $serieCat;
		}
	}