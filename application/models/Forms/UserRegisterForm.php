<?php

class Application_Model_Forms_UserRegisterForm extends Zend_Form {
    public function init(){
        $this->setMethod('post');

        $username = new Zend_Form_Element_Text('username', array(
            'label' => 'Username',
            'required' => true
        ));
        $headline->addFilter('StripTags');
        $this->addElement($username);

        //----------------------------------------------------------------------
        
        $vorname = new Zend_Form_Element_Text('vorname', array(
            'label' => 'Vorname',
            'required' => true
        ));
        $headline->addFilter('StripTags');
        $this->addElement($vorname);

        //----------------------------------------------------------------------
        
        $nachname = new Zend_Form_Element_Text('nachname', array(
            'label' => 'Nachname',
            'required' => true
        ));
        $headline->addFilter('StripTags');
        $this->addElement($nachname);

        //----------------------------------------------------------------------

        $text = new Zend_Form_Element_Password('password', array(
            'label' => 'Password',
            'required' => true
        ));
        //TODO Filter nur bestimmte html elemente zulassen
        $text->addFilter('StipTags');
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
        $this->addElement($email);
        
        //----------------------------------------------------------------------
        
        $submit = new Zend_Form_Element_Submit('submit', array(
            'label' => 'Registrieren')
        );

        $this->addElement($submit);
    }

}
