<?php

class Application_Model_Forms_UserLoginForm extends Zend_Form {

    public function init() {
        $this->setMethod('post');

        $headline = new Zend_Form_Element_Text('username', array(
            'label' => 'Username',
            'required' => true
        ));
        $headline->addFilter('StripTags');
        $this->addElement($headline);

        //----------------------------------------------------------------------

        $text = new Zend_Form_Element_Password('password', array(
            'label' => 'Password',
            'required' => true
        ));
        //TODO Filter nur bestimmte html elemente zulassen
        $text->addFilter('StipTags');
        $this->addElement($text);

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
