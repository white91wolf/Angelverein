<?php

class Application_Model_Forms_UserRecoverPasswordForm extends Zend_Form {

    public function init() {
        $this->setMethod('post');

        //TODO type_id fehlt - überlegung ob hidden oder doch änderbar was aber nicht ganz so cool wäre wenn ein newsartikel zur seite wird usw.

        $email = new Zend_Form_Element_Text('email', array(
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
            'label' => 'Absenden')
        );

        $this->addElement($submit);
    }

}
