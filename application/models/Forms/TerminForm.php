<?php

class Application_Model_Forms_TerminForm extends Zend_Form {
    public function init(){
        $this->setMethod('post');

        $headline = new Zend_Form_Element_Text('name', array(
            'label' => 'Name des Termins',
            'required' => true
        ));
        $headline->addFilter('StripTags');
        $this->addElement($headline);

        //----------------------------------------------------------------------

        $text = new Zend_Form_Element_Textarea('description', array(
            'label' => 'Beschreibung',
            'required' => true
        ));
        //TODO Filter nur bestimmte html elemente zulassen
        $text->addFilter('StipTags');
        $this->addElement($text);

        //----------------------------------------------------------------------
        
        $date = new ZendX_JQuery_Form_Element_DatePicker('date', array(
            'label' => 'Termindatum',
            'required' => true
        ));
        //TODO Filter nur bestimmte html elemente zulassen
        $this->addElement($date);

        //----------------------------------------------------------------------
        
        $submit = new Zend_Form_Element_Submit('submit', array(
            'label' => 'Speichern')
        );

        $this->addElement($submit);
    }

}
