<?php

class Application_Model_Forms_FanglisteForm extends Zend_Form {
    protected $counter;


    public function init(){
        $this->counter = 0;
        $this->setMethod('post');

        $date = new ZendX_JQuery_Form_Element_DatePicker('date', array(
            'label' => 'Datum der Tätigkeit',
            'required' => true
        ));
        
        $date->addFilter('StringTrim');
        $date->addFilter('StripTags');
        $this->addElement($date);

        //----------------------------------------------------------------------
        
        $submit = new Zend_Form_Element_Submit('submit', array(
            'label' => 'Speichern')
        );
        $submit->setOrder(99);

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
    
    public function addFishFormElements($fishArr){
        $ffish = new Zend_Form_Element_Select("ffisch$this->counter", array(
            'label' => 'Fischart',
            'required' => true)    
        );
        foreach ($fishArr as $key => $value){
            $ffish->addMultiOption($value['id'], $value['name']);
        }
        $this->addElement($ffish);
        
        $fcount = new Zend_Form_Element_Text("fcount$this->counter", array(
                'label' => 'Anzahl',
                'required' => true)
        );
        $fcount->addValidator(new Zend_Validate_Int());
        $this->addElement($fcount);
        
        
        $fweight = new Zend_Form_Element_Text("fweight$this->counter", array(
            'label' => 'Gewicht',
            'required' => true)        
        );
        $fweight->addValidator(new Zend_Validate_Int());
        $this->addElement($fweight);
        
        
    }

}
