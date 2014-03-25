<?php
class Zend_View_helper_GenHTMLSelect extends Zend_View_Helper_Abstract{
	private $allowedOptions = array(
		'class', 'id', 'name'
	);
	
	public function genHTMLSelect($element, $options = array()) {
		$html = '';
		$attr = '';
		$sel = 'selected';

		if(is_array($element)) {
			$tmp_html = '';
			foreach($element as $k => $el) {
				$d = '';
				if(isset($options['selected']) && $options['selected'] == $el) {
					$d = ' '.$sel; 
				}
				$tmp_html .= '<option value="'.$k.'"'.$d.'>'.$el.'</option>';				
			}	
			
			if(($attri = $this->getAttributeList($options)) != null) {
				$attr = $attri;
			}
			
			$html .= '<select'.$attr.'>'.$tmp_html.'</select>';
		}
		
		return $html;
	}
	
	
	private function getAttributeList($options) {
		if(!empty($options) && is_array($options)) {
			$i = 0; $count = count($options);
			$str = ' ';
			foreach($options as $k => $opt) {
				if(array_search($k, $this->allowedOptions) !== false) {
					$str .= $k.'="'.$opt.'"';
					
					if($i++ < $count - 1) {
						$str .= ' ';
					}
				}
			}
			
			return $str;
		}
		
		return null;
	}
	
}