<?php

class Application_Model_Forms_ContentForm extends Zend_Form {
    public function init(){
        $this->setMethod('post');

        $headline = new Zend_Form_Element_Text('headline', array(
            'label' => 'Titel',
            'required' => true
        ));
        $headline->addFilter('StripTags');
        $this->addElement($headline);

        //----------------------------------------------------------------------

        $text = new Zend_Form_Element_Textarea('text', array(
            'label' => 'text',
            'required' => true
        ));
        //TODO Filter nur bestimmte html elemente zulassen
        $text->addFilter('StipTags');
        $this->addElement($text);

        //----------------------------------------------------------------------
        
        $submit = new Zend_Form_Element_Submit('submit', array(
            'label' => 'Speichern')
        );

        $this->addElement($submit);
    }

}
