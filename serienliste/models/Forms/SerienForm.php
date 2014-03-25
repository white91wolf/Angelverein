<?php
	abstract class Application_Model_Forms_SerienForm extends Zend_Form {
		public function init() {
			$this->setMethod('post');
		}
				
		protected function createMetaDataForForm(){
			$name = new Zend_Form_Element_Text('name', array('label' => 'Serienname', 'required' => true));
			$name->addFilter('StripTags');
			
			$description = new Zend_Form_Element_Textarea('beschreibung', array('label' => 'Beschreibung', 'required' => true));
			$description->addFilter('StripTags');

			$episodes = new Zend_Form_Element_Text('folgen', array('label' => 'Folgenanzahl', 'required' => true));
			$episodes->addValidator(new Zend_Validate_Int());
			
			$release = new Zend_Form_Element_Text('release', array('label' => 'Erscheinungsjahr', 'required' => true));
			$release->addValidator(new Zend_Validate_Date(array('format' => 'yyyy', 'locale' => 'de')));
			
			$timePs = new Zend_Form_Element_Text('dauer', array('label' => 'Min/Folge', 'required' => true));
			$timePs->addValidator(new Zend_Validate_Int());
			
			return array(
				'name' 		=> $name, 
				'description' => $description,
				'episodes' 	=> $episodes,
				'release' 	=>$release, 
				'timePs' 	=>$timePs
			);
		}
		
		protected function createImageForm($required = true){
			$file_picture = new Zend_Form_Element_File('image_form');
			$file_picture->setLabel('Ein Bild hochladen:')->setDestination(APPLICATION_PATH.'/upload');
			$file_picture->setRequired($required);
			
			// Nur 1 Datei sicherstellen
			$file_picture->addValidator('Count', false, 1);
			// Maximal 100k
			$file_picture->addValidator('Size', false, 2102400);
			// Nur JPEG, PNG, und GIFs
			$file_picture->addValidator('Extension', false, 'jpg,png,jpeg');
			
			return $file_picture;
		}
		
		public function addGenreSelect($genres){  
			
			$genreSelect = new Zend_Form_Element_MultiCheckbox('serien_multiGenre', array(
				'label' => 'Genre',
				'required' => true,
				'multiOptions' => $genres
				)
			);
			
			$genreSelect->setOrder(1);
			$this->addElement($genreSelect);
			
			return $this;
		}
	}
