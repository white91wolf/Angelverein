<?php
class Zend_View_helper_ShowSerie extends Zend_View_Helper_Abstract{
	public function showSerie($serie, $ser_genres){
		$html = '';
		
		if($serie != null){
			$role = Application_Plugin_Auth_AccessControl::getUserRole();
			if($role == 'admin'){
				
				$html .= '<a href="'.$this->view->baseUrl().'/serie/edit/serienid/'.$serie->id.'">Serie bearbeiten</a>';	
			}	
			
			$img_url = 'image/index/imageid/'.$serie->image_id;
			$img = $this->view->partial('partial/html_image.phtml', array(
					'src' => $img_url, 
					'alt' => $serie->name
			));
			$imglink = $this->view->partial('partial/html_anchor.phtml', array(
					'href' => $img_url,
					'class' => 'fancybox',
					'text' => $img,
					'noescape' => true
			));
			
			$html .= '<div class="image">';
			$html .= $imglink;
			$html .= '</div>';
			$html .= '<div class="data"><h3>'.$serie->name.'<em>Aus dem Jahr '.$this->view->formattedYear($serie->release).'</em></h3>';
			$html .= '<p>'.$serie->beschreibung.'</p>';
			$html .= '<p><strong>Folgen:</strong> '.$serie->folgen;
			$html .= ' <strong>Dauer/Folge:</strong> '.$serie->dauer.' Minuten<br/>';
			$html .='<strong>Genres:</strong>';
			
			foreach($ser_genres as $genre){
					$html .= ' '.$genre['genre_typ'];
					if(end($ser_genres) != $genre)$html .= ',';
			}
			$html .= '</p></div>';
			
			
			
			// kommentare anzeigen
			
			// kommentar post funktion
		}
		return $html;
	}
}