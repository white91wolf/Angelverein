<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of InvokeModule
 *
 * @author FloH
 */
class InlineModule {

    protected $_registredModules = array();

    public static function invoke($module_name) {
        $returnValue = null;
        
        if(!empty($module_name) && isset($this->_registredModules[$module_name])) {
            $returnValue = $this->_registredModules[$module_name]->getAsString();
        }
        
        return $returnValue;
    }

    public static function register($module_name, $obj) {
        if (!empty($module_name) && !empty($obj) &&
                !isset($this->_registredModules[$module_name]) &&
                is_object($obj) && $obj instanceof TagModule) {
            $obj->init();
            $this->_registredModules[$module_name] = $obj;
        }
    }

}
