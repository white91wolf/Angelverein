<?php
	class Application_Model_DbTable_CommentTable extends Zend_Db_Table_Abstract {
		protected $_name = "comment";
	
		public function getCommentsBySerieId($serieId = null, $start = 0, $count = 10){
			if($serieId != null) {
				$select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false)->where('comment.serien_id = ?', $serieId)
					->join('users', 'users.id = comment.user_id', array('user_id' => 'users.id', 'user_nick' => 'users.nickname'))->limit($count, $start)
					->order('time DESC');
				$comments = $this->fetchAll($select);
				$commentList = null;
				if(count($comments)>0){
					$commentList = array();
					foreach($comments as $comment){
						$commentList[] = $comment->toArray();
					}
				}
			}
			return $commentList;
		}
		
		public function getCommentsRowCount($serieID = null) {
			if($serieID != null) {
				$select = $this->select()->from($this->_name, 'COUNT(id) as count')->where('serien_id = ?', $serieID);
				$set = $this->fetchRow($select);
				
				return $set['count'];
			}
			
			return null;
		}
	}
	