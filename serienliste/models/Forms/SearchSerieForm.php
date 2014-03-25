<?php
	class Application_Model_Forms_SearchSerieForm extends Zend_Form { 
		private $baseUrl;
		
		public function __construct($baseUrl = null) {
			$this->baseUrl = $baseUrl == null ? '' : $baseUrl;
			parent::__construct();
		}
		
		public function init() {
			$this->setMethod('post');
			
			$submit = new Zend_Form_Element_Submit('add_submit', 
				array('label' => 'Suchen')
			);

			$emt = new ZendX_JQuery_Form_Element_AutoComplete('autocomplete_search_form', array(
				'label' => 'Serie suchen',
				'required' => true)
			);

			$emt->setJQueryParam('source', $this->baseUrl.'/serie/searchseries');
			$emt->setJQueryParams(array('select' => new Zend_Json_Expr(
				'function(event, ui) { 
					$("#autocomplete_search_form form").attr("action", baseUrl + "/serie/show/serienid/" + ui.item.id);
					$("#autocomplete_search_form").val(ui.item.value);
					return false;
				}'), 'minLength' => '2'));
			
			$this->addElements(array($emt , $submit));
		}
		
	}
