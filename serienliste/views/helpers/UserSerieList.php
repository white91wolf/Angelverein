<?php
class Zend_View_helper_UserSerieList extends Zend_View_Helper_Abstract{
	public function showSerien($serien = array()){
		$html = '';
		foreach($serien as $serie){
			$html .= $serie->name.'</h3><hr>';
		}
		return $html;
	}
}