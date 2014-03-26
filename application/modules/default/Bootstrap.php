<?php

class Default_Bootstrap extends Zend_Application_Module_Bootstrap {

    private $mainBoot = null;
    private $view = null;

    public function _initAutoload() {
        $this->mainBoot = $this->getApplication();
        $this->view = $this->mainBoot->getResource('view');
        $this->mainBoot->bootstrap('Db');
        $this->mainBoot->bootstrap('FrontController');
    }

}
