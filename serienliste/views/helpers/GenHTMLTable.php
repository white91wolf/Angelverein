<?php
class Zend_View_helper_GenHTMLTable extends Zend_View_Helper_Abstract{
	private $partials = array(
		'a' => 'html_anchor.phtml',
		'img' => 'html_image.phtml',
		'span' => 'html_span.phtml'
	);
	
	private function genChildsAsString($element) {
		$html = '';
		
		if(is_array($element) && isset($element['type'])) {
			if(isset($element['text']) && is_array($element['text'])) {
				$element['text'] = $this->genChildsAsString($element['text']);
			}
			
			if(($phtml = $this->getPartialHtml($element)) != null) {
				$html .= $phtml;
			}
		} elseif(is_array($element)) {
			foreach($element as $el) {
				$html .= $this->genChildsAsString($el);
			}
		} else {
			$html .= $element;
		}
		
		return $html;
	}
	
	public function genHTMLTable($arr = array(), $id = ''){
		$html = null;
		
		if(is_array($arr) && !empty($arr)) {
			$html = '';
			
			if(isset($arr['head']) && !empty($arr['head'])) {
				$tmp = '';
				$head = $arr['head'];
				$i = 0;
				
				$keysofmap = array_values($arr['map']);
				foreach($head as $th) {
					$th = $this->genChildsAsString($th); 
					
					$tmp .= '<th scope="col" class="th_'.$keysofmap[$i++].'">'.$th.'</th>';
				}
				
				$html = '<thead>
					<tr>'.$tmp.'</tr>
				</thead>
				<tbody>';
			}
			
			if(isset($arr['data']) && is_array($arr['data'])) {
				foreach($arr['data'] as $row){
					$tmp = '';
					if(is_array($row) && !empty($row)) {
						if(isset($arr['map'])) {
							foreach($arr['map'] as $index) {
								if(isset($row[$index])) {
									$tmp .= $this->getTableData($row[$index], $index);
								}
							}
						} else { // Wenn Map nicht gesetzt, mache normal weiter
							foreach($row as $k => $col) {
								$tmp .= $this->getTableData($col, $k);
							}
						}
						
						$html .= '<tr>'.$tmp.'</tr>';
					}
				}
			}
			
			if(empty($html)) {
				$html = null;
			} else {
				$html .= '</tbody>';
				if(!empty($id)) {
					$id = ' id="'.strtolower($id).'"';
				}
				
				$html = '<table'.$id.'>
					'.$html.'
				</table>';
			}
		}
		
		return $html;
	}
	
	private function getPartialHtml($col) {
		$html = null;
		
		foreach($this->partials as $type => $phtml) {
			if($col['type'] == $type) {
				$html = $this->view->partial('partial/'.$phtml, $col);
				break;
			}
		}
		
		return $html;
	}
	
	private function getTableData($col, $k = '') {
		$col = $this->genChildsAsString($col);
		
		if(!empty($k)) {
			$k = ' class="cl_'.$k.'"';
		}
		
		return '<td'.$k.'>'.$col.'</td>';
	}
}