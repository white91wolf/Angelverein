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
class PageModul_Control {
    protected static $_instance = null;
    protected $_registredModules = array();
    protected $_prefix = '[';
    protected $_suffix = ']';
    protected $_command = ':modul:';
    
    private function __construct() {
        
    }
    
    private static function _init() {
        if(self::$_instance == null) {
            self::$_instance = new PageModul_Control();
        }
        
        return self::$_instance;
    }
    
    public static function invoke($module_name) {
        self::_init();
        return self::$_instance->_invoke($module_name);
    }
    
    public static function invokeTags($str) {
        self::_init();
        return self::$_instance->_invokeTags($str);
    }
    
    public static function register($module_name, $obj) {
        self::_init();
        return self::$_instance->_register($module_name, $obj);
    }

    private function _invoke($module_name) {
        $returnValue = null;

        if (!empty($module_name) && isset($this->_registredModules[$module_name])) {
            $returnValue = $this->_registredModules[$module_name]->getAsString();
        }

        return $returnValue;
    }

    private function _invokeTags($str) {
        $tags = $this->_searchTags($str);
        $searchPattern = '/^' . $this->_command . '(.{3,20})$/';
        
        foreach($tags as $tag) {
            if(preg_match($searchPattern, $tag)) {
                $modul_name = substr($tag, strlen($this->_command));
                $replace_pattern = $this->_prefix . $this->_command . $modul_name . $this->_suffix;
                $replace_with = $this->_invoke($modul_name);
                
                if(is_string($replace_with)) {
                    $str = str_replace($replace_pattern, $replace_with, $str);
                }
            }
        }
        
        return $str;
    }

    private function _register($modul_name, $obj) {
        if (!empty($modul_name) && strlen($modul_name) > 2 && !empty($obj) &&
                !isset($this->_registredModules[$modul_name]) &&
                is_object($obj) && $obj instanceof PageModul_Modul) {
            $obj->init();
            $this->_registredModules[$modul_name] = $obj;
        }
    }

    private function _searchTags($str) {
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
