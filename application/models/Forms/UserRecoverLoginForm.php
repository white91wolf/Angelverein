<?php

class Application_Model_Forms_UserRecoverLoginForm extends Zend_Form {

    public function init() {
        $this->setMethod('post');
        $username = new Zend_Form_Element_Text('recover_name', array(
            'label' => 'Benutzername',
            'required' => true)
        );
        $username->addFilter('StringTrim');
        $username->addFilter('StripNewlines');
        $username->addFilter('StripTags');

        $email = new Zend_Form_Element_Text('recover_email', array(
            'label' => 'Email',
            'required' => true)
        );
        $email->addValidator(new Zend_Validate_EmailAddress());
        $email->addFilter('StripTags');
        $email->addFilter('StringTrim');
        $email->addFilter('StripNewlines');

        $submit = new Zend_Form_Element_Submit('submit', array(
            'label' => 'Absenden')
        );

        $this->addElements(
                array(
                    $username,
                    $email,
                    $submit
                )
        );
    }

}
