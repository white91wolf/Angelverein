<?php

class Application_Plugin_Auth_AuthAdapter extends Zend_Auth_Adapter_DbTable {
    protected $_roleTable = 'rolle';
    protected $_roleTableRoleName = 'name';
    protected $_userTableForeign = 'rolle_id';

    public function __construct() {
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        parent::__construct($dbAdapter);
        
        $this->setupTable();
    }

    private function setupTable() {
        $this->setTableName('user');
        $this->setIdentityColumn('username');
        $this->setCredentialColumn('password');
        $this->setCredentialTreatment('SHA1(?)');
    }

    protected function _authenticateCreateSelect() {
        $select = parent::_authenticateCreateSelect();

        $select->join(
            array('r' => $this->_roleTable),
            'r.id = ' . $this->_tableName . '.' . $this->_userTableForeign,
            array('role' => $this->_roleTableRoleName)
        );
        
        return $select;
    }
}
