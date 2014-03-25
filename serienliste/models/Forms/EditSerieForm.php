<?php
	class Application_Model_Forms_EditSerieForm extends Application_Model_Forms_SerienForm { 
		public function init() {
			parent::init();
			
			$metaElements = $this->createMetaDataForForm();
			$imageElement = $this->createImageForm(false);
			
			$submit = new Zend_Form_Element_Submit('submit', array('label' => 'Editieren'));
			
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