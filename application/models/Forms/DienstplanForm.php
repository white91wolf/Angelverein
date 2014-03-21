<?php

class Application_Model_Forms_ContentForm extends Zend_Form {
    public function init(){
        $this->setMethod('post');

        $date = new ZendX_JQuery_Form_Element_DatePicker('date', array(
            'label' => 'Datum der Tätigkeit',
            'required' => true
        ));
        //TODO Filter nur bestimmte html elemente zulassen
        $this->addElement($date);

        //----------------------------------------------------------------------

        $text = new Zend_Form_Element_Textarea('description', array(
            'label' => 'Beschreibung/Ort der Tätigkeit',
            'required' => true
        ));
        //TODO Filter nur bestimmte html elemente zulassen
        $text->addFilter('StipTags');
        $this->addElement($text);

        //----------------------------------------------------------------------
        
        $submit = new Zend_Form_Element_Submit('submit', array(
            'label' => 'Eintragen')
        );

        $this->addElement($submit);
    }

}
