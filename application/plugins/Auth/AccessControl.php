<?php

class Application_Plugin_Auth_AccessControl extends Zend_Controller_Plugin_Abstract {

    private $_auth = null;
    private $_acl = null;

    public function __construct(Zend_Auth $auth, Zend_Acl $acl) {
        $this->_auth = $auth;
        $this->_acl = $acl;
    }

    public static function getUserName() {
        try {
            if (isset(Zend_Auth::getInstance()->getStorage()->read()->nickname)) {
                $username = Zend_Auth::getInstance()->getStorage()->read()->nickname;
            } else {
                $username = null;
            }
        } catch (Exception $e) {
            $username = null;
        }

        return $username;
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
                $authAdapter = new Application_Plugin_Auth_AuthAdapter();
                $authAdapter->setIdentity($username);
                $authAdapter->setCredential($password);
                $result = $this->_auth->authenticate($authAdapter);

                if (!$result->isValid()) {
                    $messages = $result->getMessages();
                    $message = $messages[0];
                } else {
                    if ($loginForm->getValue('rememberlogin') == 1) {
                        Zend_Session::rememberMe(604800); // 7 Tage Login merken
                    } else {
                        Zend_Session::forgetMe();
                    }

                    $storage = $this->_auth->getStorage();
                    $storage->write($authAdapter->getResultRowObject(null, 'password'));


                    if (($redirect = $request->getParam('redirect_after_login')) == null) {
                        $redirect = 'serie/index';
                    }

                    Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector')->gotoUrl($redirect);
                }

                $registry = Zend_Registry::getInstance();
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
        $module = $request->getModuleName();
        $controller = $module . '_' . $request->getControllerName();


        $action = $request->getActionName();

        if (!$this->_acl->has($controller)) {
            $controller = 'default_index';
        }
        //TODO viel freude beim acl schreiben
        
        if (!$this->_acl->isAllowed($role, $controller, $action)) {
            if ($this->_auth->hasIdentity()) {
                $request->setModuleName('backend');
                $request->setControllerName('error');
                $request->setActionName('erroracl');
            } else {
                $query = http_build_query($request->getQuery());
                $request->setModuleName('default');
                $request->setControllerName('user');
                $request->setActionName('requiredlogin');
                $request->setParam('redirect_after_login', $request->getControllerName() . '/' . $action . '/' . $query);
            }
        }
    }

}
