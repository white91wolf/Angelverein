<?php
	class Application_Model_Forms_EditUserForm extends Zend_Form {
		public function init() {
			$this->setMethod('post');
			
			$username = new Zend_Form_Element_Text('username', array(
				'label' => 'Username',
				'required' => true
			));
			$username->addFilter('StripTags');
			$this->addElement($username);
			
			//------------------------------------------------------------
			$this->addElement($this->createImageForm());
			
			//------------------------------------------------------------
			
			$public = new Zend_Form_Element_Checkbox('public', array(
				'label' => 'Serienliste öffentlich?'
			));
			$this->addElement($public);
			//------------------------------------------------------------
			
			$newPw = new Zend_Form_Element_Password('newPw', 
				array(
					'label' => 'Neues Passwort'
				)
			);
			$newPwControl = new Zend_Form_Element_Password('newPwControl', 
				array(
					'label' => 'Neues Passwort wiederholen',
					'validators' => array(
						array(
							'identical', 
							false, 
							array(
								'token' => 'newPw'
							)
						)
					)
				)
			);
			$this->addElements(array($newPw, $newPwControl));
	
			//------------------------------------------------------------
			
			$newEmail = new Zend_Form_Element_Text('newEmail', 
			array(
				'label' => 'Email Adresse',
				'required' => true
				)
			);
			$newEmail->addValidator(new Zend_Validate_EmailAddress());
			$newEmail->addFilter('StripTags');
	
			
			$this->addElement($newEmail);
	
			//------------------------------------------------------------
			
			$submit = new Zend_Form_Element_Submit('submit', 
			array(
				'label' => 'Änderungen speichern')
			);
			
			$this->addElement($submit);


			
			
			
			/*                                    elemente        fieldsetname
			                                      |       |            |
												  V       V            V
			$this->addDisplayGroup(array('serielist', 'password'), 'login');
			*/
		}
		
		private function createImageForm(){
			$file_picture = new Zend_Form_Element_File('image_form');
			$file_picture->setLabel('Benutzerbild hochladen:')->setDestination(APPLICATION_PATH.'/upload/users');
			$file_picture->setRequired(false);
			
			// Nur 1 Datei sicherstellen
			$file_picture->addValidator('Count', false, 1);
			// Maximal 100k
			$file_picture->addValidator('Size', false, 2102400);
			// Nur JPEG, PNG, und GIFs
			$file_picture->addValidator('Extension', false, 'jpg,png,jpeg');
			
			return $file_picture;
		}
				
	
	}
