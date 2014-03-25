<?php
	class Application_Model_Forms_CreateSerieForm extends Application_Model_Forms_SerienForm { 
		public function init() {
			parent::init();
			
			$metaElements = $this->createMetaDataForForm();
			$imageElement = $this->createImageForm(true);
			
			$submit = new Zend_Form_Element_Submit('submit', array('label' => 'Eintragen'));
			
			$this->addElements(
				array(
					$metaElements['name'],
					$imageElement,
					$metaElements['description'],
					$metaElements['episodes'],
					$metaElements['release'],
					$metaElements['timePs'],
					$submit
				)
			);
		}
		
		
	}
