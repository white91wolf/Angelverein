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
    protected $_prefix = '[';
    protected $_suffix = ']';

    public static function invoke($module_name) {
        $returnValue = null;

        if (!empty($module_name) && isset($this->_registredModules[$module_name])) {
            $returnValue = $this->_registredModules[$module_name]->getAsString();
        }

        return $returnValue;
    }

    public static function invokeInString($module_name, $str) {
        
    }

    public static function register($module_name, $obj) {
        if (!empty($module_name) && !empty($obj) &&
                !isset($this->_registredModules[$module_name]) &&
                is_object($obj) && $obj instanceof TagModule) {
            $obj->init();
            $this->_registredModules[$module_name] = $obj;
        }
    }

    private static function searchTags($str) {
        $toLoad = array();
        $tmp = '';
        
        for ($i = 0, $check = 0, $founds = 0; $i < strlen($str); $i++) {
            if ($str[$i] == $this->_prefix) {
                ++$check;
            }
            
            if ($str[$i] == $this->_suffix) {
                --$check;
                
                $toLoad[$founds++] = $tmp;
                $tmp = '';
            }

            if ($check > 0 && $str[$i] != $this->_prefix && $str[$i] != $this->_suffix) {
                $tmp .= $str[$i];
            }
        }

        return $toLoad;
    }
}
