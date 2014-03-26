<?php

class Application_Model_Forms_FanglisteForm extends Zend_Form {
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

        $text = new Zend_Form_Element_Textarea('text', array(
            'label' => 'text',
            'required' => true
        ));
        
        $text->addFilter(new HTMLPurifier_HTMLFilter());
        $this->addElement($text);

        //----------------------------------------------------------------------
        
        $submit = new Zend_Form_Element_Submit('submit', array(
            'label' => 'Speichern')
        );

        $this->addElement($submit);
    }
    
    public function addGewaesser($gewaesserArr) {
        $dropdown = new Zend_Form_Element_Select('gewaesser', array(
            'label' => 'Gewässer',
            'required' => true)    
        );
        
        foreach($gewaesserArr as $key => $value) {
            $dropdown->addMultiOption($value['id'],$value['name']);
        }
        
        $this->addElement($dropdown);
    }

}
