<?php
class Zend_View_helper_ShowTermine extends Zend_View_Helper_Abstract{
	public function showTermine($termine = array()){
		$html = '';
		if(is_array($termine) && count($termine)>0){
			foreach($termine as $termin){
				$html .= '<li>'.$termin->datum.' '.$termin->uhrzeit.': '.$termin->name;
                                
                                if($termin->anmeldung){
                                    //TODO super button um termin zu bestätigen oder ablehnen
                                }
                                
                                $html .= '</li>';
			}
		}
		
		return '<ul>'.$html.'</ul>';
	}
}