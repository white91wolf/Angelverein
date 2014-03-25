<?php
	class Application_Model_DbTable_UserTable extends Zend_Db_Table_Abstract {
		protected $_name = "users";
		
		
		public function getUserByName($name = null){
			$select = $this->select()->where('nickname = ?', $name);
			$rows = $this->fetchAll($select);
			
			return $rows;
		}
		
		public function getUserByMail($mail = null) {
			$select = $this->select()->where('email = ?', $mail);
			$rows = $this->fetchAll($select);
			return $rows->current();
		}
		
		public function getUserById($id = null) {
			$user = $this->find($id);
			
			return $user->current();
		}
		
		public function getAllUser() {
			return $this->fetchAll()->toArray();
		}
		
		public function getOnlyPublicUser() {
			$select = $this->select()->where('ispublic = ?', true);
			
			return $this->fetchAll($select)->toArray();
		}
	}