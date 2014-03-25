<?php

class Application_Model_Forms_UserRegisterForm extends Zend_Form {
    public function init(){
        $this->setMethod('post');

        $username = new Zend_Form_Element_Text('username', array(
            'label' => 'Username',
            'required' => true
        ));
        $username->addFilter('StripTags');
        $username->addFilter('StringTrim');
        $username->addFilter('StripNewlines');
        $this->addElement($username);

        //----------------------------------------------------------------------
        
        $vorname = new Zend_Form_Element_Text('vorname', array(
            'label' => 'Vorname',
            'required' => true
        ));
        $vorname->addFilter('StripTags');
        $username->addFilter('StringTrim');
        $username->addFilter('StripNewlines');
        $this->addElement($vorname);

        //----------------------------------------------------------------------
        
        $nachname = new Zend_Form_Element_Text('nachname', array(
            'label' => 'Nachname',
            'required' => true
        ));
        $nachname->addFilter('StripTags');
        $nachname->addFilter('StringTrim');
        $nachname->addFilter('StripNewlines');
        $this->addElement($nachname);

        //----------------------------------------------------------------------

        $text = new Zend_Form_Element_Password('password', array(
            'label' => 'Password',
            'required' => true
        ));
        //TODO Filter nur bestimmte html elemente zulassen
        $text->addFilter('StipTags');
        $text->addFilter('StringTrim');
        $text->addFilter('StripNewlines');
        $this->addElement($text);

        //----------------------------------------------------------------------
        
        $email = new Zend_Form_Element_Text('register_email', 
            array(
                    'label' => 'Email', 
                    'required' => true
                )
        );
        $email->addValidator(new Zend_Validate_EmailAddress());
        $email->addFilter('StripTags');
        $email->addFilter('StringTrim');
        $email->addFilter('StripNewlines');
        $this->addElement($email);
        
        //----------------------------------------------------------------------
        
        $submit = new Zend_Form_Element_Submit('submit', array(
            'label' => 'Registrieren')
        );

        $this->addElement($submit);
    }

}
