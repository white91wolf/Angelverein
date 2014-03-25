<?php
	class Application_Model_Forms_LoginForm extends Zend_Form {
		public function init() {
			$this->setMethod('post');
			$username = new Zend_Form_Element_Text('login_user', 
			array(
				'label' => 'Benutzername', 
				'required' => true)
			);
			$username->addFilter('StripTags');
		
			
			$password = new Zend_Form_Element_Password('login_password', 
			array(
				'label' => 'Passwort', 
				'required' => true)
			);
			$password->addFilter('StripTags');
						
			$submit = new Zend_Form_Element_Submit('submit', 
			array(
				'label' => 'Login')
			);
			
			$remember = new Zend_Form_Element_Checkbox('rememberlogin', array(
				'label' => 'Erinnere dich!',
				'required' => false
				)
			);
			
			$this->addElements(
				array(
					$username, 
					$password,
					$remember,
					$submit
				)
			);
		}
		
		public function setRedirectAfterLoginField($path = null) {
			if($path != null) {
				$this->addElement(new Zend_Form_Element_Hidden('redirect_after_login', array('value' => $path)));
			}
		}
	}
