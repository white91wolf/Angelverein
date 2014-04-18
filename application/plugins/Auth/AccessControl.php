<?php

class Application_Plugin_Auth_AccessControl extends Zend_Controller_Plugin_Abstract {

    private $_auth = null;
    private $_acl = null;

    public function __construct(Zend_Auth $auth, Zend_Acl $acl) {
        $this->_auth = $auth;
        $this->_acl = $acl;
    }

    // TODO rewrite!
    public static function getUserName() {
        try {
            if (isset(Zend_Auth::getInstance()->getStorage()->read()->username)) {
                $username = Zend_Auth::getInstance()->getStorage()->read()->username;
            } else {
                $username = null;
            }
        } catch (Exception $e) {
            $username = null;
        }

        return $username;
    }
    /* //Möglichkeit dynamisches read zu machen !
    public static function getUser() {
       die(var_dump(Zend_Auth::getInstance()->getStorage()->read()));
    }
*/
    
    public static function getUserRoleID() {
        try {
            if (isset(Zend_Auth::getInstance()->getStorage()->read()->rolle_id)) {
                $roleId = Zend_Auth::getInstance()->getStorage()->read()->rolle_id;
            } else {
                $roleId = null;
            }
        } catch (Exception $e) {
            $roleId = null;
        }

        return $roleId;
    }
    
    public static function getUserID() {
        try {
            if (isset(Zend_Auth::getInstance()->getStorage()->read()->id)) {
                $userid = Zend_Auth::getInstance()->getStorage()->read()->id;
            } else {
                $userid = null;
            }
        } catch (Exception $e) {
            $userid = null;
        }

        return $userid;
    }

    public static function getUserRole() {
        try {
            if (isset(Zend_Auth::getInstance()->getStorage()->read()->role)) {
                $role = Zend_Auth::getInstance()->getStorage()->read()->role;
            } else {
                $role = null;
            }
        } catch (Exception $e) {
            $role = null;
        }

        return $role;
    }

    public function routeStartup(Zend_Controller_Request_Abstract $request) {
        $loginForm = new Application_Model_Forms_UserLoginForm();

        if (!$this->_auth->hasIdentity() && $loginForm->isValid($_POST)) {
            $filter = new Zend_Filter_StripTags();
            $username = $filter->filter($loginForm->getValue('login_user'));
            $password = $filter->filter($loginForm->getValue('login_password'));

            if (!(empty($username) || empty($password))) {
                $authAdapter = new Application_Plugin_Auth_AuthAdapter(
                    Zend_Registry::get('appConfig')->toArray()["permission"]["acl"]
                );
                
                $authAdapter->setIdentity($username);
                $authAdapter->setCredential($password);
                $result = $this->_auth->authenticate($authAdapter);

                if (!$result->isValid()) {
                    $messages = $result->getMessages();
                    $message = $messages[0];
                } else {
                    if ($loginForm->getValue('rememberlogin') == 1) {
                        Zend_Session::rememberMe(Zend_Registry::get('appConfig')->general->session->lifetime); // 7 Tage Login merken
                    } else {
                        Zend_Session::forgetMe();
                    }

                    $storage = $this->_auth->getStorage();
                    $storage->write($authAdapter->getResultRowObject(null, 'password'));


                    if (($redirect = $request->getParam('redirect')) == null) {
                        $redirect = Zend_Registry::get('appConfig')->permission->redirect->afterlogin;
                    }

                    Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector')->gotoUrl($redirect);
                }
            }
        }
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        //var_dump($this->_auth->getIdentity());die();
        if ($this->_auth->hasIdentity() && is_object($this->_auth->getIdentity())) {
            $role = $this->_auth->getIdentity()->role;
        } else {
            $role = 'guest';
        }
//die($role);  //rollencheck
        $module = $request->getModuleName();
        $controller = strtolower($module . '_' . $request->getControllerName());
        $action = strtolower($request->getActionName());

        if (!$this->_acl->has($controller) || !$this->_acl->isAllowed($role, $controller, $action)) {
            if ($this->_auth->hasIdentity()) {
                $this->isNotAllowed($request);
            } else {
                $this->isNotLoggedIn($request);
                
                $query = http_build_query($request->getQuery());
                $request->setParam('redirect', $request->getControllerName() . '/' . $action . '/' . $query);
            }
        }
    }
    
    private function getParts($path) {
        $parts = explode('/', $path);
        
        if(!is_array($parts) || count($parts) < 3) {
            throw new Exception('missconfigured config.ini');
        }
        
        return $parts;
    }

    private function isNotAllowed($request) {
        $parts = $this->getParts(Zend_Registry::get('appConfig')->permission->path->isNotAllowed);
        $this->setUpRequest($request, $parts);
    }

    private function isNotLoggedIn($request) {
        $parts = $this->getParts(Zend_Registry::get('appConfig')->permission->path->istNotLoggedIn);
        $this->setUpRequest($request, $parts);
    }

    private function setUpRequest($request, $parts) {
        $request->setModuleName($parts[0]);
        $request->setControllerName($parts[1]);
        $request->setActionName($parts[2]);
    }
}
