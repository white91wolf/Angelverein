<?php
class Zend_View_helper_ShowSerie extends Zend_View_Helper_Abstract{
	public function showSerie($users){
		$html = '';
		foreach($users as $user){
			$html .= '<h3>Nickname: '.$user->name.'</h3>';
			$html .= 'Email '.$serie->beschreibung.'<br/><hr>';
		}
		return $html;
	}
}