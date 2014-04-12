<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Zend_View_Helper_AppendResource
 *
 * @author FloH
 */
class My_View_Helper_AppendResource extends Zend_View_Helper_Abstract {
    public function appendResource($resource) {
        $this->view->headScript()->appendFile($this->view->baseUrl() . $resource);
    }
}
