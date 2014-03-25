<?php
	class Application_Model_Forms_AddSerieFromShowToListForm extends Application_Model_Forms_AddSerieToListForm { 
		private $baseUrl;
		
		public function __construct($baseUrl = null) {
			$this->baseUrl = $baseUrl == null ? '' : $baseUrl;
			parent::__construct();
		}
		
		public function init() {
			parent::init();
			$emt = new ZendX_JQuery_Form_Element_AutoComplete('autocomplete_form', array(
				'label' => 'Serie hinzufügen',
				'required' => true)
			);

			$emt->setJQueryParam('source', $this->baseUrl.'/serie/searchseries');
			$emt->setJQueryParams(array(
				'select' => new Zend_Json_Expr(
					'function(event, ui) { 
						$("#serie_id").attr("value", ui.item.id); 
						$("#selected_serie_img").attr("src", ui.item.icon);
						$("#selected_serie_img").show();
						$("#autocomplete_form").val(ui.item.value);
						$("#serie_description").append(ui.item.description);
						return false;
					}'
				), 
				'minLength' => '2'/*,
				'search' => new Zend_Json_Expr(
					'function() { 
						$(this).addClass("working");
					}'
				),
				'open' => new Zend_Json_Expr(
					'function() { 
						$(this).removeClass("working");
					}'
				)*/
			));
			$emt->setOrder(0);
			
			$this->addElement($emt);
		}

		
	}
