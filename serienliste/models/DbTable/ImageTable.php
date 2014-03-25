<?php
	class Application_Model_DbTable_ImageTable extends Zend_Db_Table_Abstract {
		protected $_name = "imagetable";
		/*
		array(4) {
		  ["id"]=>
		  string(1) "1"
		  ["path"]=>
		  string(8) "/upload/"
		  ["unique_name"]=>
		  string(27) "1378576817522b69b16456d.jpg"
		  ["real_name"]=>
		  string(55) "amazing-animal-art-cute-hotography-Favim.com-273493.jpg"
		}*/
		public function getImagePathByID($id = null){
			$rows = $this->find($id);
			$path = null;
			
			if(count($rows) > 0) {
				$row = $rows->current();
				
				$path = APPLICATION_PATH.$row->path.$row->unique_name;
			}
			
			return $path;
		}	
	}
	