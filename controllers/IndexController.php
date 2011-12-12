<?php

class Commons_IndexController extends Omeka_Controller_Action
{

    public $_modelClass = 'CommonsRecord';
    
    
    
    public function browseAction()
    {
        $cRecords = $this->getTable()->findAll();
        $this->view->assign('commons_records', $cRecords);
    }



}