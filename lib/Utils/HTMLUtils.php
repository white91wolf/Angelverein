<?php
	class Series_HTMLUtils {
		private $refObj;
		
		public function __construct($refObj = null) {
			$this->refObj = $refObj;
		}
		
		public function attributeList($htmlAttr) {
			$attributeList = '';
			$c = count($htmlAttr);
			for($i = 0; $i < $c; $i++) {
				$attr = $htmlAttr[$i];
				
				if(isset($this->refObj->$attr)) {
					$attributeList .= $attr.'="'.$this->refObj->escape($this->refObj->$attr).'"';
					
					if($i < $c - 1) {
						$attributeList .= ' ';
					}
				}
			}

			return $attributeList;
		}
	}