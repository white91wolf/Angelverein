<?php

class Application_Model_Forms_ContentForm extends Zend_Form {
    public function init(){
        $this->setMethod('post');

        //TODO type_id fehlt - überlegung ob hidden oder doch änderbar was aber nicht ganz so cool wäre wenn ein newsartikel zur seite wird usw.
        
        $headline = new Zend_Form_Element_Text('headline', array(
            'label' => 'Titel',
            'required' => true
        ));
        
        $headline->addFilter('StringTrim');
        $headline->addFilter('StripNewlines');
        $headline->addFilter('StripTags');
        $this->addElement($headline);

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

}
