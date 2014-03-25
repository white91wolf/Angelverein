<?php
	class Application_Model_Forms_RegisterForm extends Zend_Form {
		public function init() {
			$this->setMethod('post');
			$username = new Zend_Form_Element_Text('register_name', 
			array(
				'label' => 'Benutzername', 
				'required' => true)
			);
			$username->addFilter('StripTags');

			
			$password = new Zend_Form_Element_Password('register_password', 
			array(
				'label' => 'Passwort', 
				'required' => true)
			);
			$password->addFilter('StripTags');
		
			
			$email = new Zend_Form_Element_Text('register_email', 
			array(
				'label' => 'Email', 
				'required' => true)
			);
			$email->addValidator(new Zend_Validate_EmailAddress());
			$email->addFilter('StripTags');
	
			
			$submit = new Zend_Form_Element_Submit('submit', 
			array(
				'label' => 'Registrieren')
			);
			
			$this->addElements(
				array(
					$username, 
					$password, 
					$email, 
					$submit
				)
			);
		}
	}