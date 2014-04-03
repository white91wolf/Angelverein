<?php

class Application_Model_Forms_FanglisteForm extends Zend_Form {
    protected $dummy;
    
    public function __construct($fishArray) {
        parent::__construct();
        $this->genDummySelectBox($fishArray);
    }
    
    private function genDummySelectBox($fishArr) {
        $ffish = new Zend_Form_Element_Select('dummy', array(
            'label' => 'Fischart',
            'required' => true)    
        );
        foreach ($fishArr as $key => $value){
            $ffish->addMultiOption($value['id'], $value['name']);
        }
        
        $this->dummy = $ffish;
    }

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
    
    private function getSelectBox($nameValue) {
        $result = $this->dummy;
        $result->setName($nameValue);
        
        return $result;
    }

    public function addFishFormElements($nameValue){
        $this->addElement($this->getSelectBox($nameValue.'_fishtypebox'));
        
        $fcount = new Zend_Form_Element_Text($nameValue.'_countinput', array(
                'label' => 'Anzahl',
                'required' => true)
        );
        
        $fcount->addValidator(new Zend_Validate_Int());
        $this->addElement($fcount);
        
        
        $fweight = new Zend_Form_Element_Text($nameValue.'_weightinput', array(
            'label' => 'Gewicht',
            'required' => true)        
        );
        //TODO kommaaaaazahlen maybe
        $fweight->addValidator(new Zend_Validate_Int());
        $this->addElement($fweight);
    }

}
