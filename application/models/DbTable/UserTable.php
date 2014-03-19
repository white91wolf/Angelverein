<?php
	class Application_Model_DbTable_UserTable extends Zend_Db_Table_Abstract {
		protected $_name = "user";
		
		
		public function getUserByName($name = null){
			$select = $this->select()->where('username = ?', $name);
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
		
		public function getUserByRole($role = null) {
                    if($role != null){
			$select = $this->select()->where('rolle_id = ?', $role); //eventuell noch freigeschaltet = true
			
                    return $this->fetchAll($select)->toArray();
                    
                    }else{
                        return null;
                    }
		}
                
                public function getAllInactiveUsers() {
                    if($role != null){
			$select = $this->select()->where('freigeschaltet = ?', false);
			
                    return $this->fetchAll($select)->toArray();
                    
                    }else{
                        return null;
                    }
		}
	}