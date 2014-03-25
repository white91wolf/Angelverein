<?php
	class Application_Model_Forms_CreateCommentForm extends Zend_Form {
		public function init() {
			$this->setMethod('post');
			$hidden = new Zend_Form_Element_Hidden('serie_id', array(
				'required' => true,
				'value' => ''
				)
			);
			
			$hidden->setDecorators(array('ViewHelper'));
			$this->addElement($hidden);
			
			$this->setDecorators(
				array(
					'FormElements', 
					array(
						'HtmlTag', array('tag' => 'div', 'class' => 'zend_form')
					),
					'Form'
				)
			);
			
			$this->addElements(array(
				$this->createCommentTextarea(),
				$this->createSubmit())
			);
		}
		
		private function createCommentTextarea() {
			$textarea = new Zend_Form_Element_Textarea('comment_content',
				array(
					'required' => true,
					'label' => 'Kommentar'
				)
			);
			$textarea->addFilter('StripTags');
			$textarea->setDecorators(
				array('ViewHelper', 'Errors',
					
					array(array('data' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'element')),
					array('Label', array('tag' => 'dt')),
					array(array('row' => 'HtmlTag'), array('tag' => 'dl'))
				)
			);
			
			return $textarea;
		}
		
		private function createSubmit() {
			$submit = new Zend_Form_Element_Submit('submit', 
				array(
					'label' => 'Absenden'
				)
			);
			/*
			$submit->setDecorators(array('ViewHelper'));*/
			$submit->setDecorators(
				array('ViewHelper', 'Errors',		
					array(array('data' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'element')),
					array(array('row' => 'HtmlTag'), array('tag' => 'dl'))
				)
			);
			
			return $submit;
		}
	}
