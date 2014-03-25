<?php
	class Application_Model_DbTable_SerieUserTable extends Zend_Db_Table_Abstract {
		protected $_name = "serie_user";
										
		public function getSerienListByUserId($userID = null) {
			$serien = null;
			
			if($userID != null){
				$serien_select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);
					
				$serien_select->where('user_id = ?', $userID)
					->join('serien', 'serien.id = serie_user.serien_id')
					->join('series_category', 'serie_user.category_id = series_category.id');
				$serien = $this->fetchAll($serien_select);
			}
		
			return $serien;
		}
		
		public function getEntrieByUserAndSerie($userID, $serieID){
			$row = null;
			if(!empty($userID) && !empty($serieID) && $userID > 0 && $serieID > 0) {
				$select = $this->select()->where('user_id = ?', $userID)->where('serien_id = ?', $serieID);

				$row = $this->fetchAll($select)->current();
			}
			
			return $row;
		}
	}