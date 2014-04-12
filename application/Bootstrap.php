<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected $_appNamespace = 'Application';

    protected function _initAutoload() {
        $this->bootstrap('FrontController');
        $front = $this->getResource('frontController');
        $front->setParam('useDefaultControllerAlways', true);
    }
    
    protected function _initConfig() {
        $config = new Zend_Config_Ini(APPLICATION_PATH."/configs/config.ini");
        Zend_Registry::set('appConfig', $config);
    }

    protected function _initDoctype() {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('HTML5');
        $view->setEncoding('UTF-8');
        $view->addHelperPath(dirname(__FILE__) . '/views/helpers', 'My_View_Helper');
    }

    protected function _initHeader() {
        header('Content-Type: text/html; charset=UTF-8');
    }

    protected function _initLanguage() {
        $translator = new Zend_Translate(
                array(
            'adapter' => 'array',
            'content' => APPLICATION_PATH . '/resources/languages',
            'locale' => 'de_DE',
            'scan' => Zend_Translate::LOCALE_DIRECTORY
                )
        );
        Zend_Validate_Abstract::setDefaultTranslator($translator);
    }

    protected function _initAuth() {
        $this->bootstrap('frontController');
        $auth = Zend_Auth::getInstance();
        //$acl = new Application_Plugin_Auth_Acl();
        $acl = new Application_Plugin_Auth_AclReader(Zend_Registry::get('appConfig')->permission->path->aclConfig);
        $this->getResource('frontController')->registerPlugin(new Application_Plugin_Auth_AccessControl($auth, $acl))->setParam('auth', $auth);

        return $acl;
    }

    protected function _initJQuery() {
        $this->bootstrap('layout');
        $view = $this->getResource('layout')->getView();
        $view->addHelperPath('ZendX/JQuery/View/Helper/', 'ZendX_JQuery_View_Helper');
        ZendX_JQuery::enableView($view);
        $view->jQuery()->enable()
                ->setVersion('1.10.2')
                ->setUiVersion('1.10.3')
                ->uienable();
        //$view->jQuery()->addStylesheet($view->baseUrl().'');
    }
}
