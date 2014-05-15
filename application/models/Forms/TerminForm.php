<?php

class Application_Model_Forms_TerminForm extends Zend_Form {

    public function init() {
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

        /*$date = new ZendX_JQuery_Form_Element_DatePicker('date', array(
            'label' => 'Termindatum',
            'jQueryParams' => array('dateFormat' => 'dd.mm.yy'),
            'required' => true
        ));

        $date->addFilter('StripTags');
        $this->addElement($date);*/
        
        $datetime = new Zend_Form_Element_Text('date_timepicker', array(
                'label' => 'Zeitpunkt des Termins',
                'required' => true
                )
                );
        $this->addElement($datetime);
        //TODO Timevalidator http://stackoverflow.com/questions/2185608/how-to-create-a-datetime-validator-in-a-zend-framework-form  http://framework.zend.com/manual/1.11/en/zend.validate.set.html (ganz unten)

        //TODO Timepicker vlt mit in date intigriert: http://www.binpress.com/app/demo/app/85


        $register = new Zend_Form_Element_Checkbox('register', array(
            'label' => 'Mit Anmeldung?'
        ));
        $this->addElement($register);

        //----------------------------------------------------------------------

        $submit = new Zend_Form_Element_Submit('submit', array(
            'label' => 'Speichern')
        );
        $submit->setOrder('99');
        $this->addElement($submit);
    }

    public function addRoleSelect($rolesArr) {
        $rolesonlyArr = array();
        $roleSelect = new Zend_Form_Element_MultiCheckbox('role', array(
            'label' => 'Für welche Nutzergruppen soll der Termin sichtbar sein'
        ));

        foreach ($rolesArr as  $value) {
            $roleSelect->addMultiOption($value['id'], $value['name']);
            $rolesonlyArr[] = $value['id'];
        }
        
        $roleSelect->setValue($rolesonlyArr);
        //$roleSelect->addMultiOptions($rolesArr);
        $this->addElement($roleSelect);
    }

    public function getEnabledRoles() {
        return $this->getElement('role')->getValue();
    }

    public function selectDisabledRolesByArray($roles, $enabledRoles) {
        $enableArr = array();
        foreach ($roles as $value) {
            $enabled = false;
            if (!empty($enabledRoles)) {
                foreach ($enabledRoles as $enaValue) {
                    
                    if ($value['id'] == $enaValue['rolle_id']) {
                        $enabled = true;
                    }
                }
            }

            if (!$enabled) {
                $enableArr[] = $value['id'];
            }
        }

        $this->getElement('role')->setValue($enableArr);
    }

}
