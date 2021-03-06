<?php

class Application_Model_Forms_FanglisteForm extends Zend_Form {

    protected $gewaesserArray;
    protected $fishTypeArray;
    protected $fishTypeSelectBox;
    protected $counter = 0;

    public function __construct($fishArray, $gewaesser) {
        $this->fishTypeArray = $fishArray;
        $this->gewaesserArray = $gewaesser;

        parent::__construct();
    }

    private function genFishTypeSelectBox($fishArr) {
        $ffish = new Zend_Form_Element_Select('fishType[]', array(
            'label' => 'Fischart',
            'isArray' => true,
            'required' => true)
        );
        
        foreach ($fishArr as $value) {
            $ffish->addMultiOption($value['id'], $value['name']);
        }

        return $ffish;
    }

    public function getGewaesserSelectBox($gewaesserArr) {
        $dropdown = new Zend_Form_Element_Select('gewaesser', array(
            'label' => 'Gewässer',
            'required' => true)
        );

        foreach ($gewaesserArr as $key => $value) {
            $dropdown->addMultiOption($value['id'], $value['name']);
        }


        return $dropdown;
    }

    public function init() {
        $this->setMethod('post');
        $this->setName("catchListForm");
        $this->fishTypeSelectBox = $this->genFishTypeSelectBox($this->fishTypeArray);

        $this->addElement($this->getGewaesserSelectBox($this->gewaesserArray));
        $this->addElement($this->getDatePicker());
        $this->addElement($this->getCounterHiddenField());
        $this->addElement($this->getSubmit());
    }

    private function getCounterHiddenField() {
        $hidden = new Zend_Form_Element_Hidden('group_counter');
        $hidden->setDecorators(array('ViewHelper'));
        // TODO mit counter + 1 eher unschoen
        $hidden->setValue($this->counter + 1);

        return $hidden;
    }

    public function addFishFormElements() {
        $group = array(
            'fishType' => $this->fishTypeSelectBox,
            'fishCount' => $this->getCountFishesTextBox(),
            'fishWeight' => $this->getFishWeightTextBox()
        );

        // change ID
        foreach ($group as $element) {
            $this->setIdOfElement($element);
        }
        ++$this->counter;

        $this->addElement($group['fishType']);
        $this->addElement($group['fishCount']);
        $this->addElement($group['fishWeight']);
    }

    private function setIdOfElement(Zend_Form_Element $element) {
        $element->setAttrib('id', 'element_' . $this->counter . '_' . $element->getId());

        return $element;
    }

    private function getSubmit() {
        $submit = new Zend_Form_Element_Submit('submit', array(
            'label' => 'Speichern')
        );
        $submit->setOrder(99);

        return $submit;
    }

    private function getDatePicker() {
        $date = new ZendX_JQuery_Form_Element_DatePicker('date', array(
            'label' => 'Datum',
            'jQueryParams' => array('dateFormat' => 'dd.mm.yy'),
            'required' => true
        ));

        $date->addFilter('StringTrim');
        $date->addFilter('StripTags');

        return $date;
    }

    public function getFishTypeSelectBox() {
        return $this->setIdOfElement($this->fishTypeSelectBox);
    }

    public function getFishWeightTextBox() {
        $fweight = new Zend_Form_Element_Text('weight[]', array(
            'label' => 'Ø Gewicht pro Fisch',
            'isArray' => true,
            'required' => true)
        );
        //TODO kommaaaaazahlen maybe
        //$fweight->addValidator(new Zend_Validate_Int());

        return $this->setIdOfElement($fweight);
    }

    public function getCountFishesTextBox() {
        $fcount = new Zend_Form_Element_Text('count_fishes[]', array(
            'label' => 'Anzahl',
            'isArray' => true,
            'required' => true)
        );

        //$fcount->addValidator(new Zend_Validate_Int());

        return $this->setIdOfElement($fcount);
    }

    public function setCounter($value) {
        $value_ = (int) $value;

        if ($value_ < 0) {
            $value_ = 0;
        }

        $this->counter = $value_;
    }

    public function getCounter() {
        return $this->counter;
    }

}
