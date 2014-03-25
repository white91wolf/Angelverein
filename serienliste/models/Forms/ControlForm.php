<?php
	class Application_Model_Forms_ControlForm extends Zend_Form {
		public function init() {
			$this->setMethod('post');
			//$this->setDecorators(array(array('ViewScript', array('viewScript' => 'partial/control-form.phtml'))));
			$this->setDecorators(
				array(
					'FormElements', 
					array(
						'HtmlTag', array('tag' => 'div', 'class' => 'zend_form')
					),
					'Form'
				)
			);
			$public = new Zend_Form_Element_Checkbox('public', array(
				'label' => 'Serienliste öffentlich?'
			));
			$this->addElement($public);
			$this->addDisplayGroup(array('public'), 'Serienliste', array(
				'legend' => 'Serienliste'
				)
			);
			//------------------------------------------------------------
			$this->addElement($this->createImageForm());
			$this->addDisplayGroup(array('image_form'), 'Benutzerbild', array(
				'legend' => 'Benutzerbild'
				)
			);
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
			$this->addDisplayGroup(array('newPw', 'newPwControl'), 'Passwort', array(
				'legend' => 'Passwort'
				)
			);
			//------------------------------------------------------------
			
			$newEmail = new Zend_Form_Element_Text('newEmail', 
			array(
				'label' => 'Email Adresse ändern' 
				)
			);
			$newEmail->addValidator(new Zend_Validate_EmailAddress());
			$newEmail->addFilter('StripTags');
	
			
			$this->addElement($newEmail);
			$this->addDisplayGroup(array('newEmail'), 'Email', array(
				'legend' => 'Email'
			));
			//------------------------------------------------------------
					
			$oldPw = new Zend_Form_Element_Password('oldPw',
				array(
					'label' => 'Passwort zur Bestätigung',
					'required' => true
				)
			);
			
			$oldPw->setDecorators(
				array('ViewHelper', 'Errors',
					
					array(array('data' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'element')),
					array('Label', array('tag' => 'dt')),
					array(array('row' => 'HtmlTag'), array('tag' => 'dl'))
				)
			);
			
			$submit = new Zend_Form_Element_Submit('submit', 
			array(
				'label' => 'Änderungen speichern')
			);
			
			$submit->setDecorators(
				array('ViewHelper', 
					array(array('data' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'element')),
					array('Label', array('tag' => 'dt')),
					array(array('row' => 'HtmlTag'), array('tag' => 'dl'))
				)
			);
			
			$g = array('Serienliste', 'Benutzerbild', 'Passwort', 'Email');
			foreach($g as $dgn) {
				$group = $this->getDisplayGroup ($dgn);
				$group->removeDecorator ('DtDdWrapper');
				
			}
			
			$this->addElements(array($oldPw, $submit));


			
			
			
			/*                                    elemente        fieldsetname
			                                      |       |            |
												  V       V            V
			$this->addDisplayGroup(array('serielist', 'password'), 'login');
			*/
		}
		
		private function createImageForm(){
			$file_picture = new Zend_Form_Element_File('image_form');
			$file_picture->setLabel('Benutzerbild hochladen')->setDestination(APPLICATION_PATH.'/upload/users');
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
