<?php

class Application_Model_Forms_UserLoginForm extends Zend_Form {

    public function init() {
        $this->setMethod('post');

        $username = new Zend_Form_Element_Text('username', array(
            'label' => 'Username',
            'required' => true
        ));
        
        $username->addFilter('StringTrim');
        $username->addFilter('StripNewlines');
        $username->addFilter('StripTags');
        $this->addElement($username);

        //----------------------------------------------------------------------

        $password = new Zend_Form_Element_Password('password', array(
            'label' => 'Password',
            'required' => true
        ));
        
        $password->addFilter('StripTags');
        $password->addFilter('StringTrim');
        $password->addFilter('StripNewlines');
        $this->addElement($password);

        //----------------------------------------------------------------------

        $submit = new Zend_Form_Element_Submit('submit', array(
            'label' => 'Login')
        );

        $this->addElement($submit);
    }

    public function setRedirectAfterLoginField($path = null) {
        if ($path != null) {
            $this->addElement(new Zend_Form_Element_Hidden('redirect_after_login', array('value' => $path)));
        }
    }

}
