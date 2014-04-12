<?php

class Application_Plugin_Auth_AuthAdapter extends Zend_Auth_Adapter_DbTable {
    protected $_roleTable = 'rolle';
    protected $_roleTableRoleName = 'name';
    protected $_userTableForeign = 'rolle_id';

    public function __construct($arr = array()) {
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        parent::__construct($dbAdapter);
        
        if(empty($arr) || !is_array($arr)) {
            throw new Exception('AclAuthAdapter: the given parameter \'arr\' is not an array or is null');
        }
        
        $this->setTableName($arr['userTable']);
        $this->setIdentityColumn($arr['identityColumn']);
        $this->setCredentialColumn($arr['credentialColumn']);
        $this->setCredentialTreatment($arr['credentialTreatment']);
        
        $this->_roleTable = $arr['roleTable'];
        $this->_roleTableRoleName = $arr['roleNameColumn'];
        $this->_userTableForeign = $arr['userRoleIdColumn'];
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
