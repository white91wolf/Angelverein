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
	
	public function _initTitle() {
		$this->view->headTitle('Serienverwaltung');
		$this->view->headTitle()->setSeparator(' | ');
	}
	
	public function _initNavigation() {
		/**
		 * Get ACL
		 */
		$front = Zend_Controller_Front::getInstance();
		$acl = $this->mainBoot->getResource('auth');
		
		
		/**
		 * Default Site Navigation
		 */#
		$user_pages = array(
			array(
				'label' => 'Login',
				'controller' => 'user',
				'modul' => 'default',
				'action' => 'login',
				'resource' => 'user.auth',
				'class' => 'login_icon icon'
			),
			array(
				'label' => 'Registrieren',
				'controller' => 'user',
				'modul' => 'default',
				'action' => 'register',
				'resource' => 'user.auth',
				'class' => 'register_icon icon'
			),
			array(
				'label' => 'Logout',
				'controller' => 'user',
				'modul' => 'default',
				'action' => 'logout',
				'resource' => 'user.control',
				'class' => 'logout_icon icon'
			)	
		);
		$main_pages = array(
			
			array(
				'label' => 'Home',
				'controller' => 'index',
				'modul' => 'default',
				'action' => 'index',
				'class' => 'home_icon icon',
				'title' => 'Startseite'
			),
			array(
				'label' => 'Meine Serien',
				'controller' => 'user',
				'modul' => 'default',
				'action' => 'userseries',
				'resource' => 'user.userseries',
				'class' => 'myseries_icon icon',
				'title' => 'Managment deiner Serien'
			),
			array(
				'label' => 'Serienliste',
				'controller' => 'serie',
				'modul' => 'default',
				'action' => 'index',
				'class' => 'serieslist_icon icon',
				'title' => 'Liste aller Serien'
			),
			array(
				'label' => 'Mitglieder',
				'controller' => 'user',
				'modul' => 'default',
				'action' => 'userlist',
				'resource' => 'user.control',
				'class' => 'users_icon icon',
				'title' => 'Die Liste aller Mitglieder'
			),
			array(
				'label' => 'Einstellungen',
				'controller' => 'user',
				'modul' => 'default',
				'action' => 'control',
				'resource' => 'user.control',
				'class' => 'settings_icon icon',
				'title' => 'Liste veröffentlichen, Passwort, Bild oder Email ändern'
			),
			array(
				'label' => 'FAQ',
				'controller' => 'index',
				'modul' => 'default',
				'action' => 'faq',
				'class' => 'faq_icon icon',
				'title' => 'Frequently Asked Questions'
			)
		);
		
		/**
		 * Setup ACL
		 */
		$acl->add(new Zend_Acl_Resource('user.userseries'));
		$acl->add(new Zend_Acl_Resource('user.control'));
		$acl->add(new Zend_Acl_Resource('user.auth'));
		
		$acl->allow('user', 'user.userseries');
		$acl->allow('user', 'user.control');
		$acl->allow('admin', null);
		$acl->deny(null, 'user.auth');
		$acl->allow('guest', 'user.auth');
		 
		/**
		 * Hinzufügen der Navigation
		 */
		$main_nav = new Zend_Navigation($main_pages);
		$user_nav = new Zend_Navigation($user_pages);
	
		$this->view->main_menu = $main_nav;
		$this->view->user_menu = $user_nav;
		
		$auth = Zend_Auth::getInstance();

		
		$currentRole = $auth->getStorage()->read() == null ? 'guest' : $auth->getStorage()->read()->role;
		$this->view->navigation()->setAcl($acl)->setRole($currentRole);
	}
}

