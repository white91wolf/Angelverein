<?php
class Zend_View_helper_ShowComments extends Zend_View_Helper_Abstract{
	public function showComments($comments){
	
		$html = '';
		if($comments != null){
			$role = Application_Plugin_Auth_AccessControl::getUserRole();

			foreach($comments as $comment){
				$html .= '<div class="comment">';
				$html .= '<h3>'.$comment['user_nick'];
				$html .= '<em>'.$this->view->formattedDate($comment['time']).'</em></h3>';
				$html .= '<div class="comment_content"><p>'.nl2br($comment['content']).'</p></div></div><hr>';
			}
		}
		
		return $html;
	}
}