<?php

class Application_Plugin_Auth_AuthAdapter extends Zend_Auth_Adapter_DbTable {
    protected $_roleTable;
    protected $_roleTableRoleName;
    protected $_userTableForeign;
    protected $_additionalWhere;

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
        
        if(isset($arr['additionWhere'])) {
            $this->_additionalWhere = $arr['additionWhere'];
        }
    }

    protected function _authenticateCreateSelect() {
        $select = clone $this->getDbSelect();

        $select->from(
                $this->_tableName,
                array(
                    'username' => $this->_identityColumn,
                    '*'
                )
            )->where(
                    $this->_zendDb->quoteIdentifier($this->_identityColumn, true) . '= ?', 
                    $this->_identity
            );
        
        if(!empty($this->_additionalWhere)) {
            $select->where($this->_additionalWhere);
        }
                
        $select->join(
                array(
                    'r' => $this->_roleTable
                ),
                'r.id = ' . $this->_tableName . '.' . $this->_userTableForeign,
                array(
                    'role' => $this->_roleTableRoleName
                )
        );
        
        return $select;
    }
    
    // Source: https://github.com/rumeau/zf1-authadapter-dbtablebcrypt/blob/master/Jr/Auth/Adapter/DbTableBcrypt.php
    protected function _authenticateValidateResult($resultIdentity) {
        $bcrypt = new Zend2_Crypt_Password_Bcrypt();
 
        if(!$bcrypt->verify($this->_credential, $resultIdentity[$this->_credentialColumn])) {
            $this->_authenticateResultInfo['code'] = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID; 
            $this->_authenticateResultInfo['messages'][] = 'Supplied credential is invalid.';
            return $this->_authenticateCreateAuthResult();
        }
        
        unset($resultIdentity[$this->_credentialColumn]);
    	$this->_resultRow = $resultIdentity;
    
    	$this->_authenticateResultInfo['code'] = Zend_Auth_Result::SUCCESS;
    	$this->_authenticateResultInfo['messages'][] = 'Authentication successful.';
    	return $this->_authenticateCreateAuthResult();
    }
}
