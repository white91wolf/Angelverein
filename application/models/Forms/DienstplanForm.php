<?php

class Application_Model_Forms_DienstplanForm extends Zend_Form {
    public function init(){
        $this->setMethod('post');

        $date = new ZendX_JQuery_Form_Element_DatePicker('date', array(
            'label' => 'Datum der Tätigkeit',
            'required' => true
        ));
        
        $date->addFilter('StringTrim');
        $date->addFilter('StripTags');
        $this->addElement($date);

        //----------------------------------------------------------------------

        $text = new Zend_Form_Element_Textarea('description', array(
            'label' => 'Beschreibung/Ort der Tätigkeit',
            'required' => true
        ));
       
        $text->addFilter(new HTMLPurifier_HTMLFilter());
        $this->addElement($text);

        //----------------------------------------------------------------------
        
        $hours = new Zend_Form_Element_Text('hours', array(
            'label' => 'Zeitaufwand',
            'required' => true
        ));
        //TODO only Zahlen
        $this->addElement($hours);
        
        $submit = new Zend_Form_Element_Submit('submit', array(
            'label' => 'Eintragen')
        );

        $this->addElement($submit);
    }

}
