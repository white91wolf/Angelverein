<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AclReader
 *
 * @author FloH
 */
class Application_Plugin_Auth_AclReader extends Zend_Acl {
    protected $config = null;

    /**
     * checks if the file $iniFile is exists, otherwise the constructer
     * will throw an exception
     * 
     * @param type $iniFile Path to the access control list
     * @throws Exception
     */
    public function __construct($iniFile) {
        if (!file_exists($iniFile)) {
            throw new Exception('acl config path: file not found! Given file was: ' . $iniFile);
        }

        $this->config = new Zend_Config_Ini($iniFile);
        $this->init();
    }

    /**
     * initializt the object with the roles, privileges and resources which are
     * descriped in the $iniFile
     */
    public function init() {
        $config_array = $this->config->toArray();
        $prev_role = null;

        foreach ($config_array as $role => $permissions) {
            $prev_role = $this->addUserRole($role, $prev_role);
            
            if(empty($permissions) || !is_array($permissions)) {
                continue;
            }
            
            foreach($permissions as $controller => $actionList) {
                $controller = $this->addController($controller);
                $priviliges = $this->getPriviliges($actionList);
                
                $this->allow($role, $controller, $priviliges);
            } 
        }
    }

    /**
     * adds a resource; specifically a given controller 
     * 
     * @param type $controller
     * @return \Zend_Acl_Resource
     */
    private function addController($controller) {
        $controllerObj = new Zend_Acl_Resource($controller);

        if (!$this->has($controller)) {
            $this->addResource($controllerObj);
        }

        return $controllerObj;
    }

    /**
     * adds user role
     * 
     * @param type $role
     * @param Zend_Acl_Role $parent
     * @return \Zend_Acl_Role
     */
    private function addUserRole($role, $parent = null) {
        $roleObj = new Zend_Acl_Role($role);

        if (!$this->hasRole($role)) {
            if (!empty($parent) || $parent instanceof Zend_Acl_Role || $this->hasRole($parent)) {
                $this->addRole($roleObj, $parent);
            } else {
                $this->addRole($roleObj);
            }
        }

        return $roleObj;
    }
    
    /**
     * explodes the $actionList parameter
     * if one value equals all, null is returned, otherwise the array with
     * priviliges is returned
     * 
     * @param type $actionList
     * @return null
     */
    private function getPriviliges($actionList) {
        $arr = $this->getActionlistAsArray($actionList);
        
        /**
         * if array_search found the value "all" in arr,
         * than set $arr to null, because of null means
         * "all priviliges granted"
         */
        if(array_search("all", $arr) !== false) {
            $arr = null;
        }
        
        return $arr;
    }

    private function getActionlistAsArray($actionList) {
        $actions = explode(',', $actionList);
        array_walk($actions, array($this, 'trimArrayValues'));

        return $actions;
    }

    private function trimArrayValues(&$value) {
        $value = trim($value);
    }
}
