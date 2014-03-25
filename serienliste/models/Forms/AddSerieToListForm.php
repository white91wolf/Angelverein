<?php
	class Application_Model_Forms_AddSerieToListForm extends Zend_Form { 
		private $baseUrl;
		
		public function __construct($baseUrl = null) {
			$this->baseUrl = $baseUrl == null ? '' : $baseUrl;
			parent::__construct();
		}
		
		public function init() {
			$this->setMethod('post');
			$element = new Zend_Form_Element_Hidden('serie_id');
			$element->setDecorators(array('ViewHelper'));
			
			$submit = new Zend_Form_Element_Submit('add_submit', 
				array('label' => 'Hinzufügen')
			);

			$emt = new ZendX_JQuery_Form_Element_AutoComplete('autocomplete_form', array(
				'label' => 'Serie hinzufügen',
				'required' => true)
			);			
			$this->addElements(array($element , $submit));
		}
		
		public function addSerieCats($seriecats = null){
		
			$selectCat = new Zend_Form_Element_Select('serie_cat', array(
				'label' => 'Welcher Liste?',
				'required' => true
				)
			);
			$selectCat->addMultiOptions($seriecats);
			$selectCat->setOrder(1);
			$this->addElement($selectCat );
		}
		
		public function setHiddenSerieIdElement($serieID = null){
			if($serieID != null){
				$this->getElement('serie_id')->setValue($serieID);
			}
		}
		
	}
