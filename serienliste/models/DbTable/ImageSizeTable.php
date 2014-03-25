<?php
	class Application_Model_DbTable_ImageSizeTable extends Zend_Db_Table_Abstract {
		protected $_name = "image_sizes";
										
		public function getImageSizeByType($type = null) {
			$image = null;
			$sizeArr = null;
			if($type != null){
				$image_select = $this->select();
					
				$image_select->where('image_sizes.typ= ?', $type);
				$images = $this->fetchAll($image_select);
				
				if(count($images) > 0) {
					$image = $images->current();
					$image = $image->toArray();
					$sizeArr = array('width' => $image['width'], 'height' => $image['height']);

				}
			}
			
			return $sizeArr;
		}
		
		public function getAllTypes() {
			$types = null;
			
			$type_select = $this->select();
			$rows = $this->fetchAll($type_select);
			
			if(count($rows) > 0) {
				$types = array();
				
				foreach($rows as $type) {
					$types[] = $type->toArray();
				}
			}
		
			return $types;
		}
	}