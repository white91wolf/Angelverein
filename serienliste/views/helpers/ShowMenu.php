<?php
class Zend_View_Helper_ShowMenu extends Zend_View_Helper_Abstract{
	public function showMenu(){
		$html = '';
		$html .= '<ul class="inline mainmenue"><li><a href="'.$this->view->baseUrl().'/serien/index">Serien</a></li>
		</lu>';		
		return $html;
	}
}