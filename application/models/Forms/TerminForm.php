<?php

class Application_Model_Forms_TerminForm extends Zend_Form {
    public function init(){
        $this->setMethod('post');

        $headline = new Zend_Form_Element_Text('name', array(
            'label' => 'Name des Termins',
            'required' => true
        ));
        
        $headline->addFilter('StringTrim');
        $headline->addFilter('StripNewlines');
        $headline->addFilter('StripTags');
        $this->addElement($headline);

        //----------------------------------------------------------------------

        $text = new Zend_Form_Element_Textarea('description', array(
            'label' => 'Beschreibung',
            'required' => true
        ));
        
        $text->addFilter(new HTMLPurifier_HTMLFilter());
        $this->addElement($text);

        //----------------------------------------------------------------------
        
        $date = new ZendX_JQuery_Form_Element_DatePicker('date', array(
            'label' => 'Termindatum',
            'required' => true
        ));
        
        $date->addFilter('StripTags');
        $this->addElement($date);

        //----------------------------------------------------------------------
        
        $submit = new Zend_Form_Element_Submit('submit', array(
            'label' => 'Speichern')
        );

        $this->addElement($submit);
    }

}
