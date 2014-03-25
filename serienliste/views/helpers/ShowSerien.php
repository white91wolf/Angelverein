<?php
class Zend_View_helper_ShowSerien extends Zend_View_Helper_Abstract{
	public function showSerien($serien = array()){
		$html = '';
		if(is_array($serien) && count($serien)>0){
			foreach($serien as $serie){
				$html .= '<li>'.$serie->name.'</li>';
			}
		}
		
		return '<ul>'.$html.'</ul>';
	}
}