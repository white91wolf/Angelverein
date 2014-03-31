<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PageNewsModule
 *
 * @author FloH
 */
class PageModul_PageNews implements PageModul_Modul {
    protected $_contentTable;

    public function init() {
        $this->_contentTable = new Application_Model_DbTable_ContentTable();
    }
    
    public function getAsString() {
        // TODO aus config die type id fuer news holen
        $this->_contentTable->getAllEntriesByTypeId(1);
    }
}
